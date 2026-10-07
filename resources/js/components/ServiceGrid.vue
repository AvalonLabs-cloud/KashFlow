// ServiceGrid.vue
<template>
  <section class="service-section">
    <div class="section-header">
      <h2 class="section-title">Services</h2>
      <button class="view-all-btn">View All</button>
    </div>
    <div class="service-grid">
      <ServiceItem icon="airtime" label="airtime" iconColor="#5D3FD3" linkTo="airtime" />
      <ServiceItem icon="data" label="data" iconColor="#FFA500" linkTo="data" />
      <ServiceItem icon="tv" label="TV Sub" iconColor="#E91E63" />
      <ServiceItem icon="sports_esports" label="Betting" iconColor="#4CAF50" />
      <ServiceItem icon="wifi" label="Internet" iconColor="#03A9F4" />
      <ServiceItem icon="school" label="Education" iconColor="#FF5722" />
      <ServiceItem icon="directions_bus" label="Transport" iconColor="#009688" />
      <ServiceItem icon="grid_view" label="More" iconColor="var(--outline)" />
    </div>
  </section>
</template>

<script lang="ts" setup>
import ServiceItem from '../components/ServiceItem.vue';
</script>

<style scoped>
.service-section {
  display: flex;
  flex-direction: column;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--on-surface);
  letter-spacing: -0.025em;
  margin: 0;
}

.view-all-btn {
  font-size: 12px;
  font-weight: 700;
  color: var(--primary);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.service-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  row-gap: 24px;
}
</style>
