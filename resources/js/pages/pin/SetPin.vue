<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useForm } from "@inertiajs/vue3";
// --- State Machine ---
const STATE_CREATE = 'CREATE'
const STATE_CONFIRM = 'CONFIRM'
const STATE_SECURING = 'SECURING'
const STATE_SUCCESS = 'SUCCESS'

const currentState = ref(STATE_CREATE)
const initialPin = ref('')
const confirmPin = ref('')

// --- UI State ---
const isError = ref(false)
const isShaking = ref(false)
const isHeaderFading = ref(false)

const form = useForm({
  pin:'',
});

// --- Computed Properties ---
const currentPin = computed(() => currentState.value === STATE_CREATE ? initialPin.value : confirmPin.value)
const isPinComplete = computed(() => currentPin.value.length === 4)
const stepNumber = computed(() => currentState.value === STATE_CREATE ? '1' : '2')
const progressWidth = computed(() => currentState.value === STATE_CREATE ? '50%' : '100%')

const viewTitle = computed(() => currentState.value === STATE_CREATE ? 'Create your PIN' : 'Confirm your PIN')
const viewSubtitle = computed(() => 
  currentState.value === STATE_CREATE 
    ? 'Set a secure 4-digit PIN to protect your transactions.' 
    : 'Enter your 4-digit PIN again to confirm.'
)

// --- Methods ---
const handleDigitInput = (digit) => {
  isError.value = false
  if (currentState.value === STATE_CREATE && initialPin.value.length < 4) {
    initialPin.value += digit
  } else if (currentState.value === STATE_CONFIRM && confirmPin.value.length < 4) {
    confirmPin.value += digit
  }
}

const handleBackspace = () => {
  isError.value = false
  if (currentState.value === STATE_CREATE) {
    initialPin.value = initialPin.value.slice(0, -1)
  } else if (currentState.value === STATE_CONFIRM) {
    confirmPin.value = confirmPin.value.slice(0, -1)
  }
}

const handleClear = () => {
  isError.value = false
  if (currentState.value === STATE_CREATE) {
    initialPin.value = ''
  } else if (currentState.value === STATE_CONFIRM) {
    confirmPin.value = ''
  }
}

const triggerMismatchError = () => {
  isError.value = true
  isShaking.value = true
  setTimeout(() => {
    isShaking.value = false
  }, 450)
  // Reset confirm PIN so user can try again
  confirmPin.value = ''
}

const handlePrimaryAction = () => {
  if (!isPinComplete.value) return

  if (currentState.value === STATE_CREATE) {
    // Transition to Confirm state with header fade animation
    isHeaderFading.value = true
    setTimeout(() => {
      currentState.value = STATE_CONFIRM
      isHeaderFading.value = false
    }, 200)
  } else if (currentState.value === STATE_CONFIRM) {
    // Validate PIN match
    if (initialPin.value === confirmPin.value) {
     form.pin = confirmPin.value
     form.post('/set_pin')


      // Simulate cryptographic securing process delay
    //   setTimeout(() => {
    //     currentState.value = STATE_SUCCESS
    //   }, 2500)
    } else {
      triggerMismatchError()
    }
  }
}

const resetFlow = () => {
  currentState.value = STATE_CREATE
  initialPin.value = ''
  confirmPin.value = ''
  isError.value = false
}

// const showHelp = () => {
//   alert('PIN security guidelines: Never share your PIN with anyone. Paynow staff will never ask for your PIN.')
// }

// --- Keyboard Interactions ---
const handleKeydown = (e) => {
  // Only handle keys if we're in input states
  if (currentState.value !== STATE_CREATE && currentState.value !== STATE_CONFIRM) return

  if (/^[0-9]$/.test(e.key)) {
    handleDigitInput(e.key)
  } else if (e.key === 'Backspace') {
    handleBackspace()
  } else if (e.key === 'Enter') {
    handlePrimaryAction()
  } else if (e.key === 'Escape' || e.key === 'Delete') {
    handleClear()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <div class="app-wrapper">
    <div class="app-container">
      
      <!-- Header / Brand & Stepper -->
      <header class="header-section">
        <!-- Top Bar: Security Badge & Step Indicator -->
        <div class="top-bar">
          <div class="brand-badge">
            <div class="brand-icon-container">
              <!-- Shield lock icon -->
              <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                <rect x="9" y="11" width="6" height="5" rx="1"></rect>
              </svg>
            </div>
            <span class="brand-text">
                PayNow
            </span>
          </div>
          </div>

        <!-- Linear Subtle Flow Progress Track
        <div class="progress-track">
          <div class="progress-bar" :style="{ width: progressWidth }"></div>
        </div> -->
      </header>

      <!-- Main Content Area: Multi-State Container -->
      <main class="main-content">
        <Transition name="view-fade" mode="out-in">
          
          <!-- ============================================== -->
          <!-- STATE 1 & 2: PIN ENTRY / CONFIRMATION VIEW     -->
          <!-- ============================================== -->
          <div v-if="currentState === 'CREATE' || currentState === 'CONFIRM'" class="view-container pin-view">
            
            <!-- Header Text Block -->
            <div class="view-header" :class="{ 'is-fading': isHeaderFading }">
              <h1 class="view-title">{{ viewTitle }}</h1>
              <p class="view-subtitle">{{ viewSubtitle }}</p>
            </div>

            <!-- 4-Digit PIN Indicator Slots -->
            <div class="pin-dots-container" :class="{ 'shake-calm': isShaking }">
              <div 
                v-for="index in 4" 
                :key="index"
                class="pin-slot"
                :class="{
                  'is-filled': index - 1 < currentPin.length,
                  'is-active': index - 1 === currentPin.length,
                  'is-empty': index - 1 > currentPin.length
                }"
              >
                <div class="pin-dot" :class="{ 'pin-filled': index - 1 < currentPin.length }"></div>
              </div>
            </div>

            <!-- Calm Feedback Notice Area -->
            <div class="feedback-container">
              <div class="feedback-message" :class="{ 'is-visible': isError }">
                <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="8" x2="12" y2="12"></line>
                  <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>PINs don't match. Please try again.</span>
              </div>
            </div>

            <!-- Ergonomic Tactile Numeric Keypad -->
            <div class="keypad">
              <button type="button" class="keypad-btn" @click="handleDigitInput('1')">
                <span class="keypad-digit">1</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('2')">
                <span class="keypad-digit">2</span>
                <span class="keypad-letters">ABC</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('3')">
                <span class="keypad-digit">3</span>
                <span class="keypad-letters">DEF</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('4')">
                <span class="keypad-digit">4</span>
                <span class="keypad-letters">GHI</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('5')">
                <span class="keypad-digit">5</span>
                <span class="keypad-letters">JKL</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('6')">
                <span class="keypad-digit">6</span>
                <span class="keypad-letters">MNO</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('7')">
                <span class="keypad-digit">7</span>
                <span class="keypad-letters">PQRS</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('8')">
                <span class="keypad-digit">8</span>
                <span class="keypad-letters">TUV</span>
              </button>
              <button type="button" class="keypad-btn" @click="handleDigitInput('9')">
                <span class="keypad-digit">9</span>
                <span class="keypad-letters">WXYZ</span>
              </button>
              
              <!-- Bottom Row: Reset / 0 / Backspace -->
              <button type="button" class="keypad-btn is-ghost" @click="handleClear" title="Clear PIN">
                <span class="keypad-action-text">Clear</span>
              </button>
              
              <button type="button" class="keypad-btn" @click="handleDigitInput('0')">
                <span class="keypad-digit">0</span>
              </button>

              <button type="button" class="keypad-btn is-ghost" @click="handleBackspace" aria-label="Delete last digit">
                <svg class="icon-md stroke-thick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path>
                  <line x1="18" y1="9" x2="12" y2="15"></line>
                  <line x1="12" y1="9" x2="18" y2="15"></line>
                </svg>
              </button> 
            </div>

            <!-- Primary Action Button -->
            <button 
              type="button" 
              class="primary-btn" 
              :class="{ 'is-active': isPinComplete, 'is-disabled': !isPinComplete }"
              :disabled="!isPinComplete"
              @click="handlePrimaryAction"
            >
              <span>{{ currentState === 'CREATE' ? 'Continue' : 'Confirm' }}</span>
              <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </div>

          <!-- ============================================== -->
          <!-- STATE 3: PROCESSING / SECURING ACCOUNT VIEW    -->
          <!-- ============================================== -->
          <div v-else-if="currentState === 'SECURING'" class="view-container loading-view">
            <div class="loader-container">
              <!-- Subtle Pulsing Glow -->
              <div class="loader-glow"></div>
              
              <!-- Outer Clean Spinner -->
              <svg class="loader-spinner" viewBox="0 0 24 24" fill="none">
                <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2.5"></circle>
                <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              
              <!-- Inner Security Icon -->
              <div class="loader-icon">
                <svg class="icon-md" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
              </div>
            </div>

  
          </div>

          <!-- ============================================== -->
          <!-- STATE 4: SUCCESS VIEW                          -->
          <!-- ============================================== -->
          <div v-else-if="currentState === 'SUCCESS'" class="view-container success-view">
            <!-- Success Checkmark Badge -->
            <div class="success-badge">
              <svg class="icon-lg stroke-thick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>

            <h2 class="view-title">PIN created successfully</h2>
            <p class="view-subtitle text-centered mt-2 max-w-xs">
              Your transaction PIN is now active and will be required to authorize future transfers and card management.
            </p>

            <!-- Success CTA -->
            <button type="button" class="primary-btn is-active mt-7 w-full" @click="resetFlow">
              <span>Go to Account Dashboard</span>
              <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>

            <!-- Secondary action to re-test demo -->
            <button type="button" class="restart-demo-btn" @click="resetFlow">
              Reset and test flow again
            </button>
          </div>

        </Transition>
      </main>
    </div>
  </div>
</template>

<style scoped>
/* ==========================================================================
   Design Tokens & Variables (Mapped from Tailwind config)
   ========================================================================== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;450;500;600;700&display=swap');

:root {
  --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  
  --vault-50: #F8FAF9;
  --vault-100: #F0F4F2;
  --vault-200: #E1E9E5;
  --vault-300: #C3D4CD;
  --vault-400: #91B2A5;
  --vault-500: #059669;
  --vault-600: #047857;
  
  --canvas: #FBFBFB;
  --surface: #FFFFFF;
  
  --ink-100: #F2F4F7;
  --ink-200: #E4E7EC;
  --ink-400: #8A97A6;
  --ink-600: #485363;
  --ink-800: #1D242D;
  --ink-900: #0A0D12;
  
  --amber-50-90: rgba(255, 251, 235, 0.9);
  --amber-200-80: rgba(253, 230, 138, 0.8);
  --amber-700: #b45309;

  --shadow-subtle: 0 1px 2px 0 rgba(16, 24, 40, 0.04);
  --shadow-keypad: 0 1px 2px 0 rgba(16, 24, 40, 0.03), 0 0 0 1px rgba(16, 24, 40, 0.05);
  --shadow-pin-focus: 0 0 0 3px rgba(5, 150, 105, 0.15);
}

/* ==========================================================================
   Global / Base Styles
   ========================================================================== */
.app-wrapper {
  background-color: var(--canvas);
  color: var(--ink-900);
  font-family: var(--font-sans);
  min-height: 100vh;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  font-feature-settings: "cv02", "cv03", "cv04", "cv11";
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  touch-action: manipulation;
}

.app-wrapper ::selection {
  background-color: var(--vault-100);
  color: var(--ink-900);
}

.app-container {
  width: 100%;
  height: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 1rem;
  max-width: 32rem;
  margin: 0 auto;
}

@media (min-width: 640px) {
  .app-container {
    padding: 2.5rem 1.5rem;
  }
}

/* ==========================================================================
   Icons & SVGs
   ========================================================================== */
.icon-xs { width: 0.875rem; height: 0.875rem; }
.icon-sm { width: 0.875rem; height: 0.875rem; }
.icon-md { width: 1.5rem; height: 1.5rem; }
.icon-lg { width: 2rem; height: 2rem; }
.stroke-thick { stroke-width: 1.75; }
.icon-vault { color: var(--vault-500); }

/* ==========================================================================
   Header & Progress
   ========================================================================== */
.header-section {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.25rem;
}

.top-bar {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.75rem;
  letter-spacing: -0.015em;
}

.brand-badge {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--ink-600);
  font-weight: 500;
}

.brand-icon-container {
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 9999px;
  background-color: var(--surface);
  border: 1px solid var(--ink-200);
  box-shadow: var(--shadow-subtle);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--vault-600);
}

.brand-text {
  letter-spacing: 0.025em;
  text-transform: uppercase;
  font-size: 11px;
  font-weight: 600;
  color: var(--ink-600);
}

.step-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.25rem 0.625rem;
  border-radius: 9999px;
  background-color: var(--surface);
  border: 1px solid var(--ink-200);
  color: var(--ink-600);
  font-size: 11px;
  font-weight: 500;
  box-shadow: var(--shadow-subtle);
}

.step-number {
  color: var(--vault-600);
  font-weight: 600;
}

.step-divider {
  color: var(--ink-400);
}

.step-label {
  color: var(--ink-400);
  margin-left: 0.25rem;
}

.progress-track {
  width: 100%;
  background-color: rgba(228, 231, 236, 0.8);
  height: 0.25rem;
  border-radius: 9999px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: var(--vault-500);
  border-radius: 9999px;
  transition: width 0.3s ease-out;
}

/* ==========================================================================
   Main Content Layout
   ========================================================================== */
.main-content {
  width: 100%;
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin: 1rem 0;
  position: relative;
  min-height: 460px;
}

@media (min-width: 640px) {
  .main-content { margin: 1.5rem 0; }
}

.view-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.view-header {
  text-align: center;
  max-width: 24rem;
  padding: 0 0.5rem;
  margin-bottom: 2rem;
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.view-header.is-fading {
  opacity: 0;
  transform: translateY(-4px);
}

.view-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--ink-900);
  letter-spacing: -0.015em;
  transition: all 0.2s ease;
}

@media (min-width: 640px) {
  .view-title { font-size: 1.75rem; }
}

.view-subtitle {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  color: var(--ink-600);
  line-height: 1.625;
  transition: all 0.2s ease;
}

.text-centered {
  text-align: center;
}

.max-w-xs {
  max-width: 20rem;
}

/* ==========================================================================
   PIN Dots
   ========================================================================== */
.pin-dots-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.25rem;
  margin-bottom: 1.75rem;
  padding: 0.75rem 0;
}

@media (min-width: 640px) {
  .pin-dots-container { gap: 1.5rem; }
}

.pin-slot {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border-radius: 1rem;
  background-color: var(--surface);
  transition: all 0.2s ease;
}

.pin-slot.is-active {
  border: 2px solid var(--vault-500);
  box-shadow: var(--shadow-pin-focus);
}

.pin-slot.is-filled {
  border: 1px solid var(--ink-900);
}

.pin-slot.is-empty {
  border: 1px solid var(--ink-200);
}

.pin-dot {
  width: 0.875rem;
  height: 0.875rem;
  border-radius: 9999px;
  background-color: var(--ink-900);
  opacity: 0;
  transform: scale(0);
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.pin-dot.pin-filled {
  animation: pinPop 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  background-color: #0A0D12;
}

@keyframes pinPop {
  0% { transform: scale(0.7); opacity: 0.4; }
  50% { transform: scale(1.18); }
  100% { transform: scale(1); opacity: 1; }
}

@keyframes subtleShake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-6px); }
  40%, 80% { transform: translateX(6px); }
}

.shake-calm {
  animation: subtleShake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
}

/* ==========================================================================
   Feedback Message
   ========================================================================== */
.feedback-container {
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.feedback-message {
  opacity: 0;
  transition: opacity 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--amber-700);
  background-color: var(--amber-50-90);
  border: 1px solid var(--amber-200-80);
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
}

.feedback-message.is-visible {
  opacity: 1;
}

/* ==========================================================================
   Keypad
   ========================================================================== */
.keypad {
  width: 100%;
  max-width: 320px;
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.875rem;
  margin-bottom: 1.5rem;
  user-select: none;
}

@media (min-width: 640px) {
  .keypad { gap: 1rem; }
}

.keypad-btn {
  height: 3.5rem;
  border-radius: 1rem;
  background-color: var(--surface);
  color: var(--ink-900);
  border: 1px solid rgba(228, 231, 236, 0.9);
  box-shadow: var(--shadow-keypad);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: all 0.1s ease;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}

@media (min-width: 640px) {
  .keypad-btn { height: 4rem; }
}

.keypad-btn:active {
  transform: scale(0.95);
  background-color: var(--vault-100);
  box-shadow: none;
}

.keypad-btn:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.3);
}

.keypad-btn.is-ghost {
  background-color: transparent;
  border-color: transparent;
  box-shadow: none;
  color: var(--ink-600);
}

.keypad-btn.is-ghost:hover {
  color: var(--ink-900);
}

.keypad-btn.is-ghost:active {
  background-color: var(--ink-100);
}

.keypad-digit {
  font-size: 1.25rem;
  font-weight: 500;
}

@media (min-width: 640px) {
  .keypad-digit { font-size: 1.5rem; }
}

.keypad-letters {
  font-size: 9px;
  letter-spacing: 0.1em;
  color: var(--ink-400);
  font-weight: 600;
  text-transform: uppercase;
  margin-top: -0.125rem;
}

.keypad-action-text {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.025em;
  text-transform: uppercase;
}

/* ==========================================================================
   Primary Action Button
   ========================================================================== */
.primary-btn {
  width: 100%;
  max-width: 320px;
  padding: 0.875rem 1.5rem;
  border-radius: 1rem;
  font-weight: 500;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border: none;
  box-shadow: var(--shadow-subtle);
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
}

.primary-btn.is-disabled {
  background-color: var(--ink-200);
  color: var(--ink-400);
  cursor: not-allowed;
}

.primary-btn.is-active {
  background-color: var(--vault-500);
  color: var(--surface);
}

.primary-btn.is-active:hover {
  background-color: var(--vault-600);
}

.primary-btn.is-active:active {
  transform: scale(0.98);
}

/* ==========================================================================
   Loading View
   ========================================================================== */
.loading-view {
  max-width: 24rem;
  text-align: center;
}

.loader-container {
  position: relative;
  width: 5rem;
  height: 5rem;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.loader-glow {
  position: absolute;
  inset: 0;
  border-radius: 9999px;
  background-color: rgba(5, 150, 105, 0.1);
  animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
  opacity: 0.6;
}

.loader-spinner {
  width: 4rem;
  height: 4rem;
  color: var(--ink-200);
  animation: spin 1s linear infinite;
}

.spinner-track { opacity: 0.25; }
.spinner-head { opacity: 0.9; color: var(--vault-500); }

.loader-icon {
  position: absolute;
  color: var(--vault-600);
}

.telemetry-badge {
  margin-top: 2rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  background-color: var(--surface);
  border: 1px solid var(--ink-200);
  font-size: 11px;
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  color: var(--ink-600);
  box-shadow: var(--shadow-subtle);
}

.telemetry-dot {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 9999px;
  background-color: var(--vault-500);
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes ping {
  75%, 100% { transform: scale(2); opacity: 0; }
}
@keyframes spin {
  to { transform: rotate(360deg); }
}
@keyframes pulse {
  50% { opacity: 0.5; }
}

/* ==========================================================================
   Success View
   ========================================================================== */
.success-view {
  max-width: 24rem;
  text-align: center;
}

.success-badge {
  width: 4rem;
  height: 4rem;
  border-radius: 9999px;
  background-color: var(--vault-50);
  border: 1px solid rgba(225, 233, 229, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--vault-600);
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-subtle);
}
.success-badge svg {
  width: 2rem;
  height: 2rem;
  stroke-width: 2.2;
}

.security-card {
  width: 100%;
  margin-top: 1.75rem;
  padding: 1rem;
  border-radius: 1rem;
  background-color: var(--surface);
  border: 1px solid rgba(228, 231, 236, 0.8);
  box-shadow: var(--shadow-subtle);
  text-align: left;
  display: flex;
  align-items: flex-start;
  gap: 0.875rem;
}

.security-card-icon {
  padding: 0.5rem;
  border-radius: 0.75rem;
  background-color: var(--vault-50);
  color: var(--vault-600);
  flex-shrink: 0;
}
.security-card-icon svg {
  width: 1.25rem;
  height: 1.25rem;
}

.security-card-text h3 {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--ink-900);
  margin: 0;
}

.security-card-text p {
  font-size: 12px;
  color: var(--ink-600);
  margin-top: 0.125rem;
  line-height: 1.5;
}

.restart-demo-btn {
  margin-top: 0.75rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--ink-400);
  background: transparent;
  border: none;
  cursor: pointer;
  transition: color 0.2s ease;
}

.restart-demo-btn:hover {
  color: var(--ink-600);
}

/* ==========================================================================
   Footer
   ========================================================================== */
.footer-section {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding-top: 1rem;
  border-top: 1px solid rgba(228, 231, 236, 0.6);
}

.footer-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.75rem;
  color: var(--ink-400);
}

.footer-encrypted {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
}

.footer-link {
  color: inherit;
  text-decoration: underline;
  text-underline-offset: 2px;
  transition: color 0.2s ease;
}

.footer-link:hover {
  color: var(--ink-600);
}

/* ==========================================================================
   Vue Transitions
   ========================================================================== */
.view-fade-enter-active,
.view-fade-leave-active {
  transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1), transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}

.view-fade-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.view-fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

</style>

