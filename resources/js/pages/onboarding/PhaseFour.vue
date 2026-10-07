<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';


const form = useForm({
    bvn: ''
});


const errors = reactive({
    bvn: ''
});

const isPasswordVisible = ref(false);

const togglePassword = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
};

const validateField = (field) => {
    if (field === 'bvn') {
        const bvnRegex = /^[0-9]{11}$/;

        if (!form.bvn) {
            errors.bvn = 'BVN is required.';
        } else if (!bvnRegex.test(form.bvn)) {
            errors.bvn = 'BVN must be exactly 11 digits.';
        } else {
            errors.bvn = '';
            form.clearErrors('bvn');
        }
    }
};


const isFormValid = () => {
    validateField('bvn');

    return  !errors.bvn;
};


const handleFormSubmit = () => {
    if (!isFormValid() || form.processing) {
        return;
    }

    form.post('/onboarding/bvn/acquisition', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <div class="app-container">
        <main class="main-content">
            <div class="card">
                <div class="card-header">
                    <div class="header-icon">
                        <span class="material-symbols-outlined">shield_lock</span>
                    </div>
                </div>

                <form class="form" @submit.prevent="handleFormSubmit">
                    <div class="form-group">
                        <div class="relative-input" :class="{ 'has-error': errors.bvn || form.errors.bvn }">
                            <input id="bvn" v-model="form.bvn" class="form-input bvn-input"
                                :type="isPasswordVisible ? 'text' : 'password'" maxlength="11" placeholder=" "
                                :disabled="form.processing" @input="validateField('bvn')"
                                @blur="validateField('bvn')" />
                            <label class="floating-label" for="bvn">Bank Verification Number (11-digits)</label>
                            <button class="toggle-visibility-btn" type="button"
                                :aria-label="isPasswordVisible ? 'Hide BVN' : 'Show BVN'" @click="togglePassword">
                                <span class="material-symbols-outlined">
                                    {{ isPasswordVisible ? 'visibility_off' : 'visibility' }}
                                </span>
                            </button>
                        </div>
                        <span v-if="errors.bvn || form.errors.bvn" class="error-message" role="alert">
                            {{ errors.bvn || form.errors.bvn }}
                        </span>
                    </div>
                    <button class="submit-btn" :disabled="form.processing" type="submit">
                        <span v-if="!form.processing">Continue to Verification</span>
                        <div v-else class="loading-spinner"></div>
                    </button>
                </form>

                <div class="disclaimer-section">
                    <span class="material-symbols-outlined info-icon">info</span>
                    <p class="disclaimer-text">
                        By continuing, you authorize FintechID to verify your details with regulatory authorities. We never store your BVN or share it with unauthorized third parties.
                    </p>
                </div>
            </div>
        </main>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-links">
                    <a class="footer-link" href="#">Privacy</a>
                    <a class="footer-link" href="#">Terms</a>
                    <a class="footer-link" href="#">Security</a>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@300..600,0..1&display=swap');

:root,
.app-container {
    /* Emerald Fintech Palette */
    --emerald-50: #ecfdf5;
    --emerald-100: #d1fae5;
    --emerald-500: #10b981;
    --emerald-600: #059669;
    --emerald-700: #047857;

    /* Slate Grays for layout */
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-500: #64748b;
    --slate-800: #1e293b;
    --slate-900: #0f172a;

    /* Functional */
    --error: #ef4444;
    --error-bg: #fef2f2;
    --surface: #ffffff;

    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--slate-900);
    background-color: var(--slate-50);
}

.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
    vertical-align: middle;
}

.filled-icon {
    font-variation-settings: 'FILL' 1;
}

/* Page Layout */
.app-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: radial-gradient(circle at top, var(--emerald-50) 0%, var(--slate-50) 100%);
}

/* Main Content */
.main-content {
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 64px 20px;
    margin-left: 0;
}

.card {
    width: 100%;
    max-width: 500px;
    background-color: var(--surface);
    border-radius: 20px;
    border: 1px solid var(--slate-200);
    box-shadow: 0 10px 40px -10px rgba(5, 150, 105, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 40px;
}

.card-header {
    margin-bottom: 32px;
    text-align: center;
}

.header-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    height: 56px;
    background-color: var(--emerald-50);
    color: var(--emerald-600);
    border-radius: 16px;
    margin-bottom: 20px;
}

.header-icon span {
    font-size: 28px;
}

.card-title {
    font-size: 28px;
    line-height: 36px;
    letter-spacing: -0.02em;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 12px;
}

.card-subtitle {
    font-size: 15px;
    line-height: 24px;
    font-weight: 400;
    color: var(--slate-500);
}

/* Form Layout */
.form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 480px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

/* Inputs & Floating Labels */
.relative-input {
    position: relative;
    width: 100%;
    background-color: var(--surface);
}

.form-input {
    width: 100%;
    height: 60px;
    padding: 24px 16px 8px 16px;
    box-sizing: border-box;
    background-color: transparent;
    border: 1px solid var(--slate-300);
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    color: var(--slate-900);
    outline: none;
    transition: all 200ms ease;
}

.form-input:hover:not(:disabled) {
    border-color: var(--slate-400);
}

.form-input:focus {
    border-color: var(--emerald-600);
    box-shadow: 0 0 0 4px var(--emerald-50);
}

.bvn-input {
    letter-spacing: 0.15em;
    font-family: monospace;
    font-size: 18px;
}

/* Floating Label Animation */
.floating-label {
    position: absolute;
    left: 16px;
    top: 20px;
    font-size: 16px;
    color: var(--slate-500);
    pointer-events: none;
    transform-origin: left top;
    transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

.form-input:focus ~ .floating-label,
.form-input:not(:placeholder-shown) ~ .floating-label {
    transform: translateY(-10px) scale(0.75);
    color: var(--slate-500);
    font-weight: 500;
}

.form-input:focus ~ .floating-label {
    color: var(--emerald-600);
}

/* Error States */
.relative-input.has-error .form-input {
    border-color: var(--error);
    background-color: var(--error-bg);
}

.relative-input.has-error .form-input:focus {
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.relative-input.has-error .form-input:focus ~ .floating-label,
.relative-input.has-error .form-input:not(:placeholder-shown) ~ .floating-label {
    color: var(--error);
}

.error-message {
    font-size: 13px;
    font-weight: 500;
    color: var(--error);
    padding-left: 4px;
}

/* Visibility Toggle */
.toggle-visibility-btn {
    position: absolute;
    right: 0px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--slate-400);
    padding: 8px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 200ms ease;
}

.toggle-visibility-btn:hover {
    color: var(--slate-700);
    background-color: var(--slate-100);
}

/* Trust Indicators Box */
.trust-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    background-color: var(--emerald-50);
    border: 1px solid var(--emerald-100);
    border-radius: 12px;
    padding: 16px;
    margin: 8px 0;
}

.trust-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--emerald-700);
}

.trust-item span.material-symbols-outlined {
    font-size: 20px;
}

.trust-text {
    font-size: 13px;
    font-weight: 600;
}

.trust-divider {
    width: 1px;
    height: 24px;
    background-color: var(--emerald-100);
}

/* Primary Button */
.submit-btn {
    width: 100%;
    height: 56px;
    background-color: var(--emerald-600);
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 12px; /* Switched from pill to rounded rect for fintech feel */
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
    transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

.submit-btn:hover:not(:disabled) {
    background-color: var(--emerald-700);
    box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
    transform: translateY(-1px);
}

.submit-btn:active:not(:disabled) {
    transform: translateY(1px);
    box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
}

.submit-btn:disabled {
    background-color: var(--slate-300);
    box-shadow: none;
    cursor: not-allowed;
    color: var(--slate-500);
}

/* Loading Spinner */
.loading-spinner {
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 3px solid #fff;
    width: 20px;
    height: 20px;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Disclaimer section */
.disclaimer-section {
    margin-top: 32px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background-color: var(--slate-50);
    padding: 16px;
    border-radius: 12px;
}

.info-icon {
    color: var(--slate-400);
    font-size: 20px;
    flex-shrink: 0;
}

.disclaimer-text {
    font-size: 13px;
    line-height: 1.6;
    font-weight: 500;
    color: var(--slate-500);
    margin: 0;
}

/* Footer Section */
.footer {
    width: 100%;
    padding: 24px 20px;
}

.footer-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    max-width: 1200px;
    margin: 0 auto;
}

@media (min-width: 768px) {
    .footer-content {
        flex-direction: row;
        justify-content: space-between;
    }
}

.footer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.footer-brand {
    font-size: 14px;
    font-weight: 700;
    color: var(--emerald-600);
}

.footer-copyright {
    font-size: 13px;
    color: var(--slate-500);
    margin: 0;
}

.footer-links {
    display: flex;
    gap: 24px;
}

.footer-link {
    font-size: 13px;
    font-weight: 500;
    color: var(--slate-500);
    text-decoration: none;
    transition: color 200ms ease;
}

.footer-link:hover {
    color: var(--emerald-600);
}
</style>

