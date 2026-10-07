<script lang="ts" setup>
import { router } from "@inertiajs/vue3";
import { ChevronLeft } from 'lucide-vue-next';
const emit = defineEmits(['back']);

const navigateToHistory = () => {
  router.get('/history');
}

const helpDeskNavigation = () => {
   router.get('/helpDesk')
}

const props = defineProps([
    'name'
])

const navigateBack = () => {
      if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/main-index');
    }
}
</script>

<template>
  <header class="top-app-bar">
    <div class="header-left">
      <button class="icon-btn active-tap" @click="emit('back') ; navigateBack()">
        <span class="material-symbols-outlined"><ChevronLeft :size="32"/></span>
      </button>
      <h1 class="page-title">{{ props.name }}</h1>
    </div>
    <div class="header-right">
      <button @click="navigateToHistory()" class="icon-btn active-tap">
        <span class="material-symbols-outlined">history</span>
      </button>
      <button @click="helpDeskNavigation()" class="icon-btn active-tap">
        <span class="material-symbols-outlined">help</span>
      </button>
    </div>
  </header>
</template>

<style scoped>
.top-app-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 16px;
  height: 64px;
  width: 100%;
  top: 0;
  z-index: 50;
  background-color: #ffffff;
  transition: all 0.2s;
  box-sizing: border-box;
}

.header-left, .header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.page-title {
  font-family: 'Manrope', sans-serif;
  font-size: 18px;
  font-weight: 700;
  color: var(--on-surface);
  margin: 0;
}

.icon-btn {
  background: none;
  border: none;
  color: #15803d; /* green-700 */
  cursor: pointer;
  display: flex;
  padding: 8px;
  transition: transform 0.2s;
}

.active-tap:active {
  transform: scale(0.95);
}
</style>
