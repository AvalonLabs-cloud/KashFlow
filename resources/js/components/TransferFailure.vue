<template>
  <div class="failure-page-wrapper">
    <div class="container">
      <section class="status-section">
        <div class="icon-container">
          <div class="icon-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
              <path d="M18 6L6 18M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
        <h1 class="title">Transfer Failed</h1>
        <p class="error-message">{{ errorMessage || 'Transaction could not be completed.' }}</p>
      </section>

      <div class="amount-display">
        <span class="currency">₦</span>{{ amount }}
      </div>

      <div class="details-card">
        <div class="detail-row">
          <span class="label">Recipient</span>
          <span class="value truncate">{{ recipientName }}</span>
        </div>
        <div class="divider"></div>
        <div class="detail-row">
          <span class="label">Bank</span>
          <span class="value">{{ bankName }}</span>
        </div>
        <div class="divider"></div>
        <div class="detail-row">
          <span class="label">Account</span>
          <span class="value">{{ maskAccount(accountNumber) }}</span>
        </div>
        <div class="divider"></div>
        <div class="detail-row">
          <span class="label">Reference</span>
          <span class="value text-secondary">{{ reference }}</span>
        </div>
        <template v-if="narration">
          <div class="divider"></div>
          <div class="detail-row">
            <span class="label">Narration</span>
            <span class="value">{{ narration }}</span>
          </div>
        </template>
      </div>

      <div class="spacer"></div>

      <footer class="action-section">
        <button @click="onRetry" class="btn-primary">
          Retry
        </button>
        <button @click="onGoHome" class="btn-ghost">
          Back to Home
        </button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  amount: { type: String, required: true },
  recipientName: { type: String, required: true },
  bankName: { type: String, required: true },
  accountNumber: { type: String, required: true },
  narration: { type: String, default: '' },
  reference: { type: String, required: true },
  errorMessage: { type: String, default: 'Network error. Please try again.' },
  onRetry: { type: Function, default: () => {} },
  onGoHome: { type: Function, default: () => {} }
});

const maskAccount = (acc) => {
  if (!acc) return '';
  return `•••• ${acc.slice(-4)}`;
};
</script>

<style scoped>
/* Design System Variables */
:root {
  --primary-green: #00A859;
  --bg-neutral: #F9FAFB;
  --text-main: #1A1A1A;
  --text-muted: #6B7280;
  --error-red: #E53935;
  --error-tint: #FDECEC;
  --white: #FFFFFF;
  --divider: #F2F2F2;
}

.failure-page-wrapper {
  background-color: #F9FAFB;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  font-family: system-ui, -apple-system, sans-serif;
  -webkit-font-smoothing: antialiased;
}

.container {
  width: 100%;
  max-width: 420px;
  padding: 40px 20px 24px 20px;
  display: flex;
  flex-direction: column;
  animation: pageIn 0.5s ease-out;
}

/* Status Section */
.status-section {
  text-align: center;
  margin-bottom: 32px;
}

.icon-container {
  display: flex;
  justify-content: center;
  margin-bottom: 16px;
}

.icon-circle {
  width: 72px;
  height: 72px;
  background-color: #FDECEC;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #E53935;
  animation: iconScale 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.icon-circle svg {
  width: 32px;
  height: 32px;
}

.title {
  font-size: 20px;
  font-weight: 600;
  color: #1A1A1A;
  margin: 0 0 8px 0;
}

.error-message {
  font-size: 14px;
  color: #6B7280;
  line-height: 1.5;
  max-width: 260px;
  margin: 0 auto;
}

/* Amount */
.amount-display {
  text-align: center;
  font-size: 28px;
  font-weight: 700;
  color: #1A1A1A;
  margin-bottom: 24px;
}

.currency {
  font-weight: 500;
  margin-right: 4px;
}

/* Card Styling */
.details-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.detail-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
}

.detail-row:first-child { padding-top: 0; }
.detail-row:last-child { padding-bottom: 0; }

.label {
  font-size: 13px;
  font-weight: 500;
  color: #6B7280;
}

.value {
  font-size: 15px;
  font-weight: 600;
  color: #1A1A1A;
  max-width: 60%;
  text-align: right;
}

.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.divider {
  height: 1px;
  background-color: #F2F2F2;
  width: 100%;
}

.spacer {
  flex-grow: 1;
  min-height: 40px;
}

/* Buttons */
.action-section {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.btn-primary {
  width: 100%;
  height: 52px;
  background-color: #00A859;
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary:active {
  transform: scale(0.98);
  filter: brightness(1.1);
}

.btn-ghost {
  width: 100%;
  height: 48px;
  background: transparent;
  color: #6B7280;
  border: none;
  font-size: 15px;
  font-weight: 500;
  cursor: pointer;
}

/* Animations */
@keyframes pageIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes iconScale {
  from { transform: scale(0.5); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

/* Accessibility: High Contrast Focus */
button:focus-visible {
  outline: 2px solid #00A859;
  outline-offset: 2px;
}
</style>
