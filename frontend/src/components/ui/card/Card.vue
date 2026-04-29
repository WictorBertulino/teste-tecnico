<script setup lang="ts">
import { cn } from '@/lib/utils'

defineProps<{ class?: string }>()
</script>

<template>
  <div
    :class="
      cn(
        'rounded-2xl border border-[color:var(--border)] bg-[color:var(--card)] text-[color:var(--card-foreground)] shadow-[0_20px_60px_rgba(15,23,42,0.08)]',
        $props.class,
      )
    "
  >
    <slot />
  </div>
</template>
