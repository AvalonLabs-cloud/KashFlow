<template>
  <section class="tips-banner">
    <span class="material-symbols-outlined tip-icon">info</span>
    <div class="tip-content">
      <p class="tip-title">Transfer Security</p>
      <p class="tip-desc">Always verify the recipient's name before completing your transfer to ensure your funds reach the correct destination.</p>
    </div>
  </section>
</template>

<style scoped>
.tips-banner {
  background-color: rgba(114, 251, 189, 0.1);
  padding: 16px;
  border-radius: 12px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.tip-icon {
  color: var(--secondary);
}

.tip-title {
  font-weight: 700;
  font-size: 14px;
  color: var(--on-secondary-fixed);
  margin: 0 0 4px 0;
}

.tip-desc {
  font-size: 12px;
  color: var(--on-secondary-fixed-variant);
  line-height: 1.5;
  margin: 0;
}
</style>
