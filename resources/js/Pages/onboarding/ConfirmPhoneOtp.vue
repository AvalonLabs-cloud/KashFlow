<template>
    <div
        class="identity-verification-container min-h-screen flex items-center justify-center p-md overflow-hidden relative bg-surface">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] rounded-full bg-primary-container-20 floating-glow">
        </div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] rounded-full bg-tertiary-container-20 floating-glow">
        </div>

        <main class="relative z-10 w-full max-w-[480px] px-lg">

            <div v-if="isLoading" class="flex flex-col items-center">
                <div class="w-12 h-12 rounded-full shimmer mb-lg"></div>
                <div class="w-48 h-8 rounded shimmer mb-md"></div>
                <div class="w-64 h-4 rounded shimmer mb-xl"></div>
                <div class="grid grid-cols-6 gap-sm mb-xl w-full">
                    <div v-for="n in 6" :key="'skel-' + n" class="aspect-square w-full rounded shimmer"></div>
                </div>
                <div class="w-full h-12 rounded-xl shimmer"></div>
            </div>

            <div v-else class="flex flex-col items-center transition-all duration-700" :class="[
                isMounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8',
                hasRingEffect ? 'ring-4 ring-primary-10 rounded-2xl p-md' : ''
            ]">
                <div class="flex items-center gap-xs mb-2xl">
                    <div
                        class="w-12 h-12 bg-primary-container rounded-lg flex items-center justify-center text-on-primary shadow-sm">
                        <span class="material-symbols-outlined text-[28px] fill-icon">account_balance_wallet</span>
                    </div>
                    <span class="font-headline-md text-[28px] text-primary tracking-tight font-bold">FintechPay</span>
                </div>

                <h1 class="font-headline-lg text-display-lg text-on-background text-center mb-md font-bold">Verify Your
                    Identity</h1>
                <p class="font-body-lg text-body-lg text-secondary text-center mb-3xl max-w-[360px]">
                    We've sent a 6-digit code to <span class="font-semibold text-on-background">ja***@example.com</span>
                </p>

                <div class="grid grid-cols-6 gap-sm w-full mb-xl" :class="{ 'animate-shake': isShaking }">
                    <input v-for="(digit, index) in otp" :key="index" ref="otpInputs" v-model="otp[index]"
                        class="otp-input w-full aspect-square text-center font-headline-md text-headline-md bg-surface-container-lowest border border-outline-variant rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary-container"
                        :class="{ 'border-error text-error': hasError }" inputmode="numeric" maxlength="1" type="text"
                        @input="handleInput($event, index)" @keydown="handleKeyDown($event, index)"
                        @paste="handlePaste" />
                </div>

                <div
                    class="w-full h-8 flex items-center justify-center mb-md overflow-hidden transition-all duration-300">
                    <div v-if="hasError" class="flex items-center gap-xs text-error font-label-md text-label-md">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        Invalid verification code
                    </div>
                    <div v-if="isVerified" class="flex items-center gap-xs text-primary font-label-md text-label-md">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Verification successful
                    </div>
                </div>

                <button
                    class="w-full h-14 font-semibold text-body-lg rounded-xl flex items-center justify-center gap-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed hover:opacity-90 active:scale-[0.98] shadow-sm"
                    :class="isVerified ? 'bg-primary text-on-primary' : 'bg-primary-container text-on-primary-container'"
                    :disabled="!isOtpComplete || isVerifying || isVerified" @click="verifyCode">
                    <span>{{ buttonText }}</span>
                    <div v-if="isVerifying"
                        class="w-5 h-5 border-2 border-current border-t-transparent rounded-full animate-spin"></div>
                </button>

                <div class="mt-xl flex flex-col items-center gap-sm">
                    <div v-if="false" class="font-label-md text-label-md text-secondary">
                        Resend code in <span class="font-bold text-on-background">{{ formattedTime }}</span>
                    </div>
                    <button v-else class="font-label-md text-label-md text-primary hover:underline transition-all"
                        :class="{ 'opacity-50': isResending }" :disabled="isResending" @click="resendCode">
                        Resend Code
                    </button>
                </div>
            </div>

        </main>
    </div>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios';
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'



// UI Loading & Transition Flags
const isLoading = ref(true)
const isMounted = ref(false)

// OTP State
const otp = ref(['', '', '', '', '', ''])
const otpInputs = ref([])
const isVerifying = ref(false)
const isVerified = ref(false)
const hasError = ref(false)
const isShaking = ref(false)
const hasRingEffect = ref(false)

// Timer & Resend State
const timeLeft = ref(60)
const isResending = ref(false)
let timerInterval = null

// Computed properties
const isOtpComplete = computed(() => otp.value.every(char => char.length === 1))

const buttonText = computed(() => {
    if (isVerifying.value) {
        return 'Verifying...'
    }

    if (isVerified.value) {
        return 'Verified'
    }

    return 'Verify Code'
})

const formattedTime = computed(() => {
    const minutes = Math.floor(timeLeft.value / 60)
    const seconds = timeLeft.value % 60

    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
})

// Timer Execution
const startTimer = () => {
    clearInterval(timerInterval)
    timeLeft.value = 120
    timerInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value--
        } else {
            clearInterval(timerInterval)
        }
    }, 1000)
}

// Initial Mock Skeleton loading sequence
onMounted(() => {
    // Load Material Icons Dynamically if they aren't globally ready
    if (!document.getElementById('material-icons-link')) {
        const link = document.createElement('link')
        link.id = 'material-icons-link'
        link.rel = 'stylesheet'
        link.href = 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap'
        document.head.appendChild(link)
    }

    setTimeout(() => {
        isLoading.value = false
        nextTick(() => {
            isMounted.value = true
        })
    }, 1200)

    startTimer()
})

onBeforeUnmount(() => {
    clearInterval(timerInterval)
})

// OTP Navigation Methods
const handleInput = (e, index) => {
    const value = e.target.value
    // Retain only the last character if multiple characters are keyed

    if (value.length > 0) {
        otp.value[index] = value.slice(-1)

        if (index < otp.value.length - 1) {
            otpInputs.value[index + 1]?.focus()
        }
    }
    // Clear layout validation configurations on edits

    clearValidationState()
}

const handleKeyDown = (e, index) => {
    if (e.key === 'Backspace') {
        if (otp.value[index] === '' && index > 0) {
            otp.value[index - 1] = ''
            otpInputs.value[index - 1]?.focus()
        } else {
            otp.value[index] = ''
        }

        clearValidationState()
    } else if (e.key === 'ArrowLeft' && index > 0) {
        otpInputs.value[index - 1]?.focus()
    } else if (e.key === 'ArrowRight' && index < otp.value.length - 1) {
        otpInputs.value[index + 1]?.focus()
    }
}

const handlePaste = (e) => {
    e.preventDefault()
    const pastedData = e.clipboardData.getData('text').trim().slice(0, 6)

    if (!/^\d+$/.test(pastedData)) {
        return // Ensure only numeric blocks paste if requested
    }

    const chars = pastedData.split('')
    chars.forEach((char, i) => {
        if (i < otp.value.length) {
            otp.value[i] = char
        }
    })

    clearValidationState()
    const nextTarget = Math.min(chars.length, otp.value.length - 1)
    otpInputs.value[nextTarget]?.focus()
}

const clearValidationState = () => {
    hasError.value = false
    isShaking.value = false
}

// Actions
const verifyCode = () => {
    // const fullCode = otp.value.join('')
        const form = useForm({
    verification_code: otp.value.join(''),
})


    isVerifying.value = true
    hasError.value = false
    isShaking.value = false

    // setTimeout(() => {
    //     isVerifying.value = false

    //     if (fullCode === '123456') {
    //         isVerified.value = true
    //         // Success Confetti Simulation Ring Effect
    //         hasRingEffect.value = true
    //         setTimeout(() => {
    //             hasRingEffect.value = false
    //         }, 1000)
    //     } else {
    //         hasError.value = true
    //         isShaking.value = true
    //     }
    // }, 1800)


    form.post('/onboarding/confirm_phone_verification_sms', {
        onSuccess: () => {
            isVerifying.value = form.processing
            isVerified.value = true
            hasRingEffect.value = true
            // setTimeout(() => {
            //     hasRingEffect.value = false

            //     router.visit('/test', {
            //         method: 'get',
            //         preserveState: true,
            //     });
            // }, 1000)
        },
        onError: () => {
            // Handle error response
            isVerifying.value = form.processing

            hasError.value = true
            isShaking.value = true
        },

    })
}
const resendCode = async () => {
    isResending.value = true

    try {
        await new Promise(resolve => setTimeout(resolve, 500))

        const response = await axios.post(
            '/onboarding/resend_phone_verification_sms'
        )

        console.log(response.data)
    } catch (error) {
        console.error(error)
    } finally {
        isResending.value = false
    }
}


</script>

<style scoped>
/* Scoped Typography / Color Design System Properties */
.identity-verification-container {
    --primary: #006e2a;
    --on-primary: #ffffff;
    --primary-container: #00c853;
    --on-primary-container: #004c1b;
    --secondary: #5f5e5e;
    --tertiary-container: #a4b1a6;
    --background: #f8f9fa;
    --surface: #f8f9fa;
    --surface-container-lowest: #ffffff;
    --on-background: #191c1d;
    --outline-variant: rgba(187, 203, 184, 0.5);
    --error: #ba1a1a;

    font-family: 'Inter', sans-serif;
    background-color: var(--surface);
}

/* Typography tokens parsed from inline configurations */
.font-headline-lg {
    font-size: 32px;
    line-height: 40px;
    letter-spacing: -0.01em;
}

.font-headline-md {
    font-size: 24px;
    line-height: 32px;
}

.font-body-lg {
    font-size: 16px;
    line-height: 24px;
}

.font-label-md {
    font-size: 12px;
    line-height: 16px;
    letter-spacing: 0.01em;
}

/* Custom Colors Maps classes */
.bg-surface {
    background-color: var(--surface);
}

.bg-primary-container {
    background-color: var(--primary-container);
}

.bg-primary {
    background-color: var(--primary);
}

.bg-surface-container-lowest {
    background-color: var(--surface-container-lowest);
}

.text-primary {
    color: var(--primary);
}

.text-on-primary {
    color: var(--on-primary);
}

.text-on-primary-container {
    color: var(--on-primary-container);
}

.text-on-background {
    color: var(--on-background);
}

.text-secondary {
    color: var(--secondary);
}

.text-error {
    color: var(--error);
}

.border-outline-variant {
    border-color: var(--outline-variant);
}

.border-error {
    border-color: var(--error) !important;
}

.ring-primary-10 {
    --tw-ring-color: rgba(0, 110, 42, 0.1);
}

/* Spacing Helpers mapping original custom config scale */
.p-md {
    padding: 16px;
}

.px-lg {
    padding-left: 24px;
    padding-right: 24px;
}

.mb-md {
    margin-bottom: 16px;
}

.mb-lg {
    margin-bottom: 24px;
}

.mb-xl {
    margin-bottom: 32px;
}

.mb-2xl {
    margin-bottom: 48px;
}

.mb-3xl {
    margin-bottom: 64px;
}

.mt-xl {
    margin-top: 32px;
}

.gap-xs {
    gap: 4px;
}

.gap-sm {
    gap: 8px;
}

/* Background Glow Specific Adjustments */
.bg-primary-container-20 {
    background-color: rgba(0, 200, 83, 0.2);
}

.bg-tertiary-container-20 {
    background-color: rgba(164, 177, 166, 0.2);
}

.floating-glow {
    filter: blur(120px);
    opacity: 0.3;
    z-index: 0;
}

/* Material Design Settings adjustments */
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.fill-icon {
    font-variation-settings: 'FILL' 1Element !important;
}

/* Custom OTP Focus State definitions */
.otp-input:focus {
    border-color: #00c853 !important;
    box-shadow: 0 0 0 2px rgba(0, 200, 83, 0.1) !important;
}

/* Animations */
@keyframes shake {

    0%,
    100% {
        transform: translateX(0);
    }

    25% {
        transform: translateX(-8px);
    }

    75% {
        transform: translateX(8px);
    }
}

.animate-shake {
    animation: shake 0.4s cubic-bezier(.36, .07, .19, .97) both;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }

    100% {
        background-position: 200% 0;
    }
}

.shimmer {
    background: linear-gradient(90deg, #f0f1f2 25%, #e1e3e4 50%, #f0f1f2 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}
</style>
