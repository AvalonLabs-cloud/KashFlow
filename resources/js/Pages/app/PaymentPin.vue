<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue'
import ErrorNotification from '@/components/ui/ErrorNotification.vue';


const page = usePage()
const PIN_LENGTH = 4

const pin = ref('')
const status = ref('idle') // 'idle' | 'loading' | 'success' | 'error'
const isShaking = ref(false)
const hiddenInput = ref(null)

const form = useForm({
    ...page.props.flash.transaction_data,
    transaction_pin: pin.value,
})

// Actions
const appendDigit = (digit) => {
    if (status.value === 'loading' || status.value === 'success' || pin.value.length >= PIN_LENGTH) {
        return
    }

    if (status.value === 'error') {
        status.value = 'idle'
    }

    pin.value += digit

    if (pin.value.length === PIN_LENGTH) {
        verifyPin()
    }
}



const deleteDigit = () => {
    if (status.value === 'loading' || status.value === 'success' || pin.value.length === 0) {
        return
    }

    if (status.value === 'error') {
        status.value = 'idle'
    }

    pin.value = pin.value.slice(0, -1)
}

const handleInputNative = (event) => {
    const cleaned = event.target.value.replace(/[^0-9]/g, '').slice(0, PIN_LENGTH)
    pin.value = cleaned

    if (pin.value.length === PIN_LENGTH) {
        verifyPin()
    }
}

const verifyPin = () => {
    status.value = 'loading'

    form.transaction_pin = pin.value

    const endpoints = {
        transfer: '/transaction/transfer',
        airtime: '/transaction/airtime',
        data: '/transaction/data',
    }

    const endpoint = endpoints[page.props.flash.transaction_data.transactionType]

    if (!endpoint) {
        status.value = 'error'
        console.error(`Unknown transaction type: ${page.props.flash.transaction_data}`)

        return
    }

    form.post(endpoint, {
        preserveScroll: true,

        onError: () => {
            console.log(page.props.flash.error);

            status.value = 'error'
            triggerShake()

            setTimeout(() => {
                pin.value = ''
                form.pin = ''
                status.value = 'idle'
                focusInput()
            }, 1200)
        },

        onFinish: () => {
            status.value = 'idle'

        },
    })
}

const triggerShake = () => {
    isShaking.value = true
    setTimeout(() => {
        isShaking.value = false
    }, 400)
}

const focusInput = () => {
    if (status.value !== 'loading' && status.value !== 'success') {
        hiddenInput.value?.focus()
    }
}

// Global Keyboard Handler
const handleKeydown = (event) => {
    if (status.value === 'loading' || status.value === 'success') {
        return
    }

    if (event.key >= '0' && event.key <= '9') {
        appendDigit(event.key)
    } else if (event.key === 'Backspace') {
        deleteDigit()
    }
}

console.log(page.props);


onMounted(() => {
    window.addEventListener('keydown', handleKeydown)
    focusInput()
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown)
})
</script>


<template>
    <div class="pin-container">
        <main class="card-wrapper">
            <div class="card">
                <!-- Top Security Indicator -->
                <div class="security-badge">
                    <span class="material-symbols-outlined icon">lock</span>
                </div>

                <!-- Header -->
                <ErrorNotification v-if="page.props.flash.error" :error="page.props.flash.error"
                    :message="page.props.flash.error" />


                <!-- Hidden Native Input for Accessibility/Mobile Keyboard -->
                <label for="numeric-pin-input" class="sr-only">Enter 4-digit PIN</label>
                <input id="numeric-pin-input" ref="hiddenInput" :value="pin" type="password" inputmode="numeric"
                    pattern="[0-9]*" maxlength="4" autocomplete="one-time-code" class="hidden-input"
                    :disabled="status === 'loading' || status === 'success'" @input="handleInputNative" />

                <!-- Visual Slot Representation -->
                <div class="slots-wrapper" :class="{ 'animate-shake': isShaking }" @click="focusInput">
                    <div v-for="index in PIN_LENGTH" :key="index" class="slot" :class="{
                        'slot-active': index - 1 === pin.length && status === 'idle',
                        'slot-filled': index <= pin.length,
                        'slot-error': status === 'error',
                        'slot-success': status === 'success'
                    }">
                        <!-- Dot Indicator -->
                        <span v-if="index <= pin.length" class="dot"
                            :class="{ 'dot-success': status === 'success' }"></span>

                        <!-- Blinking Cursor Bar -->
                        <span v-else-if="index - 1 === pin.length && status === 'idle'" class="cursor-bar"></span>
                    </div>
                </div>

                <!-- Feedback & Status Notification Area -->
                <div class="status-area">
                    <div v-if="status === 'error'" class="status-msg text-error">
                        <span class="material-symbols-outlined">error</span>
                        <span>Incorrect PIN. Please try again.</span>
                    </div>

                    <div v-if="status === 'loading'" class="status-msg text-primary">
                        <svg class="spinner" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Verifying PIN...</span>
                    </div>

                    <div v-if="status === 'success'" class="status-msg text-primary">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span>Transaction authorized successfully.</span>
                    </div>
                </div>

                <!-- On-Screen Numeric Keypad -->
                <div class="keypad">
                    <button v-for="num in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="num" type="button" class="keypad-btn"
                        :disabled="status === 'loading' || status === 'success'" @click="appendDigit(num.toString())">
                        {{ num }}
                    </button>

                    <!-- Bottom Row: Spacer, 0, Backspace -->
                    <div class="keypad-spacer"></div>

                    <button type="button" class="keypad-btn" :disabled="status === 'loading' || status === 'success'"
                        @click="appendDigit('0')">
                        0
                    </button>

                    <button type="button" class="keypad-btn keypad-action" aria-label="Backspace"
                        :disabled="status === 'loading' || status === 'success'" @click="deleteDigit">
                        <span class="material-symbols-outlined">backspace</span>
                    </button>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0');

.pin-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9ff;
    font-family: 'Inter', system-ui, sans-serif;
    color: #0b1c30;
    padding: 1.5rem 1rem;
}

.card-wrapper {
    width: 100%;
    max-width: 500px;
}

.card {
    background-color: #ffffff;
    border-radius: 1.5rem;
    padding: 2.5rem 2rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.security-badge {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background-color: #eff4ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #006948;
    margin-bottom: 1.5rem;
}

.header {
    text-align: center;
    margin-bottom: 2rem;
}

.header h1 {
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    font-size: 2rem;
    font-weight: 600;
    letter-spacing: -0.025em;
    margin: 0;
}

.header p {
    color: #565e74;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Hidden Native Input */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

.hidden-input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}

/* Slots */
.slots-wrapper {
    display: flex;
    gap: 0.75rem;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.slot {
    width: 3.5rem;
    height: 4rem;
    border-radius: 0.75rem;
    background-color: #eff4ff;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

@media (min-width: 640px) {
    .slot {
        width: 4rem;
        height: 5rem;
    }
}

.slot-active {
    background-color: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    outline: 2px solid rgba(0, 105, 72, 0.2);
}

.slot-error {
    background-color: #ffdad6 !important;
}

.slot-success {
    background-color: #00855d !important;
}

.dot {
    width: 0.875rem;
    height: 0.875rem;
    background-color: #0b1c30;
    border-radius: 50%;
}

.dot-success {
    background-color: #ffffff;
}

.cursor-bar {
    width: 2px;
    height: 1.5rem;
    background-color: #006948;
    animation: pulse 1s infinite;
}

/* Status Area */
.status-area {
    height: 2.5rem;
    margin: 0.5rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
}

.status-msg {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.text-error {
    color: #ba1a1a;
}

.text-primary {
    color: #006948;
}

.spinner {
    width: 1.25rem;
    height: 1.25rem;
    animation: spin 1s linear infinite;
}

/* Keypad */
.keypad {
    width: 100%;
    max-width: 288px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.keypad-btn {
    height: 3.5rem;
    border-radius: 9999px;
    background-color: #eff4ff;
    border: none;
    font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
    font-size: 1.375rem;
    font-weight: 600;
    color: #0b1c30;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    user-select: none;
    transition: all 0.15s ease;
}

.keypad-btn:hover:not(:disabled) {
    background-color: #e5eeff;
}

.keypad-btn:active:not(:disabled) {
    transform: scale(0.95);
}

.keypad-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.keypad-action {
    color: #565e74;
}

.keypad-spacer {
    height: 3.5rem;
}

/* Demo Notice */
.demo-notice {
    margin-top: 1rem;
    padding-top: 1rem;
    font-size: 0.75rem;
    color: #565e74;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.demo-code {
    font-weight: 600;
    color: #0b1c30;
    background-color: #eff4ff;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
}

/* Animations */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0;
    }
}

@keyframes shake {

    0%,
    100% {
        transform: translateX(0);
    }

    20%,
    60% {
        transform: translateX(-8px);
    }

    40%,
    80% {
        transform: translateX(8px);
    }
}

.animate-shake {
    animation: shake 0.4s ease-in-out;
}
</style>