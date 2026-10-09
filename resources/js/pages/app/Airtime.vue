<template>
    <div class="app-container">
        <div class="app-content-wrapper">
            <BackAndHistoryHeader :pageName="'Airtime'" />
                   <ErrorNotification v-if="page.props.flash.error" :error="page.props.flash.error"
            :message="page.props.flash.error" />

            <main class="content-area">
                <section class="card provider-section">
                    <p class="label-text">MOBILE OPERATOR & NUMBER</p>
                    <div class="input-row">
                        <div class="provider-dropdown" :class="{ 'has-value': selectedProvider !== '' }">
                            <div class="provider-display">
                                <span class="provider-name">{{ selectedProviderName }}</span>
                                <!-- <span class="material-symbols-outlined dropdown-arrow">expand_more</span> -->
                            </div>
                            <select v-model="selectedProvider" class="hidden-select">
                                <option value="" disabled>Select</option>
                                <option v-for="(provider, index) in airtime_providers" :key="index" :value="index">
                                    {{ provider }}
                                </option>
                            </select>
                        </div>

                        <div class="phone-input-container">
                            <input v-model="phoneNumber" class="text-input" placeholder="0801 234 5678" type="tel"
                                maxlength="11" />
                        </div>
                    </div>
                </section>

                <section class="grid-section">
                    <div class="section-header">
                        <h2 class="section-title">Select Amount</h2>
                        <button class="text-button active-tap">History</button>
                    </div>

                    <div class="amount-grid">
                        <button v-for="amount in amounts" :key="amount.value" @click="selectedAmount = amount.value"
                            :class="['amount-card active-tap', { 'active': selectedAmount === amount.value }]">
                            <span class="amount-text">₦{{ amount.label }}</span>
                            <div v-if="amount.popular" class="popular-badge">POPULAR</div>
                        </button>
                    </div>
                </section>

                <section class="payment-footer">
                    <div class="amount-display">
                        <p class="label-text">AMOUNT TO PAY</p>
                        <div class="price-input-group">
                            <span class="currency-symbol">₦</span>
                            <input v-model="selectedAmount" type="number" class="price-input" placeholder="0.00" />
                        </div>
                    </div>

                    <button :disabled="fieldIncompleteAndInvalid() || confirm" class="pay-button active-tap"
                        :class="{ 'btn-disabled': fieldIncompleteAndInvalid() }" @click="confirmAirtimePayment">
                        <div class="button-content" :class="{ 'is-hidden': confirm }">
                            <span>Pay ₦{{ Number(selectedAmount || 0).toLocaleString() }}.00</span>
                        </div>
                        <span v-if="confirm" class="spinner"></span>
                    </button>
                </section>
                <BottomNav />
            </main>
            <Teleport to="body">
                        <ConfirmTransaction :transactionFeesLoading="feeIsLoading" :transactionType="transactionType" :show="confirm" :amount="selectedAmount" @close="toggleConfirm"
                 @completeProcess="handleCompleteProcess" :availableBalance="$page.props.availableBalance" :transactionFees="fee" @confirm="handleContinue" :recipient="phoneNumber"  />

            </Teleport>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { useForm , usePage  } from '@inertiajs/vue3'
import { ref, computed,  watch } from 'vue';
import BackAndHistoryHeader from '@/components/BackAndHistoryHeader.vue';
import ConfirmTransaction from '@/components/ConfirmTransaction.vue';
import ErrorNotification from '@/components/ui/ErrorNotification.vue';
import { useTransactionFee } from '@/composables/useTransactionFee';
import BottomNav from '../../components/BottomNav.vue';

const page = usePage()

const props = defineProps({
    airtime_providers: {
        type: Array,
        required: true,
        // default: () => [],
    },
});

watch(page.props.flash.error , ()=> {
 console.log('page error from airtime' , page.props.flash.error)
})


const {
        fee,
        feeIsLoading,
        fetchFee,
 } = useTransactionFee()

const form = useForm({
    selectedProvider: null,
    phoneNumber: '',
    amount: null,
    transactionType: ''
})

function assignFormObjectparameteres() {
    Object.assign(form, {
        selectedProvider: selectedProvider.value,
        phoneNumber: phoneNumber.value,
        amount: selectedAmount.value,
        transactionType: transactionType.value,
    })
}

const phoneNumber = ref('');
const selectedAmount = ref(100);

const selectedProvider = ref('');

const confirm = ref(false);
const amount = ref(1000);
const transactionType = ref('airtime');

const toggleConfirm = () => {
    confirm.value = !confirm.value;
    // loading.value = !loading.value;
}

function handleContinue() {
    toggleConfirm();
}

function handleCompleteProcess() {
    assignFormObjectparameteres();
     submitForm();
    confirm.value = false

}

async function submitForm() {
    form.post('/transaction/airtime');
}

const amounts = [
    { value: 100, label: '100' },
    { value: 200, label: '200' },
    { value: 500, label: '500', popular: true },
    { value: 1000, label: '1,000' },
    { value: 2000, label: '2,000' },
    { value: 5000, label: '5,000' },
];

function fieldIncompleteAndInvalid() {
    return !phoneNumber.value || !selectedAmount.value || !selectedProvider.value || phoneInvalid();
}



function phoneInvalid() {
    const cleanPhone = phoneNumber.value.replace(/\D/g, '');

    return (
        cleanPhone.length !== 11 ||
        !cleanPhone.startsWith('0')
    );
}

async function confirmAirtimePayment() {
    fetchFee(amount.value)
    confirm.value = true;

    if (confirm.value) {
        return;
    }

}

const selectedProviderName = computed(() => {
    return selectedProvider.value !== '' ? props.airtime_providers[selectedProvider.value] : 'Select';
})
</script>

<style scoped>
/* Base Layout */
.app-container {
    position: relative;
    width: 100%;
    max-width: 430px;
    /* Standard Pro Max width */
    height: 100vh;
    max-height: 932px;
    background-color: #f8faf9;
    /* Softer, premium off-white */
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    overflow-y: auto;
    margin: 0 auto;
    font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
}

.app-content-wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
    z-index: 1;

}

/* Scrollable Content */
.content-area {
    flex: 1;
    padding: 24px 20px 140px 20px;
    /* Bottom padding for fixed footer */
    overflow-y: auto;
    /* scrollbar-width: none; */
    /* Firefox */
}



/* Common Text/Labels */
.label-text {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #6b7d72;
    text-transform: uppercase;
    margin-bottom: 12px;
}

/* Provider & Phone Card */
.card {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 20px;
    box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.03);
    margin-bottom: 32px;
}

.input-row {
    display: flex;
    gap: 12px;
}

/* Dropdown styling (Overlay method for perfect design) */
.provider-dropdown {
    position: relative;
    width: 38%;
    background-color: #f2f6f3;
    border-radius: 14px;
    border: 1.5px solid transparent;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    overflow: hidden;
}

.provider-dropdown:focus-within,
.provider-dropdown.has-value {
    background-color: #ffffff;
    border-color: #0b6638;
    box-shadow: 0 0 0 4px rgba(11, 102, 56, 0.1);
}

.provider-display {
    width: 100%;
    padding: 16px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    pointer-events: none;
    /* Let clicks pass through to select */
}

.provider-name {
    font-size: 15px;
    font-weight: 600;
    color: #1a231e;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dropdown-arrow {
    font-size: 20px;
    color: #8c9e93;
}

.hidden-select {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    appearance: none;
}

/* Phone Input */
.phone-input-container {
    flex: 1;
    background-color: #f2f6f3;
    border-radius: 14px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    border: 1.5px solid transparent;
    transition: all 0.2s ease;
}

.phone-input-container:focus-within {
    background-color: #ffffff;
    border-color: #0b6638;
    box-shadow: 0 0 0 4px rgba(11, 102, 56, 0.1);
}

.text-input {
    width: 100%;
    height: 52px;
    background: transparent;
    border: none;
    font-size: 18px;
    font-weight: 700;
    color: #1a231e;
    outline: none;
    font-variant-numeric: tabular-nums;
    letter-spacing: 0.02em;
}

.text-input::placeholder {
    color: #9ab0a2;
    font-weight: 500;
}

/* Amount Grid Section */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a231e;
}

.text-button {
    background: none;
    border: none;
    font-size: 14px;
    font-weight: 700;
    color: #0b6638;
    cursor: pointer;
    padding: 4px 8px;
}

.amount-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.amount-card {
    position: relative;
    background-color: #ffffff;
    border: 1.5px solid #e2e8e4;
    padding: 20px 8px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.amount-text {
    font-size: 18px;
    font-weight: 700;
    color: #1a231e;
    font-variant-numeric: tabular-nums;
}

.popular-badge {
    position: absolute;
    top: -10px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.05em;
    box-shadow: 0 4px 8px rgba(217, 119, 6, 0.25);
}

/* Active State for Premium Fintech Feel */
.amount-card.active {
    background: linear-gradient(145deg, #0b6638 0%, #044d28 50%, #023d20 100%);
    border-color: transparent;
    box-shadow: 0 12px 24px rgba(2, 61, 32, 0.25);
}

.amount-card.active .amount-text {
    color: #ffffff;
}

/* Footer Section */
.payment-footer {
    position: relative;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #ffffff;
    padding: 20px 24px 36px 24px;
    /* Extra bottom padding for home indicator */
    border-top-left-radius: 24px;
    border-top-right-radius: 24px;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.06);
    /* z-index: 100; */
    margin-top: 120px;
}

.price-input-group {
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.currency-symbol {
    font-size: 28px;
    font-weight: 700;
    color: #1a231e;
}

.price-input {
    width: 100%;
    font-size: 40px;
    font-weight: 800;
    background: transparent;
    border: none;
    padding: 0;
    color: #1a231e;
    outline: none;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.03em;
}

/* Pay Button */
.pay-button {
    position: relative;
    width: 100%;
    min-height: 64px;
    /* Fixed height prevents jitter */
    background: linear-gradient(145deg, #0b6638 0%, #044d28 50%, #023d20 100%);
    color: #ffffff;
    border: none;
    border-radius: 16px;
    font-size: 18px;
    font-weight: 700;
    box-shadow: 0 12px 24px rgba(2, 61, 32, 0.3);
    cursor: pointer;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.button-content {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: opacity 0.2s;
    width: 100%;
}

.button-content.is-hidden {
    opacity: 0;
}

.icon-right {
    font-size: 22px;
}

/* Disabled State */
.pay-button.btn-disabled {
    background: #e2e8e4;
    color: #9ab0a2;
    box-shadow: none;
    cursor: not-allowed;
    transform: none !important;
}

/* Interactions */
.active-tap {
    transition: transform 0.1s cubic-bezier(0.4, 0, 0.2, 1);
}

.active-tap:active:not(.btn-disabled) {
    transform: scale(0.96);
}

/* Spinner Definition */
.spinner {
    position: absolute;
    width: 24px;
    height: 24px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top: 3px solid #ffffff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
