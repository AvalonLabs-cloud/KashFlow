<template>
  <div class="demo-notice">
    <div class="notice-icon">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"  
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="12" cy="12" r="10" />
        <path d="M12 16v-4" />
        <path d="M12 8h.01" />
      </svg>
    </div>

    <div class="notice-content">
      <span class="notice-title">Demo Environment</span>
      <p>
        This application is currently in demo mode.
        No real transactions are being processed at this time.
      </p>
    </div>
  </div>
</template>

<style scoped>
.demo-notice {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  max-width: 480px;
  margin: 16px auto;
  padding: 16px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  color: #166534;
}

.notice-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: #dcfce7;
}

.notice-content {
  display: block;
  min-width: 0;
}

.notice-title {
  display: block;
  margin-bottom: 4px;
  font-size: 14px;
  font-weight: 600;
  color: #166534;
}

.notice-content p {
  margin: 0;
  font-size: 13px;
  line-height: 1.6;
  color: #15803d;
}

@media (max-width: 480px) {
  .demo-notice {
    margin: 12px;
    padding: 14px;
  }
}
</style>
