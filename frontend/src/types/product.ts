export interface Product {
  id: number
  uuid: string
  nome: string
  sku: string
  preco: number
  estoque_atual: number
  estoque_minimo: number
  ativo: boolean
  created_at: string
  updated_at: string
}

export interface ProductFormData {
  nome: string
  sku: string
  preco: number
  estoque_atual: number
  estoque_minimo: number
  ativo: boolean
}

export interface ProductListResponse {
  data: Product[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}
