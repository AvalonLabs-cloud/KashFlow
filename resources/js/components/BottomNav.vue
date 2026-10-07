<template>
  <nav class="bottom-nav">
    <a @click="home" class="nav-item active">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">home</span>
      <span class="nav-label">Home</span>
    </a>
    <a @click="profile"  class="nav-item">
      <span class="material-symbols-outlined">person</span>
      <span class="nav-label">Me</span>
    </a>
  </nav>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
const home = () => {
    router.get('/main-index')
}
const profile = () => {
    router.get('/client_profile')
}
</script>

<style scoped>
.bottom-nav {
  position: relative;
  bottom: 0px;
  left: 0;
  width: 100%;
  /* z-index: 50; */
  display: flex;
  justify-content: space-around;
  align-items: center;
  padding: 12px 16px;
  padding-bottom: max(12px, env(safe-area-inset-bottom));
  background-color: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(24px);
  box-shadow: 0 -12px 32px rgba(0, 108, 79, 0.06);
  border-radius: 24px;
  border-top: 0.5px solid rgba(187, 202, 193, 0.15);
  box-sizing: border-box;
  height:100px;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: var(--on-surface-variant);
  text-decoration: none;
  transition: opacity 0.2s;
}

.nav-item:hover:not(.active) {
  opacity: 0.8;
}

.nav-item.active {
  color: var(--primary);
  font-weight: 700;
}

.nav-item.active:active {
  transform: scale(0.95);
}

.nav-label {
  font-size: 10px;
  margin-top: 4px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
</style>
