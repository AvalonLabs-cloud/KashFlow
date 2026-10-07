<template>
  <div id="app" class="app-root">
    <!-- TopNavBar -->
    <header class="top-nav">
      <nav class="nav-inner">
        <div class="brand">
          <span class="brand-name">FintechID</span>
        </div>
        <div class="nav-right">
          <div class="nav-links">
            <a href="#" class="nav-link nav-link-active">Verification</a>
            <a href="#" class="nav-link">History</a>
            <a href="#" class="nav-link">Settings</a>
          </div>
          <div class="nav-icons">
            <span class="material-symbols-outlined nav-icon">help_outline</span>
            <span class="material-symbols-outlined nav-icon">lock</span>
          </div>
        </div>
      </nav>
    </header>

    <main class="main-content">
      <div class="atmospheric-bg" aria-hidden="true"></div>

      <div class="verification-wrapper">
        <div class="premium-card" :class="{ 'card-disabled': isCancelled }">
          <transition name="state-transition" mode="out-in">
            <!-- Processing State -->
            <div v-if="currentState === 'PROCESSING'" key="processing" class="state-block">
              <div class="verification-loader" aria-label="Verifying"></div>
              <h1 class="headline">Processing your BVN verification</h1>
              <p class="body-text">
                We're securely verifying your BVN consent. This usually takes a few moments. Please keep this page open.
              </p>
            </div>

            <!-- Success State -->
            <div v-else-if="currentState === 'SUCCESS'" key="success" class="state-block">
              <div class="icon-circle icon-circle-success">
                <span class="material-symbols-outlined icon-white icon-filled">check_circle</span>
              </div>
              <h1 class="headline">BVN Verified Successfully</h1>
              <p class="body-text">
                Your BVN consent has been successfully verified. Redirecting you to the next step...
              </p>
            </div>

            <!-- Failure State -->
            <div v-else-if="currentState === 'FAILURE'" key="failure" class="state-block state-block-full">
              <div class="icon-circle icon-circle-error">
                <span class="material-symbols-outlined icon-error icon-filled">warning</span>
              </div>
              <h1 class="headline">BVN Verification Failed</h1>
              <p class="body-text">
                We couldn't verify your BVN consent. This may happen if consent was declined or the verification was unsuccessful.
              </p>
              <div class="button-row">
                <button type="button" class="btn btn-primary" @click="retryVerification">Retry</button>
                <button type="button" class="btn btn-ghost" @click="cancelVerification">Cancel</button>
              </div>
            </div>

            <!-- Network Error State -->
            <div v-else key="network" class="state-block state-block-full">
              <div class="icon-circle icon-circle-neutral">
                <span class="material-symbols-outlined icon-outline">cloud_off</span>
              </div>
              <h1 class="headline">Connection Problem</h1>
              <p class="body-text">
                We're unable to check your verification status right now. Please check your internet connection.
              </p>
              <div class="button-row button-row-full">
                <button type="button" class="btn btn-primary btn-full" @click="retryVerification">Retry</button>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
      <div class="footer-inner">
        <div class="footer-brand">
          <span class="footer-brand-name">FintechID</span>
          <p class="footer-copy">© 2024 FintechID Inc. Secure Identity Verification.</p>
        </div>
        <div class="footer-links">
          <a href="#" class="footer-link">Privacy Policy</a>
          <a href="#" class="footer-link">Terms of Service</a>
          <a href="#" class="footer-link">Security Compliance</a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, onMounted, onUnmounted } from 'vue'

const POLL_INTERVAL_MS = 2000
const SUCCESS_REDIRECT_DELAY_MS = 2000
const SUCCESS_REDIRECT_ROUTE = '/onboarding/phase_five'

const currentState = ref('PROCESSING')
const attemptCount = ref(0)
const isCancelled = ref(false)

let pollTimer = null
let redirectTimer = null

function clearPollTimer() {
  if (pollTimer) {
    clearInterval(pollTimer)
    pollTimer = null
  }
}

function clearRedirectTimer() {
  if (redirectTimer) {
    clearTimeout(redirectTimer)
    redirectTimer = null
  }
}

function startPolling() {
  clearPollTimer()

  pollTimer = setInterval(() => {
    attemptCount.value++

    // if (attemptCount.value === OUTCOME_ATTEMPT_THRESHOLD) {
    //   const outcome = Math.random()

    //   if (outcome > 0.6) {
    //     handleSuccess()
    //   } else if (outcome > 0.3) {
    //     handleFailure()
    //   } else {
    //     handleNetworkError()
    //   }
    // }

 axios.get('/onboarding/bvn_consent_status')
    .then(({ data }) => {
        switch (data.bvnConsentStatus) {
            case 'Approved':
                clearPollTimer()
                handleSuccess()
                break;

            case 'NotApproved':
                clearPollTimer()
                handleFailure()
                break;

            default:
                // Pending
                // Continue polling
                break;
        }
    })
    .catch((error) => {
        console.error('Failed to retrieve BVN consent status.', error);
        handleNetworkError()
    });

  }, POLL_INTERVAL_MS)
}

function handleSuccess() {
  clearPollTimer()
  currentState.value = 'SUCCESS'

  redirectTimer = setTimeout(() => {
    router.get(SUCCESS_REDIRECT_ROUTE)
  }, SUCCESS_REDIRECT_DELAY_MS)
}

function handleFailure() {
  clearPollTimer()
  currentState.value = 'FAILURE'
}

function handleNetworkError() {
  clearPollTimer()
  currentState.value = 'NETWORK'
}

function retryVerification() {
  attemptCount.value = 0
  isCancelled.value = false
  currentState.value = 'PROCESSING'
  startPolling()
}

function cancelVerification() {
  clearPollTimer()
  clearRedirectTimer()
  isCancelled.value = true
  router.visit(window.history.state?.previousUrl || document.referrer || '/', { replace: true })
}

onMounted(() => {
  startPolling()
})

onUnmounted(() => {
  clearPollTimer()
  clearRedirectTimer()
})
</script>

<style scoped>
/*
  This component relies on the "Inter" and "Material Symbols Outlined"
  Google Fonts being loaded globally (e.g. in the root Blade layout / app.css):

  https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap
*/

.app-root {
  --color-primary: #006e2a;
  --color-primary-fixed-dim: #3ce36a;
  --color-primary-container: #00c853;
  --color-on-primary: #ffffff;
  --color-on-surface: #191c1d;
  --color-on-surface-variant: #3c4a3c;
  --color-on-secondary-fixed-variant: #474746;
  --color-surface: #f8f9fa;
  --color-surface-variant: #e1e3e4;
  --color-surface-container-high: #e7e8e9;
  --color-inverse-surface: #2e3132;
  --color-outline: #6c7b6a;
  --color-error: #ba1a1a;
  --color-error-container: #ffdad6;

  --spacing-xs: 4px;
  --spacing-sm: 8px;
  --spacing-md: 16px;
  --spacing-gutter: 16px;
  --spacing-lg: 24px;
  --spacing-xl: 32px;
  --spacing-2xl: 48px;
  --spacing-3xl: 64px;
  --spacing-container-margin: 20px;

  --radius-default: 0.25rem;
  --radius-lg: 0.5rem;
  --radius-xl: 16px;
  --radius-full: 9999px;

  background-color: var(--color-surface);
  color: var(--color-on-surface);
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  line-height: 20px;
  font-weight: 400;
  overflow-x: hidden;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.material-symbols-outlined {
  font-family: 'Material Symbols Outlined';
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  display: inline-block;
  line-height: 1;
}

.icon-filled {
  font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* ---------- Header ---------- */
.top-nav {
  width: 100%;
  top: 0;
  background-color: var(--color-surface);
  z-index: 50;
}

.nav-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-md) var(--spacing-container-margin);
  max-width: 1280px;
  margin: 0 auto;
}

.brand {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}

.brand-name {
  font-family: 'Inter', sans-serif;
  font-size: 24px;
  line-height: 32px;
  font-weight: 700;
  color: var(--color-primary);
}

.nav-right {
  display: flex;
  align-items: center;
  gap: var(--spacing-lg);
}

.nav-links {
  display: none;
  gap: var(--spacing-xl);
}

.nav-link {
  color: var(--color-on-surface-variant);
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  line-height: 20px;
  font-weight: 400;
  text-decoration: none;
  transition: color 250ms;
}

.nav-link:hover {
  color: var(--color-primary);
}

.nav-link-active {
  color: var(--color-primary);
  font-weight: 700;
}

.nav-icons {
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
}

.nav-icon {
  color: var(--color-primary);
  cursor: pointer;
}

@media (min-width: 768px) {
  .nav-links {
    display: flex;
  }
}

/* ---------- Main ---------- */
.main-content {
  min-height: 716px;
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: var(--spacing-3xl) var(--spacing-container-margin);
  position: relative;
  overflow: hidden;
}

.atmospheric-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
  opacity: 0.3;
  pointer-events: none;
}

.verification-wrapper {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 32rem;
}

.premium-card {
  background-color: #ffffff;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
  border: 1px solid #e0e0e0;
  border-radius: var(--radius-xl);
  padding: var(--spacing-2xl);
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--spacing-xl);
  transition: all 500ms;
}

.premium-card.card-disabled {
  opacity: 0.5;
  pointer-events: none;
}

.state-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--spacing-lg);
}

.state-block-full {
  width: 100%;
}

.verification-loader {
  width: 80px;
  height: 80px;
  margin-bottom: var(--spacing-md);
  border: 4px solid #e8f5e9;
  border-top: 4px solid #00c853;
  border-radius: 50%;
  animation: spin 1s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.headline {
  font-family: 'Inter', sans-serif;
  font-size: 32px;
  line-height: 40px;
  letter-spacing: -0.01em;
  font-weight: 700;
  color: var(--color-on-surface);
  margin: 0;
}

.body-text {
  color: var(--color-on-surface-variant);
  font-family: 'Inter', sans-serif;
  font-size: 16px;
  line-height: 24px;
  font-weight: 400;
  max-width: 28rem;
  margin: 0;
}

.icon-circle {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--spacing-md);
}

.icon-circle-success {
  background-color: var(--color-primary-container);
}

.icon-circle-error {
  background-color: var(--color-error-container);
}

.icon-circle-neutral {
  background-color: var(--color-surface-container-high);
}

.icon-white {
  color: #ffffff;
  font-size: 48px;
}

.icon-error {
  color: var(--color-error);
  font-size: 48px;
}

.icon-outline {
  color: var(--color-outline);
  font-size: 48px;
}

.button-row {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-md);
  width: 100%;
  margin-top: var(--spacing-lg);
}

.button-row-full {
  width: 100%;
}

@media (min-width: 640px) {
  .button-row:not(.button-row-full) {
    flex-direction: row;
  }
}

.btn {
  height: 48px;
  border-radius: var(--radius-xl);
  border: none;
  cursor: pointer;
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  flex: 1;
}

.btn-full {
  width: 100%;
}

.btn-primary {
  background-color: #00c853;
  color: #ffffff;
  font-weight: 700;
  transition: background-color 250ms cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-primary:hover {
  background-color: #00a344;
}

.btn-ghost {
  background-color: transparent;
  color: #757575;
  font-weight: 600;
}

/* ---------- Transitions ---------- */
.state-transition-enter-active,
.state-transition-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.state-transition-enter-from {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

.state-transition-leave-to {
  opacity: 0;
  transform: scale(1.05) translateY(-10px);
}

/* ---------- Footer ---------- */
.site-footer {
  width: 100%;
  bottom: 0;
  background-color: var(--color-surface);
  border-top: 1px solid var(--color-surface-variant);
}

.footer-inner {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  padding: var(--spacing-lg) var(--spacing-container-margin);
  gap: var(--spacing-md);
  max-width: 1280px;
  margin: 0 auto;
}

.footer-brand {
  display: flex;
  flex-direction: column;
  gap: var(--spacing-xs);
}

.footer-brand-name {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  line-height: 16px;
  letter-spacing: 0.01em;
  font-weight: 600;
  color: var(--color-primary);
}

.footer-copy {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  line-height: 16px;
  letter-spacing: 0.01em;
  font-weight: 500;
  color: var(--color-on-surface-variant);
  margin: 0;
}

.footer-links {
  display: flex;
  gap: var(--spacing-lg);
}

.footer-link {
  font-family: 'Inter', sans-serif;
  font-size: 12px;
  line-height: 16px;
  letter-spacing: 0.01em;
  font-weight: 500;
  color: var(--color-on-surface-variant);
  text-decoration: none;
  transition: color 250ms;
}

.footer-link:hover {
  color: var(--color-on-surface);
}

@media (min-width: 768px) {
  .footer-inner {
    flex-direction: row;
  }
}
</style>
