<template>
    <div @click="navigateToTransferAmount(tx)" class="tx-item active-tap">
        <div class="tx-left">
            <div class="">
                <h2 class="meta">{{ tx.recipient_name }}</h2>
                <div class="tx-details">
                    <h1 class="bank-name">{{ tx.recipient_bank }}</h1>
                    <p class="meta">{{ tx.recipient_account }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
defineProps({ tx: Object });
import { router } from '@inertiajs/vue3';
import { useTransferStore } from '@/stores/transfer';
const transferStore = useTransferStore();

const navigateToTransferAmount = (tx) => {
    handleTransfer(tx);
};

const handleTransfer = (tx) => {
    const id = transferStore.addTransfer({
        accountNumber: tx.recipient_account,
        bankName: tx.recipient_bank,
        bankCode: '031',
        accountName: tx.recipient_name,
    });
    const transferId = id;
    router.get(`/complete_transfer?transferId=${transferId}`);
};
</script>

<style scoped>
.tx-item {
    background-color: var(--surface-container-lowest);
    border: 1px solid rgba(171, 173, 174, 0.1);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.2s;
}

.tx-details {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    gap: 10px;
}

.tx-item:hover {
    background-color: #ffffff;
}

.tx-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bank-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--surface-container-high);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
}

.fill-icon {
    font-variation-settings: 'FILL' 1;
}

.bank-name {
    font-weight: bolder;
    margin-top: auto;
}

.meta {
    font-weight: bolder;
    color: var(--on-surface-variant);
    margin: 2px 0 0 0;
}

.tx-right {
    text-align: right;
}

.amount {
    font-family: 'Manrope', sans-serif;
    font-weight: 700;
    font-size: 14px;
    margin: 0 0 4px 0;
}

.status-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
}

.success {
    background: rgba(114, 251, 189, 0.3);
    color: var(--secondary);
}
.failed {
    background: rgba(251, 81, 81, 0.2);
    color: var(--error);
}
.pending {
    background: rgba(3, 220, 255, 0.2);
    color: var(--on-tertiary-container);
}

.active-tap:active {
    transform: scale(0.98);
}
</style>
