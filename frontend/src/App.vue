<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import {
  AlertTriangle,
  Box,
  ClipboardList,
  LoaderCircle,
  Pencil,
  Plus,
  RefreshCcw,
  Search,
  ShieldCheck,
  Trash2,
} from 'lucide-vue-next'
import Badge from '@/components/ui/badge/Badge.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Separator from '@/components/ui/separator/Separator.vue'
import Switch from '@/components/ui/switch/Switch.vue'
import { createProduct, deleteProduct, fetchProducts, updateProduct } from '@/services/product-service'
import type { Product, ProductFormData } from '@/types/product'

type ActiveFilter = '' | 'true' | 'false'

const defaultForm = (): ProductFormData => ({
  nome: '',
  sku: '',
  preco: 0,
  estoque_atual: 0,
  estoque_minimo: 0,
  ativo: true,
})

const products = ref<Product[]>([])
const form = ref<ProductFormData>(defaultForm())
const formattedPrice = ref('0,00')
const selectedProductId = ref<number | null>(null)
const isLoading = ref(false)
const isSaving = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const search = ref('')
const activeFilter = ref<ActiveFilter>('')
const currentPage = ref(1)
const total = ref(0)
const lastPage = ref(1)

const lowStockCount = computed(
  () => products.value.filter((product) => product.estoque_atual <= product.estoque_minimo).length,
)
const activeCount = computed(() => products.value.filter((product) => product.ativo).length)
const totalInventoryValue = computed(() =>
  products.value.reduce((sum, product) => sum + product.preco * product.estoque_atual, 0),
)
const isEditing = computed(() => selectedProductId.value !== null)
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000/api'

const currencyFormatter = new Intl.NumberFormat('pt-BR', {
  style: 'currency',
  currency: 'BRL',
})

const dateFormatter = new Intl.DateTimeFormat('pt-BR', {
  dateStyle: 'short',
  timeStyle: 'short',
})

async function loadProducts(page = 1) {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetchProducts({
      page,
      per_page: 10,
      search: search.value || undefined,
      ativo: activeFilter.value,
    })

    products.value = response.data
    currentPage.value = response.meta.current_page
    lastPage.value = response.meta.last_page
    total.value = response.meta.total
  } catch (error) {
    errorMessage.value = 'Nao foi possivel carregar os produtos.'
    console.error(error)
  } finally {
    isLoading.value = false
  }
}

const debouncedReload = useDebounceFn(() => loadProducts(1), 350)

watch(search, debouncedReload)
watch(activeFilter, () => loadProducts(1))

function resetForm() {
  form.value = defaultForm()
  formattedPrice.value = formatPriceInput(form.value.preco)
  selectedProductId.value = null
}

function populateForm(product: Product) {
  selectedProductId.value = product.id
  form.value = {
    nome: product.nome,
    sku: product.sku,
    preco: product.preco,
    estoque_atual: product.estoque_atual,
    estoque_minimo: product.estoque_minimo,
    ativo: product.ativo,
  }
  formattedPrice.value = formatPriceInput(product.preco)
  successMessage.value = ''
  errorMessage.value = ''
}

function formatPriceInput(value: number): string {
  return value.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}

function parsePriceInput(value: string): number {
  const digits = value.replace(/\D/g, '')

  if (!digits) {
    return 0
  }

  return Number(digits) / 100
}

function handlePriceInput(event: Event) {
  const target = event.target as HTMLInputElement
  const parsedValue = parsePriceInput(target.value)

  form.value.preco = parsedValue
  formattedPrice.value = formatPriceInput(parsedValue)
}

async function submitForm() {
  isSaving.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    if (selectedProductId.value) {
      await updateProduct(selectedProductId.value, form.value)
      successMessage.value = 'Produto atualizado com sucesso.'
    } else {
      await createProduct(form.value)
      successMessage.value = 'Produto criado com sucesso.'
    }

    resetForm()
    await loadProducts(currentPage.value)
  } catch (error: any) {
    errorMessage.value =
      error?.response?.data?.message ?? 'Nao foi possivel salvar o produto. Verifique os campos.'
  } finally {
    isSaving.value = false
  }
}

async function handleDelete(product: Product) {
  const confirmed = window.confirm(`Deseja remover o produto "${product.nome}"?`)

  if (!confirmed) {
    return
  }

  try {
    await deleteProduct(product.id)
    successMessage.value = 'Produto removido com sucesso.'

    if (products.value.length === 1 && currentPage.value > 1) {
      await loadProducts(currentPage.value - 1)
      return
    }

    await loadProducts(currentPage.value)
  } catch (error) {
    errorMessage.value = 'Nao foi possivel remover o produto.'
    console.error(error)
  }
}

function stockVariant(product: Product): 'success' | 'warning' | 'danger' {
  if (product.estoque_atual === 0) return 'danger'
  if (product.estoque_atual <= product.estoque_minimo) return 'warning'
  return 'success'
}

onMounted(() => {
  formattedPrice.value = formatPriceInput(form.value.preco)
  loadProducts()
})
</script>

<template>
  <main class="min-h-screen bg-[color:var(--background)] text-[color:var(--foreground)]">
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-4 py-8 sm:px-6 lg:px-8">
      <div class="overflow-hidden rounded-[32px] border border-white/50 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.16),_transparent_32%),linear-gradient(135deg,_#fff7ed_0%,_#ffffff_45%,_#ecfeff_100%)] p-8 shadow-[0_30px_80px_rgba(14,116,144,0.16)]">
        <div class="grid gap-8 lg:grid-cols-[1.3fr_0.7fr] lg:items-end">
          <div class="space-y-4">
            <Badge variant="secondary" class="bg-white/70 text-slate-700">Teste tecnico em Laravel + Vue</Badge>
            <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
              CRUD de Produtos com API em camadas, SOLID e interface pronta para demonstrar raciocinio tecnico.
            </h1>
            <p class="max-w-2xl text-base text-slate-600 sm:text-lg">
              O fluxo separa backend e frontend, aplica Service Layer com Repository sobre Eloquent e entrega uma UI
              consistente no padrao shadcn.
            </p>
          </div>

          <Card class="border-white/60 bg-white/75 backdrop-blur">
            <CardContent class="grid gap-4 p-6">
              <div class="flex items-center justify-between">
                <span class="text-sm text-[color:var(--muted-foreground)]">API base</span>
                <code class="rounded bg-slate-100 px-2 py-1 text-xs text-slate-700">
                  {{ apiBaseUrl }}
                </code>
              </div>
              <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-[color:var(--border)] bg-white p-4">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Produtos</p>
                  <p class="mt-2 text-3xl font-semibold text-slate-950">{{ total }}</p>
                </div>
                <div class="rounded-xl border border-[color:var(--border)] bg-white p-4">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Ativos</p>
                  <p class="mt-2 text-3xl font-semibold text-slate-950">{{ activeCount }}</p>
                </div>
                <div class="rounded-xl border border-[color:var(--border)] bg-white p-4">
                  <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Estoque critico</p>
                  <p class="mt-2 text-3xl font-semibold text-slate-950">{{ lowStockCount }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      <div class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
        <Card>
          <CardHeader>
            <CardTitle>{{ isEditing ? 'Editar produto' : 'Novo produto' }}</CardTitle>
            <CardDescription>Preencha os campos da entidade Produto seguindo as validacoes da API.</CardDescription>
          </CardHeader>

          <CardContent class="space-y-6">
            <div v-if="errorMessage" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
              {{ errorMessage }}
            </div>

            <div
              v-if="successMessage"
              class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700"
            >
              {{ successMessage }}
            </div>

            <form class="space-y-5" @submit.prevent="submitForm">
              <div class="grid gap-2">
                <Label for="nome">Nome</Label>
                <Input id="nome" v-model="form.nome" placeholder="Ex.: Headset Bluetooth Pro" required />
              </div>

              <div class="grid gap-2">
                <Label for="sku">SKU</Label>
                <Input id="sku" v-model="form.sku" placeholder="Ex.: HEAD-BLU-001" required />
              </div>

              <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                  <Label for="preco">Preco</Label>
                  <Input
                    id="preco"
                    :model-value="formattedPrice"
                    inputmode="decimal"
                    placeholder="0,00"
                    required
                    @input="handlePriceInput"
                  />
                </div>

                <div class="grid gap-2">
                  <Label for="estoque-atual">Estoque atual</Label>
                  <Input id="estoque-atual" v-model.number="form.estoque_atual" type="number" min="0" step="1" required />
                </div>
              </div>

              <div class="grid gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                  <Label for="estoque-minimo">Estoque minimo</Label>
                  <Input id="estoque-minimo" v-model.number="form.estoque_minimo" type="number" min="0" step="1" required />
                </div>

                <div class="grid gap-2">
                  <Label>Status</Label>
                  <div class="flex min-h-10 items-center rounded-md border border-[color:var(--border)] px-3">
                    <Switch v-model="form.ativo">
                      <span class="text-sm text-[color:var(--muted-foreground)]">
                        {{ form.ativo ? 'Produto ativo' : 'Produto inativo' }}
                      </span>
                    </Switch>
                  </div>
                </div>
              </div>

              <Separator />

              <div class="flex flex-col gap-3 sm:flex-row">
                <Button class="flex-1" type="submit" :disabled="isSaving">
                  <LoaderCircle v-if="isSaving" class="h-4 w-4 animate-spin" />
                  <template v-else-if="isEditing">Atualizar produto</template>
                  <template v-else>
                    <Plus class="h-4 w-4" />
                    Cadastrar produto
                  </template>
                </Button>
                <Button type="button" variant="outline" class="flex-1" @click="resetForm">
                  <RefreshCcw class="h-4 w-4" />
                  Limpar
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="gap-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
              <CardTitle>Estoque e catalogo</CardTitle>
              <CardDescription>Busca, paginacao e indicadores de disponibilidade.</CardDescription>
            </div>
            <Button variant="outline" @click="loadProducts(currentPage)">
              <RefreshCcw class="h-4 w-4" />
              Atualizar
            </Button>
          </CardHeader>

          <CardContent class="space-y-5">
            <div class="grid gap-4 lg:grid-cols-[1fr_auto]">
              <div class="relative">
                <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <Input v-model="search" class="pl-9" placeholder="Buscar por nome, SKU ou UUID" />
              </div>

              <div class="flex flex-wrap gap-2">
                <Button :variant="activeFilter === '' ? 'default' : 'outline'" size="sm" @click="activeFilter = ''">Todos</Button>
                <Button :variant="activeFilter === 'true' ? 'default' : 'outline'" size="sm" @click="activeFilter = 'true'">Ativos</Button>
                <Button :variant="activeFilter === 'false' ? 'default' : 'outline'" size="sm" @click="activeFilter = 'false'">Inativos</Button>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
              <Card class="border-dashed shadow-none">
                <CardContent class="flex items-center gap-4 p-5">
                  <div class="rounded-2xl bg-sky-100 p-3 text-sky-700"><Box class="h-5 w-5" /></div>
                  <div>
                    <p class="text-sm text-[color:var(--muted-foreground)]">Valor em estoque</p>
                    <p class="text-xl font-semibold">{{ currencyFormatter.format(totalInventoryValue) }}</p>
                  </div>
                </CardContent>
              </Card>

              <Card class="border-dashed shadow-none">
                <CardContent class="flex items-center gap-4 p-5">
                  <div class="rounded-2xl bg-emerald-100 p-3 text-emerald-700"><ShieldCheck class="h-5 w-5" /></div>
                  <div>
                    <p class="text-sm text-[color:var(--muted-foreground)]">Produtos ativos</p>
                    <p class="text-xl font-semibold">{{ activeCount }}</p>
                  </div>
                </CardContent>
              </Card>

              <Card class="border-dashed shadow-none">
                <CardContent class="flex items-center gap-4 p-5">
                  <div class="rounded-2xl bg-amber-100 p-3 text-amber-700"><AlertTriangle class="h-5 w-5" /></div>
                  <div>
                    <p class="text-sm text-[color:var(--muted-foreground)]">Abaixo do minimo</p>
                    <p class="text-xl font-semibold">{{ lowStockCount }}</p>
                  </div>
                </CardContent>
              </Card>
            </div>

            <div class="overflow-hidden rounded-2xl border border-[color:var(--border)]">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[color:var(--border)] text-sm">
                  <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                      <th class="px-4 py-3 font-medium">Produto</th>
                      <th class="px-4 py-3 font-medium">Preco</th>
                      <th class="px-4 py-3 font-medium">Estoque</th>
                      <th class="px-4 py-3 font-medium">Status</th>
                      <th class="px-4 py-3 font-medium">Atualizado</th>
                      <th class="px-4 py-3 text-right font-medium">Acoes</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-[color:var(--border)] bg-white">
                    <tr v-if="isLoading">
                      <td colspan="6" class="px-4 py-10 text-center text-[color:var(--muted-foreground)]">
                        <span class="inline-flex items-center gap-2">
                          <LoaderCircle class="h-4 w-4 animate-spin" />
                          Carregando produtos...
                        </span>
                      </td>
                    </tr>
                    <tr v-else-if="products.length === 0">
                      <td colspan="6" class="px-4 py-10 text-center text-[color:var(--muted-foreground)]">
                        Nenhum produto encontrado com os filtros atuais.
                      </td>
                    </tr>
                    <tr v-for="product in products" :key="product.id" class="hover:bg-slate-50/80">
                      <td class="px-4 py-4 align-top">
                        <div class="font-medium text-slate-950">{{ product.nome }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ product.sku }}</div>
                        <div class="mt-1 text-xs text-slate-400">{{ product.uuid }}</div>
                      </td>
                      <td class="px-4 py-4 align-top font-medium text-slate-900">{{ currencyFormatter.format(product.preco) }}</td>
                      <td class="px-4 py-4 align-top">
                        <div class="flex flex-col gap-2">
                          <Badge :variant="stockVariant(product)">{{ product.estoque_atual }} em estoque</Badge>
                          <span class="text-xs text-slate-500">Minimo: {{ product.estoque_minimo }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-4 align-top">
                        <Badge :variant="product.ativo ? 'success' : 'outline'">{{ product.ativo ? 'Ativo' : 'Inativo' }}</Badge>
                      </td>
                      <td class="px-4 py-4 align-top text-slate-500">{{ dateFormatter.format(new Date(product.updated_at)) }}</td>
                      <td class="px-4 py-4 align-top">
                        <div class="flex justify-end gap-2">
                          <Button variant="outline" size="icon" @click="populateForm(product)"><Pencil class="h-4 w-4" /></Button>
                          <Button variant="destructive" size="icon" @click="handleDelete(product)"><Trash2 class="h-4 w-4" /></Button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div class="inline-flex items-center gap-2 text-sm text-[color:var(--muted-foreground)]">
                <ClipboardList class="h-4 w-4" />
                Pagina {{ currentPage }} de {{ lastPage }} com {{ total }} registros.
              </div>

              <div class="flex gap-2">
                <Button variant="outline" size="sm" :disabled="currentPage <= 1" @click="loadProducts(currentPage - 1)">Anterior</Button>
                <Button variant="outline" size="sm" :disabled="currentPage >= lastPage" @click="loadProducts(currentPage + 1)">Proxima</Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </section>
  </main>
</template>
