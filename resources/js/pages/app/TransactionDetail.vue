<script lang="ts" setup>
import { ref, computed, reactive} from 'vue'
import {checkSvg} from '@/svgs/svg';
import {xSvg} from '@/svgs/svg';
import {clockSvg} from '@/svgs/svg';
import Header from '../../components/Header.vue';

// --- State ---
// const currentType = ref('transfer')
const currentStatus = ref('successful')
const showReceiptModal = ref(false)

const toast = reactive({
  visible: false,
  message: '',
  icon: 'check_circle'
})

let toastTimeout = null

const prop = defineProps([
    'transactionDetail',
    'completedAtHuman'
])

// --- Computed ---
// const activeData = computed(() => prop.transactionDetail)



const isSuccess = computed(() => currentStatus.value === 'successful')
const isProcessing = computed(() => currentStatus.value === 'pending')
const isFailed = computed(() => currentStatus.value === 'failed')
const isCancelled = computed(() => currentStatus.value === 'cancelled')


const statusLabel = computed(() => {
  if (isSuccess.value){
   return 'Successful'
  }

  if (isProcessing.value){
  return 'Pending'
  }
  
  if (isFailed.value){
    return 'Failed'
  } 

  if (isCancelled.value){
    return 'Cancelled'
  }

  return ''
})

// const statusDesc = computed(() => {
//   if (isSuccess.value){
//      return `${activeData.value.typeTitle} completed successfully`
//   }

//   if (isProcessing.value){
//    return `${activeData.value.typeTitle} is processing`
//   } 

//   if (isFailed.value){
//     return `${activeData.value.typeTitle} declined`
//   }

//   if (isCancelled.value){
//    return `${activeData.value.typeTitle} was cancelled`
//   } 

//   return ''
// })

const themeClass = computed(() => {
  if (isSuccess.value){
   return 'theme-success'
  } 

  if (isProcessing.value){
    return 'theme-processing'
  } 

  if (isFailed.value){
   return 'theme-failed'
  } 

  if (isCancelled.value){
   return 'theme-cancelled'
  }

  return 'theme-success'
})

const statusIcon = computed(() => {
  if (isSuccess.value){
   return checkSvg;
  } 

  if (isProcessing.value){
    return clockSvg;
  } 

  if (isFailed.value){
   return xSvg;
  } 

  if (isCancelled.value){
   return 'theme-cancelled'
  }

  return 'theme-success'
})

// --- Methods ---
// const switchType = (type) => {
//   currentType.value = type
// }

const switchStatus = (status) => {
  currentStatus.value = status
}

// const showToast = (message, icon = 'check_circle') => {
//   if (toastTimeout){
//     clearTimeout(toastTimeout)
//   } 

//   toast.message = message
//   toast.icon = icon
//   toast.visible = true
//   toastTimeout = setTimeout(() => {
//     toast.visible = false
//   }, 2800)
// }

// const copyToClipboard = (text) => {
//   navigator.clipboard?.writeText(text).catch(() => {})
//   showToast('Copied to clipboard!', 'content_copy')
// }

// const copyReference = () => {
//   copyToClipboard(activeData.value.ref)
// }

// const repeatBeneficiary = () => {
//   showToast('Initializing quick pay...', 'send')
// }

// const openReceiptModal = () => {
//   showReceiptModal.value = true
// }

// const closeReceiptModal = () => {
//   showReceiptModal.value = false
// }

// const openSupportModal = () => {
//   showToast('Redirecting to support...', 'support_agent')
// }

// const simulateAction = (msg) => {
//   showToast(msg, 'info')
// }
switchStatus(prop.transactionDetail.status)


// const goBack = () => {
//   window.history.back()
// }
</script>

<template>
  <div class="app-wrapper">
<Header/>

    <main class="main-content pt-safe pb-safe">
      <div class="content-wrapper">

        <!-- Main Status Hero Card -->
        <div class="card hero-card" :class="themeClass">
          <div class="status-glow"></div>
          
          <!-- Animated Status Emblem -->
          <div class="status-emblem-wrap">
            <div class="status-circle-outer">
              <span v-html="statusIcon " class="material-symbols-outlined status-icon"></span>
            </div>
          </div>

          <!-- Status Pill -->
          <div class="status-badge font-label-sm">
            <span class="status-dot"></span>
            <span>{{ statusLabel }}</span>
          </div>

          <!-- Primary Amount -->
          <div class="amount-display">
            <!-- <span class="currency-symbol font-headline-sm">{{ activeData[0].amount }}</span> -->
            <!-- <span class="amount-value font-display-lg-mobile">₦{{ activeData.amount ?? 'hello' }}</span> -->
          </div>
          
 
        </div>

        <!-- Failure Details Alert -->
        <div v-if="isFailed || isCancelled" class="failure-alert">
          <div class="failure-icon-wrap">
            <span class="material-symbols-outlined icon-sm">info</span>
          </div>
          <div class="failure-content">
            <span class="failure-title font-headline-sm">{{ isFailed ? 'Transaction Declined' : 'Transaction Cancelled' }}</span>
            <p class="failure-message font-body-sm">
              {{ isFailed ? 'Bank network reported insufficient funds in the funding account or an invalid session timeout.' : 'This transaction was cancelled by the user before completion.' }}
            </p>
          </div>
        </div>

        <!-- Financial Breakdown Accordion/Card -->
        <div class="card">
          <div class="card-header border-bottom">
            <span class="label-heading font-label-sm">Transaction Detail</span>
            <div class="direction-badge font-label-sm">
              <!-- <span class="material-symbols-outlined icon-xs">north_east</span> -->
              <span>Debit</span>
            </div>
          </div>
          <div class="breakdown-list">  
                     <!-- <div class="breakdown-row">
              <span class="breakdown-label font-body-sm">Transaction Type</span>
              <span class="breakdown-val font-body-md">{{ prop.transactionDetail.type }}</span>
            </div>   -->

            <div v-if="prop.transactionDetail.type === 'transfer'" class="breakdown-row">
              <span class="breakdown-label font-body-sm">Recipient Name</span>
              <span class="breakdown-val font-body-md">{{ prop.transactionDetail.recipient }}</span>
            </div>  
             <div v-if="prop.transactionDetail.type === 'transfer'" class="breakdown-row">
              <span class="breakdown-label font-body-sm">Recipient Bank</span>
              <span class="breakdown-val font-body-md">{{ prop?.bank ?? 'opay' }}</span>
            </div>  
                   <div  v-if="prop.transactionDetail.type === 'transfer'"  class="breakdown-row">
              <span class="breakdown-label font-body-sm">Account Number</span>
              <span class="breakdown-val font-body-md">{{ prop.transactionDetail.accountNumber ?? '09132026039' }}</span>
            </div>  

                       <div  v-if="prop.transactionDetail.type === 'data' || prop.transactionDetail.type === 'airtime' "  class="breakdown-row">
              <span class="breakdown-label font-body-sm">Recipient Mobile</span>
              <span class="breakdown-val font-body-md">{{ prop.transactionDetail.recipient ?? '09132026039' }}</span>
            </div>  

              <div  v-if="prop.transactionDetail.type === 'data'"  class="breakdown-row">
              <span class="breakdown-label font-body-sm">Data Bundle</span>
              <span class="breakdown-val font-body-md">{{ prop.transactionDetail.plan ?? '500MB' }}</span>
            </div>  


                   <div class="breakdown-row">
              <span class="breakdown-label font-body-sm">Transaction Reference</span>
              <span class="breakdown-val font-body-md">{{ prop.transactionDetail.transaction_reference }}</span>
            </div>  

                         <div class="breakdown-row">
              <span class="breakdown-label font-body-sm">Transaction Date</span>
              <span class="breakdown-val font-body-md">{{ completedAtHuman }}</span>
            </div>  
     
          </div>
        </div>

  

        <!-- Contextual Action Buttons -->
        <!-- <div class="action-buttons-group">
          <button v-if="isSuccess" class="btn-primary font-headline-sm" @click="openReceiptModal">
            Share Receipt
          </button>
          <button v-if="isSuccess" class="btn-secondary font-headline-sm" @click="simulateAction('Downloading PDF...')">
            Download PDF
          </button>
          
          <button v-if="isProcessing" class="btn-secondary font-headline-sm" @click="simulateAction('Refreshing status...')">
            Refresh Status
          </button>
          
          <button v-if="isFailed || isCancelled" class="btn-primary font-headline-sm" @click="simulateAction('Retrying transaction...')">
            Retry Transaction
          </button>
        </div> -->

        <!-- Support & Dispute Link -->
        <div class="support-link-wrap">
          <button class="support-btn font-label-md" @click="">
            Have an issue with this transaction? Contact Support
          </button>
        </div>
      </div>
    </main>

  </div>
</template>

<style scoped>
/* --- Design Tokens --- */
.app-wrapper {
  --color-surface-tint: #006c4a;
  --color-tertiary-container: #00855b;
  --color-on-tertiary: #ffffff;
  --color-primary-fixed: #85f8c4;
  --color-on-error: #ffffff;
  --color-secondary-fixed: #d5e3fd;
  --color-background: #faf8ff;
  --color-surface-container-lowest: #ffffff;
  --color-on-background: #131b2e;
  --color-outline: #6d7a72;
  --color-primary-container: #00855d;
  --color-on-primary: #ffffff;
  --color-surface: #faf8ff;
  --color-on-surface: #131b2e;
  --color-on-primary-fixed: #002114;
  --color-on-primary-fixed-variant: #005137;
  --color-on-secondary: #ffffff;
  --color-secondary-fixed-dim: #b9c7e0;
  --color-on-secondary-container: #57657b;
  --color-error-container: #ffdad6;
  --color-surface-container: #eaedff;
  --color-surface-container-high: #e2e7ff;
  --color-surface-container-low: #f2f3ff;
  --color-tertiary: #006947;
  --color-error: #ba1a1a;
  --color-on-surface-variant: #3d4a42;
  --color-primary: #006948;
  --color-inverse-surface: #283044;
  --color-inverse-on-surface: #eef0ff;
  --color-inverse-primary: #68dba9;
  --color-surface-container-highest: #dae2fd;
  --color-secondary: #515f74;
  --color-on-error-container: #93000a;

  --radius-lg: 0.25rem;
  --radius-xl: 0.5rem;
  --radius-full: 0.75rem; /* Configured as 0.75rem */
  --radius-circle: 9999px; /* For true circles */

  --gutter-mobile: 1rem;
  --space-xs: 0.25rem;

  background-color: var(--color-surface);
  color: var(--color-on-surface);
  font-family: 'Inter', sans-serif;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  min-height: 100dvh;
  -webkit-font-smoothing: antialiased;
}

/* --- Typography --- */
.font-label-sm { font-size: 11px; line-height: 14px; letter-spacing: 0.06em; font-weight: 500; }
.font-label-md { font-size: 12px; line-height: 16px; letter-spacing: 0.04em; font-weight: 600; }
.font-body-sm { font-size: 13px; line-height: 18px; letter-spacing: 0.005em; font-weight: 400; }
.font-body-md { font-size: 14px; line-height: 22px; letter-spacing: 0em; font-weight: 400; }
.font-headline-sm { font-size: 18px; line-height: 26px; letter-spacing: -0.01em; font-weight: 500; }
.font-display-lg-mobile { font-size: 30px; line-height: 38px; letter-spacing: -0.025em; font-weight: 600; }

.icon-xs { font-size: 14px; }
.icon-sm { font-size: 16px; }
.icon-md { font-size: 22px; }

/* --- Base Layout --- */
.pt-safe { padding-top: env(safe-area-inset-top, 0px); }
.pb-safe { padding-bottom: env(safe-area-inset-bottom, 0px); }

.app-header {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 50;
  background-color: rgba(250, 248, 255, 0.9);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  box-shadow: 0 1px 8px rgba(0,0,0,0.03);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 var(--gutter-mobile);
  height: 4rem;
}

.header-title {
  font-weight: 600;
  letter-spacing: -0.01em;
  text-align: center;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 0 0.5rem;
  color: var(--color-on-surface);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.icon-btn, .icon-btn-variant {
  min-width: 44px;
  min-height: 44px;
  width: 2.75rem;
  height: 2.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: var(--radius-circle);
  color: var(--color-on-surface);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.icon-btn-variant {
  color: var(--color-on-surface-variant);
}

.icon-btn:hover, .icon-btn-variant:hover {
  background-color: var(--color-surface-container-low);
  color: var(--color-on-surface);
}

.avatar-circle {
  width: 2rem;
  height: 2rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.text-on-primary { color: var(--color-on-primary); }
.text-secondary { color: var(--color-secondary); }

/* --- Main Content Area --- */
.main-content {
  display: flex;
  flex-direction: column;
  position: relative;
  width: 100%;
  padding-top: 4rem;
  padding-left: var(--gutter-mobile);
  padding-right: var(--gutter-mobile);
  background-color: var(--color-surface);
  flex: 1;
}

.content-wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
  padding-bottom: 2.5rem;
  padding-top: 2rem;
}

.card {
  width: 100%;
  background-color: var(--color-surface-container-lowest);
  border-radius: var(--radius-xl);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  padding: 1rem;
  margin-bottom: 1rem;
}

/* --- Controls Card --- */
.controls-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.label-heading {
  color: var(--color-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.live-demo-badge {
  color: var(--color-primary);
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.pulse-dot {
  width: 0.375rem;
  height: 0.375rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-primary);
  display: inline-block;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.pill-scroller {
  display: flex;
  gap: 0.5rem;
  overflow-x: auto;
  padding-bottom: 0.5rem;
  margin: 0 -0.25rem;
  padding-left: 0.25rem;
  padding-right: 0.25rem;
  scrollbar-width: none;
}
.pill-scroller::-webkit-scrollbar { display: none; }

.type-pill {
  flex-shrink: 0;
  padding: 0.375rem 0.75rem;
  border-radius: var(--radius-lg);
  background-color: var(--color-surface-container-low);
  color: var(--color-secondary);
  display: flex;
  align-items: center;
  gap: 0.375rem;
  transition: all 0.2s;
  border: none;
  cursor: pointer;
}

.active-type-pill {
  background-color: var(--color-surface-container-high);
  color: var(--color-on-surface);
}

.status-segment-group {
  margin-top: 0.5rem;
  padding-top: 0.5rem;
  background-color: var(--color-surface-container-low);
  border-radius: var(--radius-lg);
  padding: 0.25rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.status-pill {
  flex: 1;
  padding: 0.375rem 0;
  border-radius: var(--radius-lg);
  text-align: center;
  color: var(--color-secondary);
  transition: all 0.2s;
  border: none;
  background: transparent;
  cursor: pointer;
}

.active-status-pill {
  background-color: var(--color-surface-container-lowest);
  color: var(--color-on-surface);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* --- Hero Card & Status Themes --- */
.hero-card {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.status-glow {
  position: absolute;
  top: -3rem;
  width: 12rem;
  height: 12rem;
  border-radius: var(--radius-circle);
  filter: blur(24px);
  pointer-events: none;
  transition: background-color 0.5s;
}

.status-emblem-wrap {
  position: relative;
  margin-bottom: 0.875rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-circle-outer {
  width: 4rem;
  height: 4rem;
  border-radius: var(--radius-circle);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.status-icon {
  font-size: 32px;
  font-weight: 600;
  transition: transform 0.3s;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.25rem 0.625rem;
  border-radius: var(--radius-circle);
  text-transform: uppercase;
  margin-bottom: 0.75rem;
}

.status-dot {
  width: 0.375rem;
  height: 0.375rem;
  border-radius: var(--radius-circle);
  display: inline-block;
}

.amount-display {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 0.125rem;
  color: var(--color-on-surface);
  margin-bottom: 0.25rem;
}

.currency-symbol {
  color: var(--color-secondary);
}

.amount-value {
  font-weight: 900;
  letter-spacing: 0.01em;
  line-height: 3rem;
}

.status-desc {
  color: var(--color-secondary);
  max-width: 20rem;
  margin-bottom: 0.75rem;
}

.timestamp-display {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-secondary);
}

/* Theme variables overrides depending on status */
.theme-success .status-glow { background-color: rgba(0, 105, 72, 0.1); }
.theme-success .status-circle-outer { background-color: var(--color-primary-fixed); }
.theme-success .status-icon { color: var(--color-primary); }
.theme-success .status-badge { background-color: var(--color-primary-fixed); color: var(--color-on-primary-fixed-variant); }
.theme-success .status-dot { background-color: var(--color-primary); }

.theme-processing .status-glow { background-color: rgba(81, 95, 116, 0.1); }
.theme-processing .status-circle-outer { background-color: var(--color-secondary-fixed); }
.theme-processing .status-icon { color: var(--color-secondary); animation: spin 2s linear infinite; }
.theme-processing .status-badge { background-color: var(--color-secondary-fixed); color: var(--color-on-secondary-container); }
.theme-processing .status-dot { background-color: var(--color-secondary); }

.theme-failed .status-glow { background-color: rgba(186, 26, 26, 0.1); }
.theme-failed .status-circle-outer { background-color: var(--color-error-container); }
.theme-failed .status-icon { color: var(--color-error); }
.theme-failed .status-badge { background-color: var(--color-error-container); color: var(--color-on-error-container); }
.theme-failed .status-dot { background-color: var(--color-error); }

.theme-cancelled .status-glow { background-color: rgba(61, 74, 66, 0.05); }
.theme-cancelled .status-circle-outer { background-color: var(--color-surface-container-high); }
.theme-cancelled .status-icon { color: var(--color-on-surface-variant); }
.theme-cancelled .status-badge { background-color: var(--color-surface-container-high); color: var(--color-on-surface-variant); }
.theme-cancelled .status-dot { background-color: var(--color-on-surface-variant); }

@keyframes spin { 100% { transform: rotate(360deg); } }

/* --- Failure Alert --- */
.failure-alert {
  width: 100%;
  background-color: var(--color-error-container);
  color: var(--color-on-error-container);
  border-radius: var(--radius-xl);
  padding: 1rem;
  margin-bottom: 1rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  transition: all 0.3s;
}

.failure-icon-wrap {
  width: 2rem;
  height: 2rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-error);
  color: var(--color-on-error);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.failure-content {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
}

.failure-title {
  font-weight: 600;
  margin-bottom: 0.125rem;
}

.failure-message {
  opacity: 0.9;
}

/* --- Recipient Card --- */
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.verified-badge {
  color: var(--color-primary);
  display: flex;
  align-items: center;
  gap: 0.25rem;
  background-color: var(--color-primary-fixed);
  padding: 0.125rem 0.5rem;
  border-radius: var(--radius-circle);
}

.verified-icon { font-size: 13px; }

.beneficiary-row {
  display: flex;
  align-items: center;
  gap: 0.875rem;
}

.beneficiary-avatar {
  width: 3rem;
  height: 3rem;
  border-radius: var(--radius-xl);
  background-color: var(--color-surface-container-high);
  color: var(--color-on-surface);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  flex-shrink: 0;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.beneficiary-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
  flex: 1;
}

.beneficiary-name-wrap {
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.beneficiary-name {
  font-weight: 600;
  color: var(--color-on-surface);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.beneficiary-sub {
  color: var(--color-secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.quick-pay-btn {
  min-width: 40px;
  min-height: 40px;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-surface-container-low);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-on-surface);
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.quick-pay-btn:hover {
  background-color: var(--color-surface-container-high);
}

/* --- Token Card --- */
.token-card {
  width: 100%;
  background-color: var(--color-surface-container-highest);
  color: var(--color-on-surface);
  border-radius: var(--radius-xl);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  padding: 1rem;
  margin-bottom: 1rem;
}

.token-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.token-label {
  color: var(--color-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.token-icon { color: var(--color-tertiary); }

.token-units {
  background-color: var(--color-surface-container-lowest);
  color: var(--color-primary);
  padding: 0.125rem 0.5rem;
  border-radius: var(--radius-lg);
  font-weight: 600;
}

.token-box {
  background-color: var(--color-surface-container-lowest);
  border-radius: var(--radius-xl);
  padding: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.token-text-col {
  display: flex;
  flex-direction: column;
}

.token-hint { color: var(--color-secondary); }

.token-digits {
  letter-spacing: 0.1em;
  font-weight: 600;
  color: var(--color-on-surface);
}

.copy-btn {
  min-height: 40px;
  padding: 0 0.75rem;
  border-radius: var(--radius-lg);
  background-color: var(--color-surface-container-low);
  color: var(--color-on-surface);
  display: flex;
  align-items: center;
  gap: 0.25rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.copy-btn:hover { background-color: var(--color-surface-container-high); }

/* --- Breakdown Card --- */
.border-bottom {
  border-bottom: 1px solid var(--color-surface-container-low);
  padding-bottom: 0.75rem;
}

.direction-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  color: var(--color-secondary);
  background-color: var(--color-surface-container-low);
  padding: 0.125rem 0.5rem;
  border-radius: var(--radius-circle);
}

.breakdown-list {
  display: flex;
  flex-direction: column;
  gap: 0.625rem;
}

.breakdown-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.breakdown-label { color: var(--color-secondary); }
.breakdown-label-with-icon {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  color: var(--color-secondary);
}

.breakdown-val {
  color: var(--color-on-surface);
  font-weight: 500;
}

.breakdown-total-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 0.625rem;
  background-color: var(--color-surface-container-low);
  border-radius: var(--radius-lg);
  padding: 0.625rem;
}

.total-label {
  color: var(--color-on-surface);
  font-weight: 600;
}

.total-val {
  color: var(--color-on-surface);
  font-weight: 700;
}

/* --- Audit Card --- */
.padding-bottom { padding-bottom: 0.75rem; }

.meta-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.25rem 0;
}

.align-start { align-items: flex-start; }

.meta-label { color: var(--color-secondary); }
.meta-val {
  font-weight: 500;
  color: var(--color-on-surface);
}

.meta-val-wallet {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-weight: 500;
  color: var(--color-on-surface);
}

.wallet-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-primary);
  display: inline-block;
}

.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.max-w-200 { max-width: 200px; }
.max-w-220 { max-width: 220px; }
.text-right { text-align: right; }

.reference-container { padding-top: 0.5rem; }
.reference-box {
  background-color: var(--color-surface-container-low);
  border-radius: var(--radius-lg);
  padding: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.reference-text {
  display: flex;
  flex-direction: column;
  min-width: 0;
  padding-right: 0.5rem;
}

.reference-label { color: var(--color-secondary); }
.reference-val {
  color: var(--color-on-surface);
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ref-copy-btn {
  min-width: 40px;
  min-height: 40px;
  padding: 0.375rem 0.625rem;
  border-radius: var(--radius-lg);
  background-color: var(--color-surface-container-lowest);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  color: var(--color-on-surface);
  display: flex;
  align-items: center;
  gap: 0.375rem;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  flex-shrink: 0;
}

.ref-copy-btn:hover { background-color: var(--color-surface-container); }
.ref-copy-btn:active { transform: scale(0.95); }

/* --- Action Buttons --- */
.action-buttons-group {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.625rem;
  margin-top: 0.5rem;
}

.btn-primary, .btn-secondary {
  width: 100%;
  padding: 0.75rem;
  border-radius: var(--radius-xl);
  font-weight: 600;
  text-align: center;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background-color: var(--color-primary);
  color: var(--color-on-primary);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.btn-primary:active { transform: scale(0.98); }

.btn-secondary {
  background-color: var(--color-surface-container-high);
  color: var(--color-on-surface);
}
.btn-secondary:active { transform: scale(0.98); }

/* --- Support Link --- */
.support-link-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
  padding-top: 0.5rem;
}

.support-btn {
  color: var(--color-secondary);
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.5rem 0.75rem;
  border-radius: var(--radius-lg);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: color 0.2s;
}

.support-btn:hover { color: var(--color-on-surface); }

/* --- Modal --- */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 50;
  background-color: rgba(19, 27, 46, 0.4);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 0;
}

@media (min-width: 640px) {
  .modal-backdrop {
    align-items: center;
    padding: 1rem;
  }
}

.modal-content {
  width: 100%;
  max-width: 24rem;
  background-color: var(--color-surface-container-lowest);
  border-top-left-radius: 1rem;
  border-top-right-radius: 1rem;
  padding: 1.5rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  max-height: 795px;
  overflow-y: auto;
}

@media (min-width: 640px) {
  .modal-content {
    border-radius: 1rem;
  }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 0.75rem;
  margin-bottom: 1rem;
}

.modal-title-wrap {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.modal-icon {
  width: 1.75rem;
  height: 1.75rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-on-primary);
}

.modal-title {
  font-weight: 600;
  color: var(--color-on-surface);
}

.modal-close-btn {
  width: 2rem;
  height: 2rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-surface-container);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-on-surface);
  border: none;
  cursor: pointer;
  transition: background-color 0.2s;
}

.modal-close-btn:hover { background-color: var(--color-surface-container-high); }

.receipt-ticket {
  background-color: var(--color-surface-container-low);
  border-radius: var(--radius-xl);
  padding: 1rem;
  margin-bottom: 1rem;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.receipt-currency-circle {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: var(--radius-circle);
  background-color: var(--color-primary-fixed);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
  color: var(--color-primary);
  font-weight: 700;
}

.receipt-amount {
  font-weight: 700;
  color: var(--color-on-surface);
}

.receipt-status {
  color: var(--color-primary);
  font-weight: 600;
  text-transform: uppercase;
  margin-top: 0.125rem;
}

.receipt-date {
  color: var(--color-secondary);
  margin-top: 0.25rem;
}

.receipt-details {
  width: 100%;
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--color-surface-container-high);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  text-align: left;
}

.receipt-row {
  display: flex;
  justify-content: space-between;
}

.receipt-label { color: var(--color-secondary); }
.receipt-val {
  font-weight: 500;
  color: var(--color-on-surface);
}

.receipt-val-mono {
  font-family: monospace;
  color: var(--color-on-surface);
}

.modal-actions {
  display: flex;
  gap: 0.5rem;
}

.modal-btn-secondary, .modal-btn-primary {
  flex: 1;
  padding: 0.75rem 1rem;
  border-radius: var(--radius-xl);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border: none;
  cursor: pointer;
  font-weight: 500;
  transition: transform 0.2s;
}

.modal-btn-secondary {
  background-color: var(--color-surface-container-high);
  color: var(--color-on-surface);
}

.modal-btn-primary {
  background-color: var(--color-primary);
  color: var(--color-on-primary);
  font-weight: 600;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.modal-btn-secondary:active, .modal-btn-primary:active { transform: scale(0.98); }

/* --- Toast --- */
.toast-notification {
  position: fixed;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 50;
  background-color: var(--color-inverse-surface);
  color: var(--color-inverse-on-surface);
  padding: 0.625rem 1rem;
  border-radius: var(--radius-circle);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.text-inverse-primary { color: var(--color-inverse-primary); }

/* --- Transitions --- */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(100%);
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translate(-50%, 0.5rem);
}
</style>