<script setup lang="ts">
import { cva } from 'class-variance-authority'
import { computed } from 'vue'
import { cn } from '@/lib/utils'

const badgeVariants = cva(
  'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors',
  {
    variants: {
      variant: {
        default: 'border-transparent bg-[color:var(--primary)] text-[color:var(--primary-foreground)]',
        secondary: 'border-transparent bg-[color:var(--secondary)] text-[color:var(--secondary-foreground)]',
        outline: 'border-[color:var(--border)] text-[color:var(--foreground)]',
        success: 'border-transparent bg-[#dcfce7] text-[#166534]',
        danger: 'border-transparent bg-[#fee2e2] text-[#991b1b]',
        warning: 'border-transparent bg-[#fef3c7] text-[#92400e]',
      },
    },
    defaultVariants: {
      variant: 'default',
    },
  },
)

interface Props {
  variant?: 'default' | 'secondary' | 'outline' | 'success' | 'danger' | 'warning'
  class?: string
}

const props = defineProps<Props>()
const classes = computed(() => cn(badgeVariants({ variant: props.variant }), props.class))
</script>

<template>
  <span :class="classes">
    <slot />
  </span>
</template>
