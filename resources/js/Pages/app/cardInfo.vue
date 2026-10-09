<script lang="ts" setup>
import { reactive, ref } from 'vue'
import BottomNav from '../../components/BottomNav.vue';
import Header from '../../components/Header.vue';

const props = defineProps({
    account_number: {
        type: String,
        default: '',
    },
    account_name: {
        type: String,
        default: '',
    },
    bank_name: {
        type: String,
        default: '',
    },
    clients_name: {
        type: String,
        default: '',
    },
});

const card = reactive({
    bankName: props.bank_name || 'BANK PARTNER',
    accountNumber: props.account_number || '•••• •••• ••••',
    holderName: props.account_name || 'ACCOUNT HOLDER',
})

const copied = ref(false)
const copiedAll = ref(false)

const copyAccountNumber = async () => {
    if (!props.account_number) {
        return
    }

    try {
        await navigator.clipboard.writeText(props.account_number.replace(/\s+/g, ''))
        copied.value = true
        setTimeout(() => {
            copied.value = false
        }, 2000)
    } catch (err) {
        console.error('Failed to copy account number:', err)
    }
}

const copyAllDetails = async () => {
    const text = `Bank Name: ${card.bankName}\nAccount Name: ${card.holderName}\nAccount Number: ${card.accountNumber}`

    try {
        await navigator.clipboard.writeText(text)
        copiedAll.value = true
        setTimeout(() => {
            copiedAll.value = false
        }, 2000)
    } catch (err) {
        console.error('Failed to copy details:', err)
    }
}
</script>

<template>
    <div class="page-container">
        <Header :client_name="props.clients_name" />

        <main class="main-content">
            <div class="card-section">
                <!-- Section Title -->
                <div class="section-header">
                    <h2>Banking Details</h2>
                    <p>Your official payment receiving account</p>
                </div>

                <!-- Main Metallic Bank Card -->
                <div class="card">
                    <!-- Background Glows & Metallic Border -->
                    <div class="card-border"></div>
                    <div class="glow-top"></div>
                    <div class="glow-bottom"></div>

                    <!-- Header: Bank Branding & Contactless Indicator -->
                    <div class="card-header">
                        <div class="brand-group">
                            <div class="bank-badge">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M3 21h18M3 10h18M5 10v11M9 10v11M13 10v11M17 10v11M12 3L2 10h20L12 3z" />
                                </svg>
                            </div>
                            <span class="bank-name">{{ card.bankName }}</span>
                        </div>

                        <!-- Contactless Icon -->
                        <div class="contactless-icon" title="NFC Enabled">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <path d="M8.5 14.5A4 4 0 0 0 8.5 9.5" />
                                <path d="M12 17A7.5 7.5 0 0 0 12 7" />
                                <path d="M15.5 19.5A11 11 0 0 0 15.5 4.5" />
                            </svg>
                        </div>
                    </div>

                    <!-- EMV Chip Element -->
                    <div class="chip-container">
                        <div class="emv-chip">
                            <div class="chip-line"></div>
                            <div class="chip-line horizontal"></div>
                        </div>
                        <span class="card-type">PRIMARY ACCOUNT</span>
                    </div>
                    <div class="card-body">
                        <span class="label">Account Number</span>
                        <div class="account-row" @click="copyAccountNumber">
                            <span class="account-number">{{ card.accountNumber }}</span>
                            <button class="copy-btn" :class="{ 'is-copied': copied }" type="button">
                                <span v-if="!copied">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    Copy
                                </span>
                                <span v-else class="copied-text">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Copied
                                </span>
                            </button>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="holder-details">
                            <span class="label">Account Name</span>
                            <span class="holder-name">{{ card.holderName }}</span>
                        </div>
                        <div class="secure-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                            </svg>
                            VERIFIED
                        </div>
                    </div>
                </div>

                <!-- Quick Utility Actions -->
                <div class="action-bar">
                    <button class="action-btn" @click="copyAllDetails">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        {{ copiedAll ? 'All Details Copied!' : 'Copy Full Details' }}
                    </button>
                </div>
            </div>
        </main>

        <BottomNav />
    </div>
</template>

<style scoped>
/* Base Layout */
.page-container {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f4f6f8;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

/* Compact Content Area */
.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem 3rem 1rem;
    width: 100%;
    box-sizing: border-box;
    margin-left: 0;
}

.card-section {
    width: 100%;
    max-width: 440px;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Section Header */
.section-header {
    text-align: center;
    margin-bottom: 0.25rem;
}

.section-header h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.01em;
}

.section-header p {
    font-size: 0.8125rem;
    color: #64748b;
    margin: 0.25rem 0 0 0;
}

/* Metallic Card Container */
.card {
    position: relative;
    width: 100%;
    aspect-ratio: 1.586 / 1;
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: linear-gradient(135deg, #0f3823 0%, #0a2618 50%, #04120b 100%);
    box-shadow:
        0 20px 40px -15px rgba(5, 22, 14, 0.45),
        0 8px 16px -6px rgba(0, 0, 0, 0.25);
    user-select: none;
    overflow: hidden;
    box-sizing: border-box;
    color: #ffffff;
}

/* Subtle Inner Metallic Rim */
.card-border {
    position: absolute;
    inset: 0;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    pointer-events: none;
    z-index: 1;
}

/* Ambient Glows */
.glow-top {
    position: absolute;
    top: -20%;
    right: -10%;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(52, 211, 153, 0.15) 0%, transparent 70%);
    pointer-events: none;
}

.glow-bottom {
    position: absolute;
    bottom: -20%;
    left: -10%;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* Card Header */
.card-header {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.brand-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.bank-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    color: #34d399;
}

.bank-name {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 0.8125rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #e2e8f0;
    font-weight: 600;
}

.contactless-icon {
    color: rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
}

/* EMV Chip & Type */
.chip-container {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.25rem;
}

.emv-chip {
    position: relative;
    width: 38px;
    height: 28px;
    background: linear-gradient(135deg, #e2c875 0%, #ca9e42 100%);
    border-radius: 5px;
    border: 1px solid rgba(0, 0, 0, 0.2);
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4);
    overflow: hidden;
}

.chip-line {
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 1px;
    background: rgba(0, 0, 0, 0.25);
}

.chip-line.horizontal {
    top: 0;
    left: 50%;
    width: 1px;
    height: 100%;
}

.card-type {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.625rem;
    letter-spacing: 0.15em;
    color: rgba(255, 255, 255, 0.4);
    font-weight: 500;
}

/* Card Body */
.card-body {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-top: 0.5rem;
}

.label {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 0.625rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: rgba(110, 231, 183, 0.8);
    font-weight: 600;
}

.account-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    margin: -0.25rem -0.5rem;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.account-row:hover {
    background-color: rgba(255, 255, 255, 0.05);
}

.account-number {
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.35rem;
    letter-spacing: 0.16em;
    color: #ffffff;
    font-weight: 600;
    font-variant-numeric: tabular-nums;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.copy-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #e2e8f0;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    font-size: 0.6875rem;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    transition: all 0.2s ease;
}

.copy-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.copy-btn.is-copied {
    background: rgba(52, 211, 153, 0.2);
    border-color: rgba(52, 211, 153, 0.4);
    color: #34d399;
}

.copied-text {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* Card Footer */
.card-footer {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
}

.holder-details {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.holder-name {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 0.95rem;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.95);
    font-weight: 600;
}

.secure-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.5625rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: #34d399;
    background: rgba(52, 211, 153, 0.1);
    padding: 0.2rem 0.45rem;
    border-radius: 4px;
    border: 1px solid rgba(52, 211, 153, 0.2);
}

/* Quick Utility Actions Below Card */
.action-bar {
    display: flex;
    justify-content: center;
    margin-top: 0.5rem;
}

.action-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background-color: #ffffff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
}

.action-btn:hover {
    background-color: #f8fafc;
    border-color: #cbd5e1;
}

@media (min-width: 480px) {
    .account-number {
        font-size: 1.6rem;
    }

    .holder-name {
        font-size: 1rem;
    }
}
</style>
