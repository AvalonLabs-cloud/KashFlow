// ServiceItem.vue
<template>
  <div class="service-item active-tap">
   <Link :href="linkTo">
      <div class="service-icon-wrap" :style="{ color: iconColor }">
      <span class="material-symbols-outlined">{{ icon }}</span>
    </div>
    <span class="service-label">{{ label }}</span>
</Link>

  </div>
</template>

<script lang="ts" setup>
import { Link } from '@inertiajs/vue3'
defineProps({
  icon: String,
  label: String,
  iconColor: {
    type: String,
    default: 'inherit'
  },
  linkTo: String,

});
</script>

<style scoped>
.service-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

.service-icon-wrap {
  width: 48px;
  height: 48px;
  background-color: var(--surface);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.service-label {
  font-size: 10px;
  font-weight: 500;
  color: var(--on-surface-variant);
  text-align: center;
}

.active-tap:active {
  transform: scale(0.95);
  transition: transform 0.1s;
}
</style>
