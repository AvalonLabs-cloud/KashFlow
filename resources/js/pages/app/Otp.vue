```vue
<template>
  <div class="page-container">
    <!-- Mobile Screen Wrapper -->
    <div class="mobile-wrapper">
      <!-- TopAppBar Component -->
      <header class="app-header">
        <button class="icon-button" aria-label="Go back">
          <span class="material-symbols-outlined">arrow_back</span>
        </button>
        <h1 class="header-title">Verify Number</h1>
      </header>

      <!-- Content Canvas -->
      <main class="main-content">
        <!-- Branding/Visual Anchor -->
        <section class="branding-section">
          <div class="icon-container">
            <span class="material-symbols-outlined icon-fill">shield_lock</span>
          </div>
          <h2 class="headline">Enter the 5-digit code</h2>
          <p class="sub-headline">
            We sent a code to <span class="phone-highlight">+234 XXX XXX XXXX</span>
          </p>
        </section>

        <!-- OTP Input Section -->
        <div class="otp-container">
          <input
            v-for="(digit, index) in 5"
            :key="index"
            ref="otpInputs"
            v-model="otp[index]"
            type="text"
            inputmode="numeric"
            maxlength="1"
            pattern="\d*"
            class="otp-input"
            :placeholder="index > 1 ? '•' : ''"
            @input="handleInput($event, index)"
            @keydown.delete="handleDelete($event, index)"
          />
        </div>

        <!-- Resend Section -->
        <div class="resend-wrapper">
          <p class="resend-text">
            Didn't receive code?
            <button class="resend-link" @click="resendCode">
              Resend in {{ timer }}s
            </button>
          </p>
        </div>

        <!-- Security Message -->
        <div class="security-card">
          <!-- <span class="material-symbols-outlined security-icon">verified_user</span> -->
          <div class="security-content">
            <p class="security-title">Bank-grade security</p>
            <p class="security-description">
              Your connection is encrypted and your data is never shared with third parties.
            </p>
          </div>
        </div>

        <!-- Action Button -->
        <div class="action-wrapper">
          <button class="btn-verify" @click="verifyCode">
            Verify
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>
      </main>

      <!-- Optional Contextual Illustration -->
      <div class="background-illustration">
        <img 
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuAAMO5nUX1QwIIa5wuDWNrON8H1e9eT9ZbLvUbpA-cSWG49mvdsXkoIoFfCJljgJgXiuqpQFEdmMVOliDP_YkIufwLkY0j9bqhjGZ-ap63PfjzAv9KI9mGrvrDBFhuWujFyWDIIDf2wXNc7-GfqnrltDHCDVunq8bDoY5pTuvnEYEh1Kz99PCKcN-oUvshYwBrW5BIqgRz3LeKvbAh2EXLTumWq0Hxs-VWazx0QojXDxoBs1homaHHMSu_jn2w0L9qkbhvvp3zzVXOh" 
          alt="Security Illustration"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const otp = ref(['7', '3', '', '', '']);
const timer = ref(30);
const otpInputs = ref([]);

const handleInput = (event, index) => {
  const val = event.target.value;
  if (val && index < 4) {
    otpInputs.value[index + 1].focus();
  }
};

const handleDelete = (event, index) => {
  if (!otp.value[index] && index > 0) {
    otpInputs.value[index - 1].focus();
  }
};

const resendCode = () => {
  if (timer.value === 0) {
    timer.value = 30;
    // Resend logic here
  }
};

const verifyCode = () => {
  const code = otp.value.join('');
  console.log('Verifying code:', code);
};

onMounted(() => {
  if (otpInputs.value[0]) {
    otpInputs.value[0].focus();
  }
});
</script>

<style scoped>
/* Base Theme Variables based on Tailwind Config */
/* :host {
  --primary: #006e2a;
  --primary-container: #00ef64;
  --on-primary: #00ff51;
  --surface: #56f600;
  --on-surface: #151e15;
  --on-surface-variant: #3c4a3c;
  --outline-variant: #23df02;
  --surface-container-low: #54f000;
  --background: #4ede00;
} */

.page-container {
  background-color: var(--background);
  font-family: 'Inter', sans-serif;
  color: var(--on-surface);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.mobile-wrapper {
  width: 100%;
  max-width: 448px; /* max-w-md */
  height: 100vh;
  background-color: #ffffff;
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

@media (min-width: 768px) {
  .mobile-wrapper {
    height: 812px;
    border-radius: 3rem;
    border: 8px solid var(--on-surface);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  }
}

/* Header Styles */
.app-header {
  display: flex;
  align-items: center;
  width: 100%;
  height: 64px;
  padding: 0 16px;
  background-color: #ffffff;
  border-bottom: 1px solid #f3f4f6;
  position: sticky;
  top: 0;
  z-index: 10;
}

.icon-button {
  transition: all 0.2s;
  padding: 8px;
  border-radius: 9999px;
  border: none;
  background: transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-button:active {
  transform: scale(0.95);
}

.icon-button:hover {
  background-color: #f9fafb;
}

.icon-button .material-symbols-outlined {
  color: #16a34a; /* green-600 */
}

.header-title {
  font-size: 1.125rem;
  font-weight: 600;
  letter-spacing: -0.025em;
  color: #111827;
  margin-left: 8px;
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 32px 16px 0 16px;
  display: flex;
  flex-direction: column;
}

.branding-section {
  margin-bottom: 24px;
}

.icon-container {
  width: 64px;
  height: 64px;
  background-color: var(--primary-container);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.icon-fill {
  font-size: 32px;
  color: #ffffff;
  font-variation-settings: 'FILL' 1;
}

.headline {
  font-size: 24px;
  line-height: 32px;
  font-weight: 600;
  color: var(--on-surface);
  margin-bottom: 4px;
}

.sub-headline {
  font-size: 16px;
  line-height: 24px;
  color: var(--on-surface-variant);
}

.phone-highlight {
  font-weight: 600;
}

/* OTP Inputs */
.otp-container {
  display: flex;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 24px;
}

.otp-input {
  width: 100%;
  height: 56px;
  background-color: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  text-align: center;
  font-size: 36px;
  line-height: 44px;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: var(--on-surface);
  transition: all 0.2s;
}

.otp-input:focus {
  outline: none;
  border-color: #00c853;
  box-shadow: 0 0 0 4px rgba(0, 200, 83, 0.1);
}

.otp-input::placeholder {
  color: #d1d5db;
}

/* Resend Section */
.resend-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 32px;
}

.resend-text {
  font-size: 14px;
  font-weight: 500;
  color: var(--on-surface-variant);
}

.resend-link {
  color: var(--primary);
  font-weight: 600;
  background: none;
  border: none;
  cursor: pointer;
  margin-left: 4px;
}

.resend-link:hover {
  text-decoration: underline;
}

/* Security Card */
.security-card {
  margin-top: auto;
  margin-bottom: 24px;
  padding: 16px;
  background-color: var(--surface-container-low);
  border-radius: 12px;
  border: 1px solid var(--outline-variant);
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.security-icon {
  color: var(--primary);
  font-size: 20px;
}

.security-title {
  font-size: 12px;
  font-weight: 600;
  color: var(--on-surface);
  margin: 0;
}

.security-description {
  font-size: 12px;
  line-height: 1.4;
  color: var(--on-surface-variant);
  margin: 0;
}

/* Action Button */
.action-wrapper {
  padding-bottom: 32px;
}

.btn-verify {
  width: 100%;
  height: 52px;
  background-color:  #09e622;
  color: #ffffff;
  font-size: 20px;
  font-weight: 600;
  border: none;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
}

.btn-verify:active {
  transform: scale(0.98);
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05);
}

/* Illustration */
.background-illustration {
  position: absolute;
  bottom: 128px;
  left: 0;
  right: 0;
  pointer-events: none;
  opacity: 0.03;
  overflow: hidden;
  display: flex;
  justify-content: center;
}

.background-illustration img {
  width: 256px;
  height: 256px;
  filter: grayscale(100%);
}
</style>
