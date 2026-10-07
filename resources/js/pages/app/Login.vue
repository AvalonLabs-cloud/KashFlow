<template>
  <div class="login-wrapper">
    <div class="login-card">
      <!-- Header -->
      <div class="header">
        <div class="logo"></div>
        <h1 class="title">Sign in to your account</h1>
        <p class="subtitle">Welcome back to your secure dashboard</p>
      </div>

      <!-- Success State -->
      <div v-if="isSuccess" class="success-state">
        <div class="success-icon">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h2>Authentication Successful</h2>
        <p>Securely redirecting you to your dashboard...</p>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="handleSubmit" class="form" novalidate>
        
        <!-- General Auth Error (Incorrect Password) -->
        <div v-if="$page.props.errors.email" class="alert alert-error" role="alert">
          <svg class="alert-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span>{{ $page.props.errors.email }}</span>
        </div>

        <!-- Email Field -->
        <div class="form-group">
          <label for="email" class="label">Email address</label>
          <div class="input-wrapper">
            <input 
              id="email"
              v-model="form.email"
              type="email" 
              class="input"
              :class="{ 'input-error': errors.email }"
              placeholder="Enter your email address"
              :disabled="isLoading"
              autocomplete="email"
              @input="clearError('email')"
            />
          </div>
          <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <div class="label-row">
            <label for="password" class="label">Password</label>
            <a href="#" class="forgot-link" tabindex="-1">Forgot password?</a>
          </div>
          <div class="input-wrapper">
            <input 
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'" 
              class="input"
              :class="{ 'input-error': errors.password }"
              placeholder="Enter your password"
              :disabled="isLoading"
              autocomplete="current-password"
              @input="clearError('password')"
            />
            <button 
              type="button" 
              class="toggle-password" 
              @click="showPassword = !showPassword"
              aria-label="Toggle password visibility"
              tabindex="-1"
            >
              <svg v-if="showPassword" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
              </svg>
              <svg v-else fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>
          <span v-if="errors.password" class="error-text">{{ errors.password }}</span>
        </div>

        <!-- Submit Button -->
        <button 
          type="submit" 
          class="submit-btn" 
          :disabled="isLoading"
        >
          <span v-if="!isLoading">Sign in</span>
          <span v-else class="loader-wrapper">
            <svg class="spinner" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Authenticating...
          </span>
        </button>
      </form>

      <!-- Footer -->
      <div @click="signUpPage" v-if="!isSuccess" class="footer">
        <p>Don't have an account? <a href="#" class="signup-link">Sign up</a></p>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm  , router} from '@inertiajs/vue3'
import { ref, reactive } from 'vue';
import { z } from 'zod';


const form = useForm({
    email: '',
    password: '',
})

const showPassword = ref(false);
const isLoading = ref(false);
const isSuccess = ref(false);

const errors = reactive({
  email: '',
  password: '',
  general: '',
});

// Zod Validation Schema
const loginSchema = z.object({
  email: z.string().min(1, 'Email address is required').email('Please enter a valid email address'),
  password: z.string().min(1, 'Password is required'),
});

const clearError = (field: 'email' | 'password') => {
  errors[field] = '';
  errors.general = '';
};

const validateForm = (): boolean => {
  errors.email = '';
  errors.password = '';
  errors.general = '';

  const result = loginSchema.safeParse(form);
  
  if (!result.success) {
    result.error.issues.forEach((issue) => {
      const field = issue.path[0] as keyof typeof errors;

      if (field) {
        errors[field] = issue.message;
      }
    });

    return false;
  }

  return true;
};

const signUpPage = () => {
    router.get('/onboarding/phase_one')
}

const handleSubmit = () => {
    // if (!validateForm() || form.processing) {
    //     return;
    // }

    form.post('/login', {
        preserveScroll: true,

        onStart: () => {
            isLoading.value = true;
        },

        onSuccess: () => {
            isSuccess.value = true;
        },

        onError: (errors) => {
            if (errors.email) {
                errors.general = errors.email;
            } else {
                errors.general = 'Incorrect email or password. Please try again.';
            }
        },

        onFinish: () => {
            isLoading.value = false;
        },
    });
};
</script>

<style scoped>
/* 
  Design System Variables 
  Refined green accent: #059669 (emerald-600) for a trustworthy, premium financial feel.
*/
:root {
  --color-primary: #059669;
  --color-primary-hover: #047857;
  --color-primary-focus: rgba(5, 150, 105, 0.15);
  --color-error: #dc2626;
  --color-error-bg: #fef2f2;
  --color-surface: #ffffff;
  --color-background: #f9fafb;
  --color-text-main: #111827;
  --color-text-muted: #6b7280;
  --color-border: #e5e7eb;
  
  --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
  --shadow-input: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  
  --font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

* {
  box-sizing: border-box;
}

.login-wrapper {
  display: flex;
  min-height: 100vh;
  align-items: center;
  justify-content: center;
  background-color: var(--color-background);
  padding: 1.5rem;
  font-family: var(--font-family);
  color: var(--color-text-main);
  -webkit-font-smoothing: antialiased;
}

.login-card {
  width: 100%;
  max-width: 420px;
  background-color: var(--color-surface);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-card);
  padding: 2.5rem 2rem;
  border: 1px solid rgba(229, 231, 235, 0.5);
}

/* Header */
.header {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  width: 48px;
  height: 48px;
  background-color: var(--color-primary);
  border-radius: var(--radius-md);
  margin: 0 auto 1.25rem;
  /* Simple geometric shape for a modern fintech logo placeholder */
  mask: linear-gradient(135deg, #000 40%, transparent 40%, transparent 60%, #000 60%);
  -webkit-mask: linear-gradient(135deg, #000 40%, transparent 40%, transparent 60%, #000 60%);
}

.title {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
  letter-spacing: -0.025em;
}

.subtitle {
  font-size: 0.9375rem;
  color: var(--color-text-muted);
  margin: 0;
}

/* Form Elements */
.form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.375rem;
}

.label-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  font-size: 0.9375rem;
  font-family: inherit;
  color: var(--color-text-main);
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  box-shadow: var(--shadow-input);
  transition: all 0.2s ease;
}

.input::placeholder {
  color: #9ca3af;
}

.input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-primary-focus);
}

.input:disabled {
  background-color: #f3f4f6;
  color: #9ca3af;
  cursor: not-allowed;
}

.input-error {
  border-color: var(--color-error);
}

.input-error:focus {
  border-color: var(--color-error);
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* Password Toggle */
.toggle-password {
  position: absolute;
  right: 0.75rem;
  background: none;
  border: none;
  padding: 0;
  color: #9ca3af;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.toggle-password:hover {
  color: #4b5563;
}

.toggle-password svg {
  width: 1.25rem;
  height: 1.25rem;
}

/* Links */
.forgot-link, .signup-link {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--color-primary);
  text-decoration: none;
  transition: color 0.2s ease;
}

.forgot-link:hover, .signup-link:hover {
  color: var(--color-primary-hover);
}

/* Validation & Errors */
.error-text {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--color-error);
  margin-top: 0.25rem;
}

.alert {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.875rem;
  border-radius: var(--radius-sm);
  font-size: 0.875rem;
  font-weight: 500;
}

.alert-error {
  background-color:#ff7a7a;
  color: var(--color-error);
  border: 1px solid rgba(220, 38, 38, 0.2);
}

.alert-icon {
  width: 1.25rem;
  height: 1.25rem;
  flex-shrink: 0;
}

/* Submit Button */
.submit-btn {
  margin-top: 0.5rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background-color: var(--color-primary);
  color: white;
  font-size: 0.9375rem;
  font-weight: 500;
  font-family: inherit;
  border: 1px solid transparent;
  border-radius: var(--radius-sm);
  cursor: pointer;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
  display: flex;
  justify-content: center;
  align-items: center;
}

.submit-btn:hover:not(:disabled) {
  background-color: var(--color-primary-hover);
}

.submit-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px var(--color-primary-focus);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Loading Spinner */
.loader-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.spinner {
  width: 1.125rem;
  height: 1.125rem;
  animation: spin 1s linear infinite;
}

.spinner-track {
  opacity: 0.25;
}

.spinner-head {
  opacity: 0.75;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Footer */
.footer {
  margin-top: 2rem;
  text-align: center;
  font-size: 0.875rem;
  color: var(--color-text-muted);
}

/* Success State */
.success-state {
  text-align: center;
  padding: 2rem 0;
}

.success-icon {
  width: 3.5rem;
  height: 3.5rem;
  background-color: #d1fae5;
  color: var(--color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.25rem;
}

.success-icon svg {
  width: 2rem;
  height: 2rem;
}

.success-state h2 {
  font-size: 1.25rem;
  font-weight: 600;
  margin: 0 0 0.5rem;
  color: var(--color-text-main);
}

.success-state p {
  font-size: 0.9375rem;
  color: var(--color-text-muted);
  margin: 0;
}

/* Responsive Adjustments */
@media (max-width: 480px) {
  .login-card {
    padding: 2rem 1.5rem;
    box-shadow: none;
    border: none;
    background-color: transparent;
  }
  
  .login-wrapper {
    background-color: var(--color-surface);
    padding: 1rem;
    align-items: flex-start;
    padding-top: 10vh;
  }
}
</style>