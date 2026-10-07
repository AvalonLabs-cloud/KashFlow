<template>
    <div class="registration-container">
        <main class="registration-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-wrapper">
                    <span class="brand-name">Create your account</span>
                </div>
            </div>

            <!-- Registration Form -->
            <form @submit.prevent="handleSubmit" class="registration-form">
                <!-- Email Field -->
                <div class="form-group">
                    <label class="input-label" for="email">Email Address</label>
                    <div class="input-wrapper">
                        <span class="material-symbols-outlined input-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-lock-icon lucide-lock">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg></span>
                        <input :readonly="loading" id="email" v-model="form.email" type="email" class="form-input"
                            :class="{
                                'input-error': errors.email,
                                'input-valid': errors.email,
                            }" placeholder="name@exclusive.com" required @blur="touchedEmail()" />
                    </div>
                    <div v-show="errors.email" class="form-error">
                        <span class="form-error__text">
                            {{ errors.email }}
                        </span>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="input-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="material-symbols-outlined input-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-key-round-icon lucide-key-round">
                                <path
                                    d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z" />
                                <circle cx="16.5" cy="7.5" r=".5" fill="currentColor" />
                            </svg></span>
                        <input :readonly="loading" id="password" v-model="form.password"
                            :type="showPassword ? 'text' : 'password'" class="form-input" :class="{
                                'input-error': errors.password,
                                'input-success': validated.password,
                            }" placeholder="••••••••" required @input="
                                touched;
                            validatePassword(form.password);
                            " autocomplete="current-password" />
                        <span @click="generateStrongPassword" class="password-suggestion">
                            Suggest a strong password
                        </span>

                        <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                            <span class="material-symbols-outlined" v-show="showPassword"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg></span>
                            <span class="material-symbols-outlined" v-show="!showPassword"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye-off-icon lucide-eye-off">
                                    <path
                                        d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49" />
                                    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                                    <path
                                        d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143" />
                                    <path d="m2 2 20 20" />
                                </svg></span>
                        </button>
                    </div>

                    <!-- Password Strength Meter -->
                    <div class="strength-meter">
                        <div v-for="n in 4" :key="n" class="strength-bar" :class="[
                            passwordStrength >= n
                                ? strengthClassName
                                : 'bar-empty',
                        ]"></div>
                    </div>

                    <!-- Validation List -->
                    <ul class="validation-list">
                        <li v-for="(check, key) in passwordChecks" :key="key" class="validation-item"
                            :class="{ 'item-valid': check.valid }">
                            <span class="material-symbols-outlined validation-icon"><svg
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-check-icon lucide-check">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg></span>
                            {{ check.label }}
                        </li>
                    </ul>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label class="input-label" for="confirm">Confirm Password</label>
                    <div class="input-wrapper">
                        <span class="material-symbols-outlined input-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-shield-icon lucide-shield">
                                <path
                                    d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                            </svg></span>
                        <input :readonly="form.processing" id="confirm" v-model="form.password_confirmation" type="password"
                            class="form-input" :class="{
                                'input-error': errors.confirmPassword,
                                'input-success': validated.confirmPassword,
                            }" placeholder="••••••••" required />
                    </div>
                    <div v-show="$page.props.flash.error" class="form-error">
                        <span class="form-error__text">
                            {{ errors.confirmPassword }}
                        </span>
                    </div>
                </div>

                <div v-show="$page.props.flash.error" class="form-error">
                    <span class="form-error__text">
                        <span>something went wrong in the account creation please
                            try again later</span>
                    </span>
                </div>

                <div v-if="$page.props.errors.password || $page.props.errors.email" class="form-error">
                    <span class="form-error__text">
                        <span>something went wrong in the account creation </span>
                    </span>
                </div>

                <!-- Register Button -->
                <button type="submit" class="submit-button">
                    <div>
                        <span v-show="!form.processing">Register</span>
                        <Spinner v-show="form.processing" />
                    </div>
                </button>
            </form>
        </main>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import Spinner from '@/components/Spinner.vue';
import { phaseOneValidator } from '@/validators/phaseOneValidators';
const {
    validatePassword,
    errors,
    validated,
} = phaseOneValidator();

const form = useForm({
    email: '',
    password: '',
    password_confirmation: '',
});

const touched = reactive({
    email: false,
    password: false,
    password_confirmation: false,
});

const showPassword = ref(false);
const isSubmitting = ref(false);

// Password Check Logic
const passwordChecks = computed(() => ({
    length: { label: '8+ characters', valid: form.password.length >= 8 },
    upper: {
        label: 'Upper & Lower',
        valid: /[A-Z]/.test(form.password) && /[a-z]/.test(form.password),
    },
    number: { label: 'Numeric digit', valid: /[0-9]/.test(form.password) },
    special: {
        label: 'Special char',
        valid: /[!@#$%^&*(),.?":{}|<>]/.test(form.password),
    },
}));

const passwordStrength = computed(() => {
    let score = 0;

    if (passwordChecks.value.length.valid) {
        score++;
    }

    if (passwordChecks.value.upper.valid) {
        score++;
    }

    if (passwordChecks.value.number.valid) {
        score++;
    }

    if (passwordChecks.value.special.valid) {
        score++;
    }

    return score;
});

const strengthClassName = computed(() => {
    switch (passwordStrength.value) {
        case 1:
            return 'bar-error';
        case 2:
            return 'bar-warning';
        case 3:
            return 'bar-info';
        case 4:
            return 'bar-success';
        default:
            return '';
    }
});

const touchedEmail = () => {
    touched.email = true;
};

// const validatePasswordField = computed(() => {
//   return touched.password ? validatePassword(form.password) : undefined
// })

const handleSubmit = async () => {
    touched.email = true;
    touched.password = true;
    touched.password_confirmation = true;

    // validateEmail(form.email);
    // validatePassword(form.password);
    // validateConfirmPassword(form.password, form.password_confirmation);

    // if (validated.email && validated.password && validated.confirmPassword) {
     try {
        await form.post('/onboarding/phase_one');
        } catch (error) {
            console.error('Registration failed:', error);
            isSubmitting.value = false;
        }
    // }
};

const generateStrongPassword = () => {
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const numbers = '0123456789';
    const special = '@#$';

    const getRandom = (characters) => {
        return characters[Math.floor(Math.random() * characters.length)];
    };

    const password = [
        getRandom(uppercase),
        getRandom(lowercase),
        getRandom(numbers),
        getRandom(special),
    ];

    const allCharacters = uppercase + lowercase + numbers + special;

    while (password.length < 12) {
        password.push(getRandom(allCharacters));
    }

    // Shuffle
    password.sort(() => Math.random() - 0.5);

    form.password = password.join('');
    form.password_confirmation = password.join('');
};
</script>

<style scoped>
:component {
    /* --color-primary: #00b01d; */
    --color-on-primary: #ffffff;
    --color-secondary: #0dff00;
    --color-background: #f7f9fb;
    --color-surface: #ffffff;
    --color-on-surface: #191c1e;
    --color-on-surface-variant: #45464d;
    --color-outline: #76777d;
    --color-outline-variant: #c6c6cd;
    --color-error: #ba1a1a;
    --color-success: #009668;
    --color-warning: #f59e0b;
    --color-info: #21e445;
    --color-container-highest: #e0e3e5;

    --spacing-stack-lg: 48px;
    --spacing-stack-md: 24px;
    --spacing-stack-sm: 12px;
    --spacing-stack-xs: 4px;
    --radius-lg: 0.5rem;
    --radius-xl: 0.75rem;
    --shadow-standard:
        0 8px 30px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.03);
    --shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.06);
}

/* Base Layout */
.registration-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background-color: var(--color-background);
    padding: 16px;
    font-family: 'Inter', sans-serif;
}

.registration-card {
    position: relative;
    width: 100%;
    max-width: 480px;
    background-color: var(--color-surface);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-standard);
    padding: 40px;
    transition: all 0.3s ease;
}

.registration-card:hover {
    box-shadow: var(--shadow-hover);
}

/* Logo Section */
.logo-section {
    display: flex;
    justify-content: center;
    margin-bottom: var(--spacing-stack-md);
    margin: 20px;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.logo-icon {
    color: var(--color-secondary);
    font-size: 32px;
}

.brand-name {
    font-size: 24px;
    font-weight: 600;
    color: #2ab423;
    letter-spacing: -0.02em;
}

/* Header */
.card-header {
    text-align: center;
    margin-bottom: var(--spacing-stack-md);
}

.header-title {
    font-size: 32px;
    font-weight: 600;
    color: var(--color-on-surface);
    margin-bottom: var(--spacing-stack-xs);
    line-height: 1.2;
}

.header-subtitle {
    font-size: 16px;
    color: var(--color-on-surface-variant);
}

/* Form Styles */
.registration-form {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-stack-md);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-stack-xs);
}

.input-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--color-on-surface);
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--color-outline);
    transition: color 0.2s ease;
    pointer-events: none;
}

.input-wrapper:focus-within .input-icon {
    color: var(--color-secondary);
}

.form-input {
    width: 100%;
    padding: 12px 16px 12px 48px;
    background-color: var(--color-background);
    border: 1px solid var(--color-outline-variant);
    border-radius: var(--radius-lg);
    font-size: 16px;
    outline: none;
    transition: all 0.2s ease;
}

.form-input:focus {
    border-color: var(--color-secondary);
    box-shadow: 0 0 0 4px rgba(0, 88, 190, 0.05);
}

.input-error {
    border-color: var(--color-error) !important;
}

.input-error:focus {
    box-shadow: 0 0 0 4px rgba(186, 26, 26, 0.05) !important;
}

.input-valid {
    border-color: var(--color-success);
}

.toggle-password {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--color-outline);
    cursor: pointer;
    padding: 0;
}

.toggle-password:hover {
    color: var(--color-on-surface);
}

/* Security/Validation items */
.security-hint {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: var(--color-on-surface-variant);
}

.hint-icon {
    font-size: 14px;
}

.strength-meter {
    display: flex;
    gap: 6px;
    height: 4px;
    margin-top: 8px;
}

.strength-bar {
    flex: 1;
    border-radius: 2px;
    transition: all 0.4s ease;
}

.bar-empty {
    background-color: var(--color-container-highest);
}

.bar-error {
    background-color: var(--color-error);
}

.bar-warning {
    background-color: var(--color-warning);
}

.bar-info {
    background-color: var(--color-info);
}

.bar-success {
    background-color: var(--color-success);
}

.validation-list {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
    padding-top: 8px;
    list-style: none;
    margin: 0;
}

.validation-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--color-on-surface-variant);
    transition: color 0.2s ease;
}

.validation-icon {
    font-size: 16px;
}

.item-valid {
    color: var(--color-success);
}

/* Submit Button */
.submit-button {
    width: 100%;
    background-color: #21e445;
    color: var(--color-on-primary);
    padding: 16px;
    border-radius: var(--radius-lg);
    font-weight: 500;
    font-size: 16px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
    transition: all 0.2s ease;
}

.submit-button:hover:not(:disabled) {
    transform: scale(1.01);
}

.submit-button:active:not(:disabled) {
    transform: scale(0.99);
}

.submit-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

/* Loading Spinner */
.loading-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.spinner {
    width: 20px;
    height: 20px;
    animation: rotate 2s linear infinite;
}

.spinner-track {
    opacity: 0.25;
}

.spinner-head {
    opacity: 0.75;
}

@keyframes rotate {
    100% {
        transform: rotate(360deg);
    }
}

/* Success Overlay */
.success-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(247, 249, 251, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    padding: 24px;
    text-align: center;
}

.success-content {
    max-width: 400px;
    animation: zoomIn 0.5s ease-out;
}

.success-large-icon {
    font-size: 80px;
    color: var(--color-success);
    margin-bottom: var(--spacing-stack-md);
    font-variation-settings: 'FILL' 1;
}

.success-title {
    font-size: 32px;
    font-weight: 600;
    margin-bottom: var(--spacing-stack-xs);
}

.success-message {
    font-size: 16px;
    color: var(--color-on-surface-variant);
}

@keyframes zoomIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Footer */
.registration-footer {
    margin-top: var(--spacing-stack-md);
    text-align: center;
}

.footer-legal {
    font-size: 12px;
    color: var(--color-on-surface-variant);
    max-width: 320px;
    margin: 0 auto 16px;
    line-height: 1.6;
}

.highlight {
    color: var(--color-primary);
    font-weight: 700;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 24px;
}

.footer-link {
    font-size: 12px;
    color: var(--color-secondary);
    text-decoration: none;
}

.footer-link:hover {
    text-decoration: underline;
}

/* Responsiveness */
@media (max-width: 640px) {
    .registration-card {
        padding: 32px 24px;
    }

    .header-title {
        font-size: 24px;
    }

    .validation-list {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 1440px) {
    .registration-card {
        max-width: 520px;
    }
}

.form-error {
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;

    padding: 8px 10px;
    border-radius: 8px;

    background: #fff5f5;
    border: 1px solid #fecaca;

    animation: fadeIn 0.15s ease-in-out;
}

.form-error__text {
    font-size: 13px;
    font-weight: 500;
    color: #dc2626;
    /* strong red but not neon */
    line-height: 1.3;
}

.password-suggestion {
    display: inline-block;
    margin-top: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #2563eb;
    cursor: pointer;
    transition: color 0.2s ease;
}

.password-suggestion:hover {
    color: #1d4ed8;
}
</style>
