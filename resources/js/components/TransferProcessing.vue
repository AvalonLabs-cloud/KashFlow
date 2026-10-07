<template>
    <div class="transaction-container">

            <div v-if="props.data" class="modal-backdrop" @click.self="handleCancel">
                <Transition name="slide-up">
                    <div v-if="true" class="bottom-sheet" role="dialog" aria-modal="true">
                        <div class="drag-handle"></div>

                        <div class="status-container">
                            <svg class="progress-ring" viewBox="0 0 72 72">
                                <circle class="ring-track" cx="36" cy="36" r="32" />
                                <circle class="ring-indicator" cx="36" cy="36" r="32" />
                            </svg>
                            <span class="material-symbols-outlined status-icon">sync</span>
                        </div>

                        <div class="sheet-header">
                            <h3 class="sheet-title">{{ statusTitle }}</h3>
                            <p class="sheet-subtitle">
                                Please wait while we complete your
                                transaction...
                            </p>
                        </div>

                        <div class="amount-display">
                            <span class="currency">{{ currencySymbol }}</span>
                            <span class="amount">{{ data.amount }}</span>
                        </div>

                        <div v-if="data.type === 'airtime' || data.type === 'data'" class="detail-row">
                            <span class="label">Recipient</span>
                            <span class="value">{{
                                data.metadata.phoneNumber
                            }}</span>
                        </div>

                        <div v-if="data.type === 'airtime'" class="detail-row">
                            <span class="label">Provider</span>
                            <span class="value">{{
                                data.metadata.selectedProvider
                            }}</span>
                        </div>

                        <div class="details-card">
                            <div v-if="data.type === 'transfer'" class="detail-row">
                                <span class="label">Recipient</span>
                                <span class="value">{{
                                    data.metadata.recipientname
                                }}</span>
                            </div>
                            <div v-if="data.type === 'transfer'" class="detail-row">
                                <span class="label">Bank Code</span>
                                <span class="value">{{
                                    data.metadata.bankCode
                                }}</span>
                            </div>
                            <div v-if="data.type === 'transfer'" class="detail-row">
                                <span class="label">Account Number</span>
                                <span class="value">{{
                                    data.metadata.accountNumber
                                }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="label">Reference</span>
                                <span class="value mono">{{
                                    data.transaction_reference
                                }}</span>
                            </div>
                        </div>
                        <div class="progress-section">
                            <p v-if="showTimeoutMessage" class="timeout-text">
                                Still processing... this may take a little
                                longer
                            </p>
                        </div>
                    </div>
                </Transition>
            </div>
      
    </div>
      <BottomNav />
</template>

<script lang="ts" setup>

import { ref } from 'vue';
import BottomNav from '../components/BottomNav.vue';

const props = defineProps(['data', 'clients_name']);

const statusTitle = ref('Processing Transaction');
const currencySymbol = ref('₦');

const emit = defineEmits(['close', 'cancel']);

const showTimeoutMessage = ref(false);

const handleCancel = () => {
    emit('cancel');
    emit('close');
};
</script>

<style scoped>
:host {
    --primary: #006d38;
    --primary-container: #00a859;
    --surface: #f9f9f9;
    --on-surface: #1a1c1c;
    --on-surface-variant: #3d4a3f;
    --outline: #e2e2e2;
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.transaction-container {
    font-family: 'Inter', sans-serif;
    color: var(--on-surface);
    background: #f2f2f2;
    height: fit-content;
    position: relative;
    margin-bottom: 200px; 
}

/* Header & App Bar */
.app-bar {
    position: sticky;
    top: 0;
    z-index: 40;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: #eeeeee;
    border: none;
}

.app-bar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.app-bar-title {
    font-family: 'Manrope', sans-serif;
    font-weight: 700;
    font-size: 1.125rem;
    color: #006d38;
    margin: 0;
}

.brand-logo {
    font-family: 'Manrope', sans-serif;
    font-weight: 900;
    color: #006d38;
}

.icon-button {
    background: transparent;
    border: none;
    padding: 4px;
    border-radius: 50%;
    cursor: pointer;
    color: #71717a;
    transition: var(--transition-smooth);
}

.icon-button:hover {
    background: rgba(0, 0, 0, 0.05);
}

/* Dashboard Mockup */
.dashboard-preview {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
    padding-top: 80px;
}

.amount {
    font-size: larger;
}

.preview-card {
    max-width: 420px;
    width: 100%;
    background: #ffffff;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    text-align: center;
}

.preview-title {
    font-family: 'Manrope', sans-serif;
    font-weight: 700;
    font-size: 1.5rem;
    color: #006d38;
    margin-bottom: 16px;
}

.preview-subtitle {
    color: #3d4a3f;
    margin-bottom: 32px;
}

.skeleton-loader {
    height: 48px;
    background: #eeeeee;
    border-radius: 8px;
    margin-bottom: 16px;
}

.skeleton-loader.short {
    width: 75%;
    margin-left: auto;
    margin-right: auto;
}

/* Modal & Bottom Sheet */
.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
    z-index: 50;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}

.bottom-sheet {
    background: #ffffff;
    width: 100%;
    max-width: 420px;
    border-radius: 24px 24px 0 0;
    padding: 8px 24px 48px 24px;
    box-shadow: 0 -4px 40px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
}

.drag-handle {
    width: 32px;
    height: 4px;
    background: #e2dfde;
    border-radius: 99px;
    margin: 16px 0 24px 0;
}

/* Status Icon & Ring */
.status-container {
    position: relative;
    width: 72px;
    height: 72px;
    background: #e6f6ef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
}

.progress-ring {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    animation: spin 2s linear infinite;
}

.ring-track {
    fill: none;
    stroke: rgba(0, 109, 56, 0.1);
    stroke-width: 3;
}

.ring-indicator {
    fill: none;
    stroke: #00a859;
    stroke-width: 3;
    stroke-linecap: round;
    stroke-dasharray: 200;
    stroke-dashoffset: 130;
}

.status-icon {
    color: #006d38;
    font-size: 30px;
}

/* Typography */
.sheet-header {
    text-align: center;
    margin-bottom: 24px;
}

.sheet-title {
    font-family: 'Manrope', sans-serif;
    font-weight: 600;
    font-size: 1.125rem;
    color: var(--on-surface);
    margin: 0;
}

.sheet-subtitle {
    font-size: 13px;
    color: var(--on-surface-variant);
    margin-top: 4px;
    padding: 0 16px;
}

.amount-display {
    margin-bottom: 32px;
    font-family: 'Manrope', sans-serif;
    font-weight: 800;
    font-size: 32px;
    letter-spacing: -0.02em;
}

/* Detail Card */
.details-card {
    width: 100%;
    background: #f9f9f9;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 50px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-row.divider {
    padding-top: 16px;
    border-top: 1px solid #eeeeee;
}

.label {
    font-size: 13px;
    color: var(--on-surface-variant);
}

.value {
    font-size: 13px;
    font-weight: 600;
    color: var(--on-surface);
}

.value.medium {
    font-weight: 500;
}

.value.mono {
    font-family: monospace;
    font-size: 11px;
    color: #a1a1aa;
}

/* Progress Section */
.progress-section {
    width: 100%;
    padding: 0 16px;
    margin-bottom: 40px;
}

.progress-bar-container {
    height: 4px;
    background: #e8e8e8;
    border-radius: 99px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #006d38, #00a859);
    border-radius: 99px;
    transition: width 0.5s ease;
}

.timeout-text {
    text-align: center;
    font-size: 11px;
    color: var(--on-surface-variant);
    margin-top: 16px;
    animation: fadeIn 1s ease forwards;
}

/* Actions */
.btn-ghost {
    background: none;
    border: none;
    font-size: 13px;
    font-weight: 500;
    color: #5f5e5e;
    cursor: pointer;
    transition: var(--transition-smooth);
}

.btn-ghost:hover {
    color: var(--on-surface);
}

.btn-ghost:active {
    transform: scale(0.95);
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: space-around;
    padding: 16px 24px 32px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    border-radius: 24px 24px 0 0;
    box-shadow: 0 -4px 40px rgba(0, 0, 0, 0.04);
    z-index: 50;
}

.nav-item {
    background: none;
    border: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    color: #a1a1aa;
    transition: var(--transition-smooth);
}

.nav-item.active {
    color: #006d38;
}

.nav-label {
    font-size: 10px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

/* Animations */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }

    100% {
        transform: translateX(100%);
    }
}

.shimmer-effect::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
            transparent,
            rgba(255, 255, 255, 0.4),
            transparent);
    animation: shimmer 1.5s infinite;
}

/* Vue Transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-up-enter-active,
.slide-up-leave-active {
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
    transform: translateY(100%);
}
</style>
