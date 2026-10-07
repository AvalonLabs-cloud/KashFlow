// ActionButton.vue
<template>
  <div class="action-wrapper group">
    <button
      class="action-btn active-tap"
      :class="{'is-loading': isLoading, 'is-success': status === 'success', 'is-error': status === 'error' }"
      @click="handleClick"
    >
      <!-- <span v-if="isLoading" class="material-symbols-outlined spin static-spinner"></span> -->
      <!-- <span v-else-if="status === 'success'" class="material-symbols-outlined">check</span>
      <span v-else-if="status === 'error'" class="material-symbols-outlined">close</span> -->
      <span class="material-symbols-outlined icon" v-html="props.icon"></span>
    </button>
    <span class="action-label">{{ label }}</span>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue';

const props = defineProps({
  icon: String,
  label: String
});

const isLoading = ref(false);
const status = ref(null);

const handleClick = () => {
  if (isLoading.value) {
    return
}

  isLoading.value = true;
  status.value = null;

};
</script>

<style scoped>
.action-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.action-btn {
  width: 56px;
  height: 56px;
  background-color: var(--surface);
  border-radius: 12px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  border: 1px solid rgba(187, 202, 193, 0.1);
  cursor: pointer;
  transition: all 0.2s;
}



.icon {
  font-size: 15px;
}


.action-label {
  font-size: 11px;
  font-weight: 500;
  color: var(--on-surface-variant);
}

.active-tap:active {
  transform: scale(0.95);
}




</style>
