<script setup lang="ts">
const model = defineModel<boolean>({ required: true })
defineProps<{ id?: string }>()
</script>

<template>
  <label class="inline-flex cursor-pointer items-center gap-3">
    <button
      :id="id"
      type="button"
      role="switch"
      :aria-checked="model"
      class="relative inline-flex h-6 w-11 items-center rounded-full transition"
      :class="model ? 'bg-[color:var(--primary)]' : 'bg-[color:var(--muted)]'"
      @click="model = !model"
    >
      <span
        class="inline-block h-5 w-5 rounded-full bg-white transition"
        :class="model ? 'translate-x-5' : 'translate-x-1'"
      />
    </button>
    <slot />
  </label>
</template>
