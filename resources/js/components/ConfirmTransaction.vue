<script lang="ts" setup>
import {
    ref,
    computed
} from 'vue';

const props = defineProps(['show', 'transferData', 'transactionType' , 'amount' , 'availableBalance' , 'transactionFees' , 'transactionFeesLoading', 'recipient']);

const emit = defineEmits(['close', 'confirm', 'completeProcess']);

const loading = ref(false);
const transferTransactionType = 'transfer';
const airtimeTransactionType = 'airtime';
const amountToTransact = computed(() => {
    return String(Number(props.amount) + Number(props.transactionFees))
})
</script>


<template>
    <div class="drawer-overlay"></div>
    <Transition name="slide-up">
        <div v-if="props.show" class="drawer-content" @click.self="emit('close')">
            <div class="handle"></div>
            <div class="drawer-body">
                <header class="amount-header">
                    <p class="sub-label">TOTAL PAYMENT</p>
                    <h2 v-if="!props.transactionFeesLoading" class="main-amount">₦{{ amountToTransact }}</h2>
                       <div v-else class="spinner"></div>
                </header>

                <div class="details-card">

                    <div class="detail-row">
                        <span>Transaction</span>
                        <span class="val">{{ props.transactionType ?? '' }}</span>
                    </div>

                    <div class="detail-row">
                        <span>Amount</span>
                        <span class="val">{{props.amount}}</span>
                    </div>

                    <div class="detail-row">
                        <span>Transaction Fee</span>
                       <span v-if="!props.transactionFeesLoading" class="val green">{{ props.transactionFees }}</span>
                         <div v-else class="spinner"></div>
                
                    </div>
                    <div class="divider"></div>

                    <div v-if="props.transactionType === transferTransactionType" class="detail-row align-start">
                        <span>Account Number</span>
                        <div class="text-right">
                            <span class="val block">{{ props.transferData?.accountNumber ?? '' }}</span>
                            <span class="bank-tag">{{ props.transferData?.bankCode ?? '' }}</span>
                        </div>
                    </div>

                    <div v-if="props.transactionType === transferTransactionType" class="detail-row">
                        <span>Account Number</span>
                        <span class="val">{{ props.transferData?.accountName ?? '' }}</span>
                    </div>

                    <div v-if="props.transactionType === airtimeTransactionType" class="detail-row">
                        <span>Recipient</span>
                        <span class="val">{{ props.recipient ?? 'recipient not shown' }}</span>
                    </div>

                </div>
                <div class="actions">
                    <button @click="$emit('completeProcess')" class="btn-primary" :disabled="loading">
                        <span v-if="!loading">Confirm to Pay</span>
                        <div v-else class="spinner"></div>
                    </button>

                    <button @click="$emit('close')" class="btn-ghost">
                        Cancel Transaction
                    </button>
                </div>
            </div>
        </div>
    </Transition>

</template>

<style scoped>

.amount-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    text-align: center;
    padding: 24px 16px;
}

.sub-label {
    margin: 0;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 1.5px;
    color: #888;
}

.main-amount {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
    color: #1a1a1a;
    overflow-wrap: anywhere;
}

.spinner {
    width: 28px;
    height: 28px;
    border: 3px solid #d1e8d8;
    border-top-color: #16a34a;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
.drawer-overlay {
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px) saturate(140%);
    -webkit-backdrop-filter: blur(16px) saturate(140%);
    z-index: 100;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: transform 0.5s ease-out;
}

.slide-up-enter-from,
.slide-up-leave-to {
    transform: translateY(100%);
}

.slide-up-enter-to,
.slide-up-leave-from {
    transform: translateY(0);
}

.drawer-content {
    position: fixed;
    margin-top: 100px;
    bottom: auto;
    left: 0;
    width: 100%;
    padding: 24px;
    background-color: rgba(248, 249, 250, 0.8);
    backdrop-filter: blur(20px);
    display: flex;
    flex-direction: column;
    align-items: center;
    inset: 0;
    z-index: 1000;
    overflow-y: auto;

}

/* .drawer-content.sliding-up {
        margin-top: 100px;
        transform: translateY(0);
    } */

.drawer-body {
    overflow-y: auto;
}

.handle {
    width: 48px;
    height: 6px;
    background: rgba(84, 128, 100, 0.2);
    border-radius: 99px;
    margin: 0 auto 32px;
}



.details-card {
    background: rgba(199, 253, 216, 0.15);
    border-radius: 24px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 24px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    gap: 20px;
    color: #39644a;
}

.detail-row .val {
    font-weight: 700;
    color: #06361f;
}

.detail-row .val.green {
    color: #006a28;
}

.bank-tag {
    font-size: 10px;
    font-weight: 800;
    color: #006572;
    display: block;
}

.divider {
    height: 1px;
    background: rgba(84, 128, 100, 0.1);
}

.balance-info {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: rgba(120, 245, 174, 0.1);
    border: 1px solid rgba(120, 245, 174, 0.3);
    border-radius: 16px;
    margin-bottom: 32px;
}

.icon-circle {
    width: 40px;
    height: 40px;
    background: #78f5ae;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.balance-text {
    flex: 1;
}

.balance-text p {
    margin: 0;
    font-size: 0.75rem;
}

.balance-text .val {
    font-weight: 700;
    font-size: 1rem;
}

.check {
    color: #00693f;
}

.btn-primary {
    width: 100%;
    height: 64px;
    background: linear-gradient(to right, #006a28, #5cfd80);
    border: none;
    border-radius: 32px;
    color: #004819;
    font-family: 'Manrope', sans-serif;
    font-weight: 800;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 8px 16px rgba(0, 106, 40, 0.2);
}

.btn-primary:active {
    transform: scale(0.98);
    opacity: 0.9;
}

.btn-ghost {
    width: 100%;
    background: none;
    border: none;
    padding: 16px;
    color: #39644a;
    font-weight: 500;
    cursor: pointer;
}

.btn-ghost:hover {
    color: #b02500;
}

.spinner {
    width: 24px;
    height: 24px;
    border: 3px solid rgba(0, 72, 25, 0.3);
    border-top-color: #004819;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
