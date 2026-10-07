<template>
  <section class="wallet-card">
    <div class="card-glow glow-1"></div>
    <div class="card-glow glow-2"></div>
    
    <div class="card-content">
      <div class="balance-header">
        <span class="balance-label">Total Balance</span>
        <button 
          class="toggle-btn active-tap" 
          @click="toggleVisibility" 
          aria-label="Toggle Balance Visibility"
        >
          <span class="icon-wrapper">
            <img 
              :src="isVisible ? togglebtnStates.show : togglebtnStates.hide" 
              alt="Toggle Icon" 
            />
          </span>
        </button>
      </div>
      
      <div class="balance-amount">
        <span class="currency">{{ wallet.currency }}</span>
        <span class="amount" :class="{ 'is-hidden': !isVisible }">
          {{ displayBalance }}
        </span>
      </div>

      <div class="card-actions">
        <button class="add-money-btn active-tap" @click="navigateToSendMoney">
          <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/>
          </svg>
          <span>Add Money</span>
        </button>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps({
  wallet: {
    type: Object,
    required: true
  },
  isVisible: {
    type: Boolean,
    default: true
  },
  togglebtnStates: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['toggle-visibility', 'navigate-send-money']);

const toggleVisibility = () => emit('toggle-visibility');

const navigateToSendMoney = () => {
  emit('navigate-send-money');
};

const displayBalance = computed(() => {
  return props.isVisible
    ? props.wallet.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })
    : '****';
});

const formattedCashback = computed(() => {
  return props.wallet.cashback.toLocaleString('en-US', { minimumFractionDigits: 2 });
});
</script>

<style scoped>
/* Base Card Styling */
.wallet-card {
  position: relative;
  /* Darker, defined Fintech Green Gradient */
  background: linear-gradient(145deg, #0b6638 0%, #044d28 50%, #023d20 100%);
  border-radius: 20px;
  padding: 24px;
  color: #ffffff;
  /* Deep shadow for high-contrast premium feel */
  box-shadow: 0 16px 36px rgba(2, 61, 32, 0.35), 0 4px 12px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

/* Ambient Glows */
.card-glow {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
}

.glow-1 {
  right: -60px;
  top: -60px;
  width: 220px;
  height: 220px;
  background: radial-gradient(circle, rgba(16, 201, 113, 0.25) 0%, rgba(0, 0, 0, 0) 70%);
}

.glow-2 {
  left: -40px;
  bottom: -50px;
  width: 180px;
  height: 180px;
  background: radial-gradient(circle, rgba(16, 201, 113, 0.12) 0%, rgba(0, 0, 0, 0) 70%);
}

.card-content {
  position: relative;
  z-index: 10;
}

/* Header Section */
.balance-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}

.balance-label {
  font-size: 13px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.8);
  letter-spacing: 0.03em;
}

/* Toggle Button */
.toggle-btn {
  background: none;
  border: none;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 4px;
  border-radius: 50%;
  transition: background-color 0.2s ease;
}

.toggle-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
}

.icon-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  opacity: 0.85;
}

/* Balance Amount - Anti-Jitter Structure */
.balance-amount {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  min-height: 44px;
}

.currency {
  font-size: 18px;
  font-weight: 700;
  margin-top: 4px;
  color: rgba(255, 255, 255, 0.9);
}

.amount {
  font-size: 34px;
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: -0.03em;
  font-variant-numeric: tabular-nums;
  white-space: nowrap;
}

.amount.is-hidden {
  letter-spacing: 0.05em;
  transform: translateY(6px);
}

/* Action Area */
.card-actions {
  margin-top: 16px;
  display: flex;
  justify-content: flex-start;
}

.add-money-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.15);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  transition: background 0.2s ease, border-color 0.2s ease;
}

.add-money-btn:hover {
  background: rgba(255, 255, 255, 0.22);
  border-color: rgba(255, 255, 255, 0.35);
}

.btn-icon {
  width: 16px;
  height: 16px;
}

/* Cashback Banner */
.cashback-banner {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: rgba(0, 0, 0, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  padding: 12px 16px;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.cashback-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.cashback-label {
  font-size: 11px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.cashback-amount {
  font-size: 15px;
  font-weight: 700;
}

/* Tap Interaction */
.active-tap {
  transition: transform 0.12s cubic-bezier(0.4, 0, 0.2, 1);
}

.active-tap:active {
  transform: scale(0.94);
}
</style>
