<template>
<!-- <Header :client_name="props.clients_name" /> -->
  <div class="transfer-status-container">
    <header class="header">
      <div class="header-left">
        <button
          class="icon-button"
          aria-label="Go back"
          @click="$emit('back')"
        >
          <span class="material-symbols-outlined">arrow_back</span>
        </button>
        <h2 class="header-title">Transfer Status</h2>
      </div>
      <div class="header-right">
        <button class="icon-button" aria-label="Help">
          <span class="material-symbols-outlined">help</span>
        </button>
        <button class="icon-button" aria-label="Share">
          <span class="material-symbols-outlined">share</span>
        </button>
      </div>
    </header>

    <main class="content">
      <section class="status-summary">
        <div class="status-icon-wrapper">
          <div :class="['status-icon', status]">
            <span class="material-symbols-outlined">
              {{ status === 'success' ? 'check_circle' : 'error' }}
            </span>
          </div>
          <div class="status-glow"></div>
        </div>

        <div class="status-text">
          <h1 class="status-title">{{ statusTitle }}</h1>
          <!-- <p class="status-message">{{ statusMessage }}</p> -->
        </div>

        <div class="amount-display">
          <span class="currency-symbol">₦</span>
          <span class="amount-value">{{ data.amount }}</span>
        </div>
      </section>

      <section class="details-card">
        <h3 class="card-label">Transaction Summary</h3>

        <div class="details-grid">
          <div  v-if="data.type === 'transfer'"  class="detail-row">
            <div class="detail-group">
              <span class="label">Recipient</span>
              <span class="value truncate">{{ data.metadata.recipientname }}</span>
            </div>
            <div class="detail-group align-right">
              <span class="label">Bank</span>
              <span class="value">{{  data.bankCode}}</span>
            </div>
          </div>

          <div  v-if="data.type === 'transfer'"  class="detail-row">
            <div class="detail-group">
              <span class="label">Account Number</span>
              <div class="value-with-action">
                <span class="value">{{data.metadata.accountNumber}}</span>
                <button
                  class="copy-button"
                  @click="copyToClipboard( data.metadata.accountNumber)"
                  title="Copy Account Number"
                >
                  <span class="material-symbols-outlined">content_copy</span>
                </button>
              </div>
            </div>
          </div>

          <div class="detail-divider">
            <div class="detail-row pt-4">
              <div class="detail-group">
                <span class="label">Reference</span>
                <span class="value-mono">{{ data.transaction_reference  }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

  <!-- <BottomNav/> -->
  </div>
</template>

<script lang="ts"  setup>
import { ref} from 'vue';
// import BottomNav from '../BottomNav.vue';
// import Header from '../Header.vue';

 const status = ref('success')

const emit = defineEmits(['back', 'done', 'share']);

const props = defineProps([
    'data',
    'clients_name'
])

const statusTitle = ref('Processing Transactions')

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text);
  // Implementation for a toast notification would go here
  alert('Copied to clipboard');
};
</script>

<style scoped>
/* MODERN SCOPED CSS
  - Based on an 8px grid system
  - Semantic naming convention
  - Mobile-first constraint (420px)
*/

.transfer-status-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  max-width: 420px;
  margin: 0 auto;
  background-color: #fcf9f8;
  color: #1c1b1b;
  font-family: 'Inter', sans-serif;
  position: relative;
  overflow-x: hidden;
}

/* Header Styles */
.header {
  position: sticky;
  top: 0;
  z-index: 10;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 24px;
  background-color: #fcf9f8;
}

.header-left, .header-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-title {
  font-family: 'Manrope', sans-serif;
  font-size: 18px;
  font-weight: 700;
  margin: 0;
}

.icon-button {
  padding: 8px;
  border-radius: 50%;
  border: none;
  background: transparent;
  color: #3d4a3f;
  cursor: pointer;
  transition: background 0.2s ease;
  display: flex;
  align-items: center;
}

.icon-button:hover {
  background-color: #eae7e7;
}

/* Content Area */
.content {
  flex-grow: 1;
  padding: 16px 24px 100px;
  overflow-y: auto;
}

/* Status Header Section */
.status-summary {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 32px;
}

.status-icon-wrapper {
  position: relative;
  margin-bottom: 16px;
  padding-top: 16px;
}

.status-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease;
}

.status-icon:hover {
  transform: scale(1.05);
}

.status-icon.success {
  background-color: #00a859;
  color: #ffffff;
}

.status-icon .material-symbols-outlined {
  font-size: 48px;
  font-variation-settings: 'FILL' 1;
}

.status-glow {
  position: absolute;
  inset: -8px;
  background-color: rgba(0, 109, 56, 0.1);
  border-radius: 50%;
  filter: blur(16px);
  z-index: -1;
}

.status-title {
  font-family: 'Manrope', sans-serif;
  font-size: 24px;
  font-weight: 700;
  margin: 0 0 4px 0;
}

.status-message {
  color: #3d4a3f;
  font-size: 15px;
  margin: 0;
}

.amount-display {
  margin-top: 8px;
  display: flex;
  align-items: baseline;
  justify-content: center;
  color: #006d38;
}

.currency-symbol {
  font-size: 18px;
  font-weight: 700;
  margin-right: 4px;
}

.amount-value {
  font-family: 'Manrope', sans-serif;
  font-size: 32px;
  font-weight: 700;
}

/* Transaction Card */
.details-card {
  background-color: #ffffff;
  border-radius: 24px;
  padding: 24px;
  box-shadow: 0 8px 24px rgba(28, 27, 27, 0.04);
  margin-bottom: 24px;
}

.card-label {
  font-size: 12px;
  font-weight: 700;
  color: #3d4a3f;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 20px;
}

.details-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
}

.detail-group {
  display: flex;
  flex-direction: column;
}

.detail-group.align-right {
  align-items: flex-end;
  text-align: right;
}

.label {
  font-size: 13px;
  font-weight: 500;
  color: #3d4a3f;
  margin-bottom: 2px;
}

.value {
  font-size: 15px;
  font-weight: 600;
  color: #1c1b1b;
}

.truncate {
  max-width: 180px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.value-with-action {
  display: flex;
  align-items: center;
  gap: 8px;
}

.copy-button {
  border: none;
  background: transparent;
  color: #006d38;
  cursor: pointer;
  padding: 0;
  display: flex;
}

.copy-button .material-symbols-outlined {
  font-size: 18px;
}

.detail-divider {
  border-top: 1px solid #f6f3f2;
  margin-top: 8px;
}

.value-mono {
  font-family: monospace;
  font-size: 13px;
  color: rgba(28, 27, 27, 0.8);
  text-transform: uppercase;
  word-break: break-all;
}

.value-italic {
  font-size: 13px;
  color: rgba(28, 27, 27, 0.8);
  font-style: italic;
}

/* Trust Badge */
.trust-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background-color: #f6f3f2;
  padding: 8px 16px;
  border-radius: 999px;
  width: fit-content;
  margin: 0 auto;
}

.trust-badge .material-symbols-outlined {
  font-size: 16px;
  color: #006d38;
  font-variation-settings: 'FILL' 1;
}

.trust-badge span {
  font-size: 12px;
  font-weight: 600;
  color: #3d4a3f;
}

/* Footer Actions */
.action-footer {
  margin-top: 32px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.btn-primary, .btn-secondary {
  width: 100%;
  padding: 16px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 16px;
  border: none;
  cursor: pointer;
  transition: transform 0.2s ease;
  -webkit-tap-highlight-color: transparent;
}

.btn-primary {
  background-color: #006d38;
  color: #ffffff;
}

.btn-secondary {
  background-color: #eae7e7;
  color: #3d4a3f;
}

.btn-primary:active, .btn-secondary:active {
  transform: scale(0.98);
}

/* Bottom Nav */
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100%;
  max-width: 420px;
  background-color: #ffffff;
  padding: 12px 16px 24px;
  border-top-left-radius: 24px;
  border-top-right-radius: 24px;
  box-shadow: 0 -8px 24px rgba(28, 27, 27, 0.06);
  display: flex;
  justify-content: space-around;
  align-items: center;
  z-index: 50;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  background: transparent;
  border: none;
  padding: 4px 16px;
  color: #3d4a3f;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.nav-item.active {
  background-color: rgba(0, 168, 89, 0.1);
  border-radius: 16px;
  color: #006d38;
}

.nav-item.active .material-symbols-outlined {
  font-variation-settings: 'FILL' 1;
}

.nav-label {
  font-size: 13px;
  font-weight: 500;
  margin-top: 4px;
}

.nav-item.active .nav-label {
  font-weight: 700;
}

.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>```
