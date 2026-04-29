<script setup lang="ts">
import { cn } from '@/lib/utils'

const model = defineModel<string | number>()

defineProps<{ class?: string }>()
</script>

<template>
  <input
    v-model="model"
    v-bind="$attrs"
    :class="
      cn(
        'flex h-10 w-full rounded-md border border-[color:var(--input)] bg-[color:var(--background)] px-3 py-2 text-sm text-[color:var(--foreground)] shadow-sm outline-none transition placeholder:text-[color:var(--muted-foreground)] focus-visible:ring-2 focus-visible:ring-[color:var(--ring)]',
        $props.class,
      )
    "
  />
</template>
