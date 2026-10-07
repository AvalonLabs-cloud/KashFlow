<template>
  <div v-if="accountName" class="recipient-preview" :class="{ 'is-loading': loading }">
    <div class="avatar">
      <span class="material-symbols-outlined">person</span>
    </div>
    <div class="info">
      <p class="name">{{ accountName }}</p>
      <p class="badge">Verified Account Holder</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
defineProps({
  accountName: String,
  loading: Boolean
});
</script>

<style scoped>
.recipient-preview {
  background-color: rgba(117, 253, 160, 0.15); /* primary-container/20 */
  border: 1px solid rgba(0, 106, 53, 0.05);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  transition: opacity 0.3s;
}

.is-loading {
  opacity: 0.5;
  filter: grayscale(1);
}

.avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background-color: var(--primary);
  color: var(--on-primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.name {
  font-family: 'Manrope', sans-serif;
  font-weight: 700;
  color: var(--on-primary-container);
  margin: 0;
}

.badge {
  font-size: 12px;
  color: var(--on-primary-fixed-variant);
  margin: 4px 0 0 0;
}
</style>
