<template>
  <div class="vault-app font-sans antialiased bg-surface-container-lowest text-on-surface">
    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-surface-container-lowest/90 backdrop-blur-md shadow-header">
      <div class="h-16 max-w-7xl mx-auto px-6 lg:px-12 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-xl bg-primary flex items-center justify-center text-on-primary font-headline-sm text-headline-sm font-semibold tracking-tight">
              V
            </div>
            <span class="font-headline-sm text-headline-sm font-semibold tracking-tight text-on-surface">VAULT</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="w-full pt-16 bg-surface-container-lowest">
      <div class="flex flex-col w-full">
        <div class="relative w-full py-16 sm:py-24 lg:py-32 flex flex-col items-center justify-center px-4 sm:px-6">
          <div class="w-full max-w-[480px] flex flex-col">

            <!-- Title & Description -->
            <div class="flex flex-col gap-3 mb-10">
              <h1 class="font-headline-lg text-headline-lg text-on-surface font-semibold tracking-tight">
                What’s your full name?
              </h1>
              <p class="font-body-lg text-body-lg text-secondary">
                Enter your full name if you’d like us to personalize your account.
              </p>
            </div>

            <!-- Onboarding Form -->
            <form class="flex flex-col gap-6" @submit.prevent="handleSubmit">
              <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                  <label class="font-label-md text-label-md font-medium text-on-surface" for="full-name-input">
                    Full name
                  </label>
                  <span class="font-label-sm text-label-sm text-secondary tracking-normal font-normal">
                    (Optional)
                  </span>
                </div>

                <div class="relative group">
                  <input
                    id="full-name-input"
                    v-model="fullName"
                    type="text"
                    autocomplete="name"
                    placeholder="Enter your full name"
                    class="w-full h-14 px-4 rounded-xl bg-surface-container-lowest text-on-surface font-body-lg text-body-lg placeholder:text-secondary/50 shadow-input transition-all duration-200 outline-none focus:bg-surface-container-lowest"
                    @focus="isFocused = true"
                    @blur="isFocused = false"
                  />
                  <div
                    class="pointer-events-none absolute inset-0 rounded-xl transition-opacity duration-200 shadow-focus"
                    :class="isFocused ? 'opacity-100' : 'opacity-0'"
                  ></div>
                </div>
              </div>
              <!-- Submit Button -->
              <button
                type="submit"
                class="w-full h-14 rounded-xl bg-primary text-on-primary font-headline-sm text-headline-sm font-medium flex items-center justify-center gap-2 shadow-sm transition-all duration-200 hover:bg-primary-container active:scale-[0.99] cursor-pointer group"
                :class="{ 'opacity-90': isSubmitting }"
              >
                <span>Continue</span>
                <!-- <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-hover:translate-x-0.5">
                  arrow_forward
                </span> -->
              </button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script lang="ts" setup>
import { router } from "@inertiajs/vue3";
import { ref } from 'vue'

const fullName = ref('')
const isFocused = ref(false)
const isSubmitting = ref(false)

const handleSubmit = () => {
   router.post('/onboarding/complete', {
    fullName: fullName.value,
   });
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200');

/* Custom Color Tokens mapped to CSS Variables */
.vault-app {
  --color-primary: #006948;
  --color-primary-container: #00855d;
  --color-on-primary: #ffffff;
  --color-secondary: #565e74;
  --color-on-surface: #0b1c30;
  --color-on-surface-variant: #3d4a42;
  --color-surface-container: #e5eeff;
  --color-surface-container-low: #eff4ff;
  --color-surface-container-lowest: #ffffff;
  
  font-family: 'Inter', sans-serif;
  color: var(--color-on-surface);
  background-color: var(--color-surface-container-lowest);
  min-height: 100vh;
}

/* Color Utility Helpers */
.bg-primary { background-color: var(--color-primary); }
.bg-primary-container { background-color: var(--color-primary-container); }
.text-primary { color: var(--color-primary); }
.text-on-primary { color: var(--color-on-primary); }
.text-secondary { color: var(--color-secondary); }
.text-on-surface { color: var(--color-on-surface); }
.text-on-surface-variant { color: var(--color-on-surface-variant); }
.bg-surface-container { background-color: var(--color-surface-container); }
.bg-surface-container-low { background-color: var(--color-surface-container-low); }
.bg-surface-container-lowest { background-color: var(--color-surface-container-lowest); }

/* Typography Rules matching Tailwind Theme */
.font-headline-lg {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 32px;
  line-height: 40px;
  letter-spacing: -0.02em;
  font-weight: 600;
}

.font-headline-sm {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 18px;
  line-height: 24px;
  letter-spacing: -0.01em;
}

.font-body-lg {
  font-family: 'Inter', sans-serif;
  font-size: 16px;
  line-height: 26px;
  letter-spacing: -0.005em;
}

.font-body-sm {
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  line-height: 18px;
  letter-spacing: 0em;
}

.font-label-md {
  font-family: 'Inter', sans-serif;
  font-size: 13px;
  line-height: 18px;
  letter-spacing: 0.01em;
}

.font-label-sm {
  font-family: 'Inter', sans-serif;
  font-size: 11px;
  line-height: 14px;
  letter-spacing: 0.04em;
  font-weight: 600;
}

/* Shadows and Custom Focus Indicators */
.shadow-header {
  box-shadow: 0 1px 8px rgba(15, 23, 42, 0.03);
}

.shadow-input {
  box-shadow: inset 0 0 0 1px #e5eeff, 0 1px 2px 0 rgba(11, 28, 48, 0.04);
}

.shadow-focus {
  box-shadow: inset 0 0 0 2px #006948, 0 0 0 4px rgba(0, 105, 72, 0.08);
}

.fill-icon {
  font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* Material Symbols Adjustments */
.material-symbols-outlined {
  font-family: 'Material Symbols Outlined';
  font-style: normal;
  display: inline-block;
  line-height: 1;
  text-transform: none;
  letter-spacing: normal;
  word-wrap: normal;
  white-space: nowrap;
  direction: ltr;
}
</style>
