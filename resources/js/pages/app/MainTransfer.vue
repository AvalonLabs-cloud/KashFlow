<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import axios from "axios";
import { ref, reactive, computed, toRefs, watch, onMounted } from 'vue';
import BackAndHistoryHeader from '@/components/BackAndHistoryHeader.vue';
import { useTransferStore } from "@/stores/transfer";
import BottomNav from '../../components/BottomNav.vue';
import ResolvedCard from '../../components/ResolvedCard.vue';
import TransferTransactionItem from '../../components/TransferTransactionItem.vue';

const transferStore = useTransferStore()

const formData = reactive({
    accountNumber: '',
    banks: {} as any,
    accountName: '',
});

const selectedBank = ref('');
const isValidating = ref(false);
const props = defineProps([
    'beneficiaries'
])

const beneficiaryData = computed(() => props.beneficiaries)

const { accountNumber, banks, accountName } = toRefs(formData);
const search = ref('')

watch([accountNumber, selectedBank], ([currentValueAccountNumber, currentValueBankName]) => {
    if (currentValueAccountNumber.length === 10 && currentValueBankName) {
        fetchAccountName()
    }

})

const searchBeneficiaries = () => {
    router.get(
        '/transfer',
        {
            search: search.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['beneficiaries'],
            reset: ['beneficiaries'],
        }
    )
}

const isSearching = ref(false);
const isProcessing = ref(false);

const isFormValid = computed(() => {
    return formData.accountNumber.length === 10 && selectedBank.value !== '';
});


const initiateTransfer = () => {
    const transferId = transferStore.addTransfer({
        accountNumber: formData.accountNumber,
        bankName: 'access bank',
        bankCode: selectedBank.value,
        accountName: accountName.value,
    });
     router.get(`/complete_transfer?transferId=${transferId}`);
};

async function getBanks() {
    const bankList = await axios.get('/flutterwave/banks');
    console.log(bankList)

    if (bankList) {
        banks.value = bankList.data.data
    }
}

const fetchAccountName = async () => {
    isValidating.value = true
    isSearching.value = true

    try {
        const response = await axios.post('/flutterwave/accounts/resolve', {
            accountNumber: accountNumber.value,
            bankCode: selectedBank.value
        })

        console.log(response)

        accountName.value = response.data.data.account_name

        console.log(accountName.value)

    } catch (error) {
        console.error(error)
    } finally {
        isValidating.value = false
        isSearching.value = false
    }
}
watch(search, () => {
    searchBeneficiaries()
})
const pageName = ref('Transfer')


onMounted(() => {
    getBanks();
});
</script>
<template>

    <div class="app-viewport">
        <BackAndHistoryHeader :page-name="pageName"/>
        <main class="main-content">
            <section class="form-card">
                <div class="input-group">
                    <label class="input-label">Recipient Account Number</label>
                    <div class="input-wrapper">
                        <input type="text" v-model="formData.accountNumber" placeholder="Enter 10-digit number"
                            class="base-input" />
                        <!-- <span class="material-symbols-outlined input-icon">contact_page</span> -->
                    </div>
                </div>

                <div class="input-group">
                    <label class="input-label">Select Bank</label>
                    <button class="select-btn">
                        <select class="base-input" v-model="selectedBank">
                            <option disabled value="">Select Bank</option>
                            <option v-for="bank in banks" :key="bank.code" :value="bank.code">
                                {{ bank.name }}
                            </option>
                        </select>
                    </button>
                </div>
                <ResolvedCard :accountName="accountName" :loading="isSearching" />
                <button class="submit-btn active-tap" :disabled="!accountName" @click="initiateTransfer">
                    <span v-if="!isProcessing">Confirm & Send Money</span>
                    <span v-else class="loader"></span>
                </button>
            </section>

            <section class="history-section">
                <div class="section-header">
                    <h2 class="section-title">Beneficiaries</h2>

                    <div class="transaction-search">
                        <svg class="search-icon" width="17" height="17" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-4-4"></path>
                        </svg>

                        <input type="search" placeholder="Search transactions" v-model="search" />
                    </div>
                </div>
                <div class="history-list">
                    <TransferTransactionItem v-for="tx in beneficiaryData.data" :key="tx.id" :tx="tx" />
                </div>
            </section>
            <BottomNav />
        </main>



        <div class="ambient-glow top"></div>
        <div class="ambient-glow bottom"></div>
    </div>
</template>



<style>
:root {
    --primary: #006a35;
    --primary-dim: #005c2d;
    --primary-container: #75fda0;
    --on-primary: #cdffd4;
    --on-primary-container: #005f2f;
    --on-primary-fixed-variant: #006a35;
    --secondary: #006946;
    --secondary-container: #72fbbd;
    --on-secondary-fixed: #004930;
    --on-secondary-fixed-variant: #006946;
    --surface: #f5f6f7;
    --on-surface: #2c2f30;
    --on-surface-variant: #595c5d;
    --surface-container-high: #e0e3e4;
    --surface-container-lowest: #ffffff;
    --error: #b31b25;
}

body {
    margin: 0;
    font-family: 'Inter', sans-serif;
    background-color: var(--surface);
    color: var(--on-surface);
}

.material-symbols-outlined.filled {
    font-variation-settings: 'FILL' 1;
}
</style>

<style scoped>
.app-viewport {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    z-index: 50;

}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    gap: 16px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}

.transaction-search {
    position: relative;
    width: 230px;
}

.transaction-search input {
    width: 100%;
    height: 40px;
    padding: 0 14px 0 40px;

    border: 1px solid #e5e7eb;
    border-radius: 10px;

    background: #fff;
    color: #111827;

    font-size: 13px;
    font-family: inherit;

    outline: none;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.transaction-search input::placeholder {
    color: #9ca3af;
}

.transaction-search input:hover {
    border-color: #d1d5db;
}

.transaction-search input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px color-mix(in srgb,
            var(--primary) 10%,
            transparent);
}


@media (max-width: 600px) {
    .section-header {
        flex-wrap: wrap;
    }

    .transaction-search {
        width: 100%;
        order: 2;
    }
}

.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);

    color: #9ca3af;
    pointer-events: none;
}

.main-content {
    padding: 30px 16px 120px 16px;
    display: flex;
    flex-direction: column;
    gap: 24px;
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
    box-sizing: border-box;
}

/* Form Styles */
.form-card {
    background-color: var(--surface-container-lowest);
    border-radius: 16px;
    padding: 24px;
    border: 1px solid rgba(171, 173, 174, 0.1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-label {
    font-size: 12px;
    color: var(--on-surface-variant);
    padding: 0 4px;
}

.input-wrapper,
.amount-wrapper {
    position: relative;
}

.base-input,
.select-btn {
    width: 100%;
    height: 56px;
    border-radius: 12px;
    border: none;
    background-color: var(--surface-container-high);
    padding: 0 16px;
    font-size: 16px;
    outline: none;
    box-sizing: border-box;
}

.select-btn {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--on-surface-variant);
    cursor: pointer;
}

.amount-input {
    width: 100%;
    height: 80px;
    border-radius: 12px;
    background-color: var(--surface-container-high);
    border: none;
    padding: 0 16px 0 40px;
    font-size: 32px;
    font-weight: 800;
    font-family: 'Manrope', sans-serif;
    outline: none;
}

.currency-symbol {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    font-weight: 800;
    color: var(--primary);
}

.balance-row {
    display: flex;
    justify-content: space-between;
    padding: 0 4px;
}

.available-text {
    font-size: 12px;
    color: var(--on-surface-variant);
}

.max-btn {
    border: none;
    background: none;
    color: var(--primary);
    font-weight: 700;
    cursor: pointer;
}

.submit-btn {
    width: 100%;
    background-color: var(--primary);
    color: #ffffff;
    border: none;
    padding: 16px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 8px 16px rgba(0, 106, 53, 0.2);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* History Section */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}

.text-link {
    color: var(--primary);
    font-weight: 700;
    background: none;
    border: none;
    cursor: pointer;
}

.history-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Bottom Nav */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 80px;
    background-color: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(20px);
    display: flex;
    justify-content: space-around;
    align-items: center;
    border-top: 1px solid #f0f0f0;
    padding-bottom: env(safe-area-inset-bottom);
}

.nav-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: none;
    border: none;
    color: #6b7280;
}

.nav-btn.active {
    background-color: #dcfce7;
    color: #166534;
    padding: 8px 16px;
    border-radius: 16px;
}

/* Decorative Glows */
.ambient-glow {
    position: fixed;
    pointer-events: none;
    z-index: -1;
    border-radius: 50%;
    filter: blur(100px);
}

.top {
    top: -10%;
    right: -10%;
    width: 50%;
    height: 40%;
    background: rgba(0, 106, 53, 0.05);
}

.bottom {
    bottom: 0;
    left: -10%;
    width: 40%;
    height: 30%;
    background: rgba(0, 105, 70, 0.05);
}

/* Utility */
.active-tap:active {
    transform: scale(0.95);
}
</style>
