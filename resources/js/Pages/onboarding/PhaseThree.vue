<template>
    <div class="email-verification-page">
        <main class="ev-main">
            <div class="ev-container">
                <div class="ev-header">
                    <span style="
    display: block;
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 8px;
    background: #fff7ed;
    border: 1px solid #fdba74;
    color: #c2410c;
    font-size: 14px;
    font-weight: 500;
  ">
                        ⚠️ This service is currently available only in Nigeria.
                    </span>
                    <div class="ev-icon-container">
                        <span class="material-symbols-outlined ev-icon-security"><svg xmlns="http://www.w3.org/2000/svg"
                                width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#00ffb3" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-shield-icon lucide-shield">
                                <path
                                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                            </svg></span>
                    </div>
                    <h2 class="ev-title">Verify Your Phone Number</h2>
                    <p class="ev-description">
                        Enter your phone number to continue. We'll send a code to verify your account.
                    </p>
                </div>

                <form class="ev-form" @submit.prevent="handleSubmit" novalidate>
                    <div class="ev-form-group" :class="{
                        'ev-state-error': hasInteracted && !validationStatus.isValid && phoneNumber,
                        'ev-state-success': hasInteracted && validationStatus.isValid && phoneNumber
                    }">
                        <label class="ev-label" id="phone-input-label" for="phone-input">Phone Number</label>
                        <div class="ev-input-row">
                            <div class="ev-country-selector">
                                <button class="ev-country-btn" type="button" aria-haspopup="listbox"
                                    aria-label="Select country code, current selection Nigeria +234">
                                    <span class="ev-country-info">
                                        <span class="ev-flag">🇳🇬</span>
                                        <span class="ev-dial-code">+234</span>
                                    </span>
                                    <!-- <span class="material-symbols-outlined ev-expand-icon">expand_more</span> -->
                                </button>
                            </div>
                            <div class="ev-input-wrapper">
                                <input v-model="phoneNumber" @blur="hasInteracted = true" class="ev-input"
                                    id="phone-input" placeholder="Enter phone number" requireds type="tel"
                                    autocomplete="tel"
                                    :aria-invalid="hasInteracted && !validationStatus.isValid ? 'true' : 'false'"
                                    aria-labelledby="phone-input-label" ref="phoneInputRef"
                                    :class="error ? 'form-input--error' : ''" />
                            </div>
                            <!-- <span v-if="error" class="error-message" id="email-error">
  {{ error }}
</span> -->
                        </div>
                        <span v-if="error" class="error-message" id="email-error">
                            {{ error }}
                        </span>
                        <div class="ev-feedback-message" aria-live="polite">
                            {{ hasInteracted && phoneNumber ? validationStatus.message : '' }}
                        </div>
                    </div>

                    <button class="ev-submit-btn" type="submit" :disabled="isSent || loading"
                        :class="{ 'ev-btn-processing': loading, 'ev-btn-sent': isSent }">
                        <span class="ev-btn-content-wrapper">
                            <template v-if="isProcessing">
                                <span class="material-symbols-outlined animate-spin">progress_activity</span>
                                Processing...
                            </template>
                            <template v-else-if="isSent">
                                <span class="material-symbols-outlined">check_circle</span> Sent!
                            </template>
                            <template v-else>
                                Verify Phone Number
                                <!-- <span class="material-symbols-outlined ev-btn-arrow">chevron_right</span> -->
                            </template>
                        </span>
                    </button>
                </form>

                <div class="ev-footer">
                    <p class="ev-legal-text">
                        By continuing, you agree to our <a class="ev-link" href="#">Terms of Service</a> and <a
                            class="ev-link" href="#">Privacy Policy</a>.
                    </p>
                </div>
            </div>
        </main>
    </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue';
import { usePhoneVerification } from '@/composables/useVerifyAndStorePhoneNumber';
const { loading, error, verifyPhone } = usePhoneVerification();

const phoneNumber = ref<string>('');
const hasInteracted = ref<boolean>(false);
const isProcessing = ref<boolean>(false);
const isSent = ref<boolean>(false);
const phoneInputRef = ref<HTMLInputElement | null>(null);

// Clean numerical value generator
const getCleanValue = (val: string): string => val.replace(/\s+/g, '');

// Comprehensive UX Validation Rule logic
const validationStatus = computed(() => {
    const cleanValue = getCleanValue(phoneNumber.value);

    if (!cleanValue) {
        return { isValid: false, message: 'Phone number is required.' };
    }

    if (isNaN(Number(cleanValue))) {
        return { isValid: false, message: 'Please enter numbers only.' };
    }

    if (cleanValue.length < 7 || cleanValue.length > 15) {
        return { isValid: false, message: 'Please enter a valid phone number length (7-15 digits).' };
    }

    return { isValid: true, message: 'Ready to send verification code.' };
});

// Dynamic Form Submission Management & Micro-interactions
const handleSubmit = (): void => {
    hasInteracted.value = true;

    if (!validationStatus.value.isValid) {
        phoneInputRef.value?.focus();

        return;
    }

    // UX States: Processing / Loading State Transition
    //   isProcessing.value = true;

    verifyPhone(getCleanValue(phoneNumber.value));

    if (error.value) {
        // Handle error state (e.g., show a toast notification or inline message)
        console.error('Error verifying phone number:', error.value);
        isProcessing.value = false;

        return;
    }

    //   setTimeout(() => {
    //     // UX States: Success Feedback State Transition
    //     isProcessing.value = false;
    //     isSent.value = true;

    //     setTimeout(() => {
    //       // Reset UI state to baseline layout defaults
    //       isSent.value = false;
    //       phoneNumber.value = '';
    //       hasInteracted.value = false;
    //     }, 3000);
    //   }, 3000);
};
</script>

<style scoped>
.email-verification-page {
    /* Material Design 3 Dynamic Token Color Mapping */
    --ev-outline-variant: #c3c5d9;
    --ev-secondary: #505f76;
    --ev-on-background: #191b25;
    --ev-primary-container: #0052ff;
    --ev-on-error-container: #93000a;
    --ev-on-primary-container: #dfe3ff;
    --ev-on-primary: #ffffff;
    --ev-on-primary-fixed: #001452;
    --ev-primary: #003ec7;
    --ev-surface-container-highest: #e1e1ef;
    --ev-on-primary-fixed-variant: #0038b6;
    --ev-surface-variant: #e1e1ef;
    --ev-surface-tint: #004ced;
    --ev-outline: #737688;
    --ev-inverse-on-surface: #f0effe;
    --ev-on-secondary: #ffffff;
    --ev-surface: #fbf8ff;
    --ev-on-secondary-container: #54647a;
    --ev-tertiary-fixed: #ffdbd2;
    --ev-on-tertiary: #ffffff;
    --ev-secondary-fixed: #d3e4fe;
    --ev-error-container: #ffdad6;
    --ev-primary-fixed-dim: #b7c4ff;
    --ev-on-secondary-fixed-variant: #38485d;
    --ev-on-tertiary-fixed-variant: #891e00;
    --ev-surface-container-lowest: #ffffff;
    --ev-surface-bright: #fbf8ff;
    --ev-on-tertiary-fixed: #3c0800;
    --ev-on-surface-variant: #434656;
    --ev-on-secondary-fixed: #0b1c30;
    --ev-surface-container: #ededfb;
    --ev-background: #fbf8ff;
    --ev-secondary-container: #d0e1fb;
    --ev-error: #ba1a1a;
    --ev-success: #146c43;
    --ev-success-container: #d1e7dd;
    --ev-inverse-surface: #2e303a;
    --ev-tertiary-container: #bf3003;
    --ev-on-tertiary-container: #ffddd5;
    --ev-primary-fixed: #a2ffb0;

    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background-color: var(--ev-background);
    color: var(--ev-on-background);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}

.email-verification-page * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.email-verification-page .ev-main {
    width: 100%;
    max-width: 480px;
    padding: 64px 20px;
}

.email-verification-page .ev-container {
    width: 100%;
}

.email-verification-page .ev-header {
    margin-bottom: 32px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.email-verification-page .ev-icon-container {
    width: 100px;
    height: 100px;
    background-color: var(--ev-primary-fixed);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    margin-bottom: 24px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.email-verification-page .ev-icon-security {
    color: var(--ev-primary);
    /* font-size: 30px; */
    font-variation-settings: 'FILL' 1;
}

.email-verification-page .ev-title {
    font-size: 24px;
    line-height: 32px;
    letter-spacing: -0.01em;
    font-weight: 700;
    color: var(--ev-on-surface);
    text-align: center;
    margin-bottom: 8px;
}

@media (min-width: 640px) {
    .email-verification-page .ev-title {
        font-size: 32px;
        line-height: 40px;
        letter-spacing: -0.02em;
    }
}

.email-verification-page .ev-description {
    font-size: 16px;
    line-height: 24px;
    font-weight: 400;
    color: var(--ev-on-surface-variant);
    text-align: center;
    max-width: 320px;
}

.email-verification-page .ev-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.email-verification-page .ev-form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.email-verification-page .ev-label {
    font-size: 14px;
    line-height: 20px;
    font-weight: 500;
    color: var(--ev-on-surface-variant);
    margin-left: 4px;
}

.email-verification-page .ev-input-row {
    display: flex;
    gap: 8px;
}

.email-verification-page .ev-country-selector {
    position: relative;
    min-width: 100px;
}

.email-verification-page .ev-country-btn {
    width: 100%;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    background-color: var(--ev-surface-bright);
    border: 1px solid var(--ev-outline-variant);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.email-verification-page .ev-country-btn:hover {
    border-color: var(--ev-primary);
}

.email-verification-page .ev-country-btn:focus {
    outline: none;
    border-color: var(--ev-primary);
    box-shadow: 0 0 0 2px var(--ev-surface-bright), 0 0 0 4px var(--ev-primary);
}

.email-verification-page .ev-country-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.email-verification-page .ev-flag {
    font-size: 20px;
}

.email-verification-page .ev-dial-code {
    font-size: 14px;
    line-height: 20px;
    font-weight: 500;
    color: var(--ev-on-background);
}

.email-verification-page .ev-expand-icon {
    color: var(--ev-on-surface-variant);
    font-size: 14px;
}

.email-verification-page .ev-input-wrapper {
    position: relative;
    flex-grow: 1;
}

.email-verification-page .ev-input {
    width: 100%;
    height: 48px;
    padding: 0 16px;
    background-color: var(--ev-surface-bright);
    border: 1px solid var(--ev-outline-variant);
    border-radius: 8px;
    font-size: 16px;
    line-height: 24px;
    font-weight: 400;
    color: var(--ev-on-background);
    transition: all 0.15s ease-in-out;
}

.email-verification-page .ev-input::placeholder {
    color: var(--ev-outline);
}

.email-verification-page .ev-input:focus {
    outline: none;
    border-color: var(--ev-primary);
    box-shadow: 0 0 0 2px var(--ev-surface-bright), 0 0 0 4px var(--ev-primary);
}

/* Validation UX Feedback Messaging Styles */
.email-verification-page .ev-feedback-message {
    font-size: 13px;
    line-height: 16px;
    font-weight: 500;
    margin-left: 4px;
    margin-top: 2px;
    min-height: 16px;
    transition: color 0.2s ease;
}

/* ERROR STATE DESIGN CLASS MATRICES */
.email-verification-page .ev-state-error .ev-input {
    border-color: var(--ev-error);
    color: var(--ev-error);
    background-color: #fff8f8;
}

.email-verification-page .ev-state-error .ev-input:focus {
    box-shadow: 0 0 0 2px var(--ev-surface-bright), 0 0 0 4px var(--ev-error);
    border-color: var(--ev-error);
}

.email-verification-page .ev-state-error .ev-feedback-message {
    color: var(--ev-error);
}

/* SUCCESS STATE DESIGN CLASS MATRICES */
.email-verification-page .ev-state-success .ev-input {
    border-color: var(--ev-success);
}

.email-verification-page .ev-state-success .ev-feedback-message {
    color: var(--ev-success);
}

/* ACTION BUTTON AND DYNAMIC MICRO-INTERACTIONS UI */
.email-verification-page .ev-submit-btn {
    width: 100%;
    padding: 16px;
    background-color: var(--ev-primary);
    color: var(--ev-on-primary);
    font-size: 14px;
    line-height: 20px;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    cursor: pointer;
    margin-top: 32px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.email-verification-page .ev-btn-content-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
}

.email-verification-page .ev-submit-btn:hover:not(:disabled) {
    background-color: var(--ev-on-primary-fixed-variant);
}

.email-verification-page .ev-submit-btn:active:not(:disabled) {
    transform: scale(0.97);
}

.email-verification-page .ev-submit-btn:disabled {
    cursor: not-allowed;
}

/* Loading/Processing Intermediate UX State */
.email-verification-page .ev-submit-btn.ev-btn-processing {
    background-color: var(--ev-on-primary-fixed-variant);
    opacity: 0.85;
}

/* Finished Sent UX State Styling */
.email-verification-page .ev-submit-btn.ev-btn-sent {
    background-color: var(--ev-tertiary-container);
    color: var(--ev-on-tertiary-container);
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05);
}

.email-verification-page .ev-btn-arrow {
    font-size: 18px;
}

.email-verification-page .ev-footer {
    margin-top: 32px;
    text-align: center;
}

.email-verification-page .ev-legal-text {
    font-size: 12px;
    line-height: 16px;
    font-weight: 600;
    color: var(--ev-on-surface-variant);
}

.email-verification-page .ev-link {
    color: var(--ev-primary);
    text-decoration: none;
    font-weight: 600;
}

.email-verification-page .ev-link:hover {
    text-decoration: underline;
}

/* Keyframe for standard rotation operations */
@keyframes ev-spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.email-verification-page .animate-spin {
    animation: ev-spin 0.85s linear infinite;
    display: inline-block;
}

.form-input--error {
    border-color: #ef4444;
    box-shadow: 0 0 0 1px #ef4444;
}

.error-message {
    display: block;
    color: red;
    font-size: 0.875rem;
    margin-top: 4px;
}
</style>
