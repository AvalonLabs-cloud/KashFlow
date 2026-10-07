// Banner.vue
<template>
  <section class="promo-banner">
    <div class="icon-bg">
      <span class="material-symbols-outlined banner-icon" style="font-variation-settings: 'FILL' 1;">campaign</span>
    </div>
    <p class="banner-text">10% Cashback on first Airtime purchase!</p>
    <span class="material-symbols-outlined arrow-icon">chevron_right</span>
  </section>
</template>

<script setup>
</script>

<style scoped>
.promo-banner {
  background-color: rgba(0, 108, 79, 0.05);
  border-radius: 8px;
  padding: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid rgba(0, 108, 79, 0.05);
  cursor: pointer;
}

.icon-bg {
  background-color: rgba(0, 179, 134, 0.2);
  padding: 8px;
  border-radius: 50%;
  display: flex;
}

.banner-icon {
  color: var(--primary);
  font-size: 14px;
}

.banner-text {
  font-size: 12px;
  font-weight: 500;
  color: var(--on-surface-variant);
  margin: 0;
  flex: 1;
}

.arrow-icon {
  color: var(--primary);
  font-size: 14px;
  margin-left: auto;
}
</style>
