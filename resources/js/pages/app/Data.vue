<template>
    <div class="app-shell">
        <BackAndHistoryHeader :page-name="'Data'" />
        <ErrorNotification v-if="page.props.flash.error" :error="page.props.flash.error"
            :message="page.props.flash.error" />
        <main class="main-container">
            <section class="input-section">
                <div class="form-card">
                    <div class="input-group">
                        <div class="input-wrapper" :class="{ 'is-focused': isInputFocused }">
                            <input v-model="phoneNumber" type="tel" class="phone-input" placeholder="Enter number"
                                @focus="isInputFocused = true" @blur="isInputFocused = false" />
                        </div>
                        <div class="network-pill">
                            <select class="drop-down" v-model="selectedProvider">
                                <option v-for="(provider, index) in dataProviders" :key="index" :value="provider">
                                    {{ provider }}
                                </option>
                            </select>
                            <span v-if="selectedProvider === 'MTN'">MTN</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="tabs-section">
                <div class="tabs-scroll-container hide-scrollbar">
                    <button v-for="(tab) in categoriesToIterate" :key="tab[0]" class="tab-btn"
                        :class="{ 'tab-active': activeCategory == tab[0] }" @click="activeCategory = tab[0];">
                        {{ tab[1] }}
                    </button>
                </div>
            </section>

            <section>
                <div v-if="!isLoading" class="plans-grid">
                    <div v-for="(plan, item_code) in filteredPlans" :key="item_code" class="plan-card"
                        :class="{ 'plan-selected': selectedPlanItemCode === item_code }"
                        @click="selectedPlanItemCode = item_code; selectedItemPrice = plan.amount">
                        <p class="plan-data">{{ plan.name }}</p>
                        <p class="plan-price">₦{{ plan.amount }}</p>
                        <p class="plan-validity">{{ plan.validity_period }} <span
                                v-if="daysLabel(plan.validity_period) === 'days'">days</span> <span v-else>day</span>
                        </p>
                    </div>
                </div>

                <div v-if="isLoading" class="loading-content">
                    <Spinner />
                </div>
            </section>
        </main>

        <!-- Bottom Action Area -->
        <footer class="action-footer">
            <div class="footer-inner">
                <button class="buy-btn" :disabled="!selectedItemPrice" @click="confirmPurchase">
                    <template v-if="!isProcessing">Continue</template>
                    <span v-else class="loader"></span>
                </button>
            </div>
        </footer>
    </div>
    <ConfirmTransaction :transactionFeesLoading="feeIsLoading" :transactionType="transactionType" :show="confirm"
        :amount="selectedItemPrice" @close="toggleConfirm" :availableBalance="$page.props.availableBalance"
        :transactionFees="fee" @confirm="completePurchase" :recipient="phoneNumber"
        @completeProcess="completePurchase" />
</template>

<script lang="ts" setup>
import { useForm, usePage } from '@inertiajs/vue3'
import axios from "axios";
import { ref, computed, watch } from 'vue';
import BackAndHistoryHeader from '@/components/BackAndHistoryHeader.vue';
import ConfirmTransaction from '@/components/ConfirmTransaction.vue';
import ErrorNotification from '@/components/ui/ErrorNotification.vue';
import { Spinner } from '@/components/ui/spinner';
import { useTransactionFee } from '@/composables/useTransactionFee';

const phoneNumber = ref('09132026039');
const isInputFocused = ref(false);
const activeCategory = ref('1');
const selectedPlanItemCode = ref();
const selectedItemPrice = ref();
const isProcessing = ref(false);


// Strongly initialize with MTN
const selectedProvider = ref('MTN');

const isLoading = ref(false);
const confirm = ref(false);
const transactionType = 'data';
const props = defineProps([
    'bundle_list', 'bundle_category', 'bundle_providers'
]);
const page = usePage()

const {
    fee,
    feeIsLoading,
    feeError,
    fetchFee,
} = useTransactionFee()


const form = useForm({
    phoneNumber: '',
    transactionType: '',
    itemCode: '',
})

function assignFormObjectparameteres() {
    Object.assign(form, {
        phoneNumber: phoneNumber.value,
        itemCode: selectedPlanItemCode.value,
        transactionType: transactionType,
    })
}

const daysLabel = (validityPeriod) => {
    const value = Number(validityPeriod)

    return value > 1 ? 'days' : 'day'
}

const bundleListIntermediate = ref(props.bundle_list);

const dataProviders = computed(() => {
    // Fallback to ensuring MTN is always an option if props delay
    if (!props.bundle_providers || !Array.isArray(Object.entries(props.bundle_providers))) {
        return ['MTN', 'GLO', 'AIRTEL', '9MOBILE'];
    }

    return props.bundle_providers;
});

const categoryIntermediate = ref(props.bundle_category);

const categories = computed(() => {
    if (!categoryIntermediate.value || !Array.isArray(Object.entries(categoryIntermediate.value))) {
        return [];
    }

    return Object.entries(categoryIntermediate.value).map((item) => {
        return { [item[1] as string]: categoryName(item[1]) };
    });
})

const categoriesToIterate = computed(() => {
    if (!categories.value || !Array.isArray(categories.value)) {
        return [];
    }

    return categories.value.map(obj => Object.entries(obj)[0])
})

const categoryName = (category) => {
    switch (category) {
        case '1': return 'Daily';
        case '7': return 'Weekly';
        case '30': return 'Monthly';
        case '365': return 'Yearly';
        default: return category;
    }
};

const fetchBundleData = async (selectedProviderValue) => {
    isLoading.value = true;

    try {
        const response = await axios.get('/data_items', {
            params: { selectedProviderValue: selectedProviderValue }
        });
        const { bundle_category, bundle_list } = response.data;
        categoryIntermediate.value = bundle_category;
        bundleListIntermediate.value = bundle_list;
    } catch (error) {
        console.error('Error fetching bundle data:', error);
    } finally {
        isLoading.value = false;
    }
};

const toggleConfirm = () => {
    confirm.value = !confirm.value;
};


const filteredPlans = computed(() => {
    if (!props.bundle_list || !Array.isArray(Object.entries(props.bundle_list))) {
        return [];
    }

    return Object.fromEntries(Object.entries(bundleListIntermediate.value).filter(([key, plan]) => plan?.validity_period == activeCategory.value));
});

const isFormValid = computed(() => {
    return phoneNumber.value.replace(/\s/g, '').length >= 10 && selectedPlanItemCode.value !== null;
});


watch(selectedProvider, (newProvider) => {
    fetchBundleData(newProvider)
}, { immediate: true });

const confirmPurchase = () => {
    fetchFee(selectedItemPrice.value)
    confirm.value = true;
}

const completePurchase = () => {
    assignFormObjectparameteres();
    confirm.value = !confirm.value;
    form.post('/transaction/data')
}
</script>

<style scoped>
/* --- Design Tokens & Variables (Premium White & Emerald Green) --- */
:root {
    --primary: #047857;
    /* Deep, premium emerald green */
    --primary-hover: #065F46;
    /* Darker emerald for interactions */
    --background: #F9FAFB;
    /* Crisp, ultra-light gray to make white cards pop */
    --surface: #FFFFFF;
    /* Pure white */
    --surface-hover: #F0FDF4;
    /* Very subtle green tint for hovers */
    --surface-muted: #F3F4F6;
    --border: #E5E7EB;
    --border-active: #047857;
    --text-main: #111827;
    /* Near black */
    --text-muted: #6B7280;
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --shadow-soft: 0 4px 24px -4px rgba(4, 120, 87, 0.06);
    --transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
}

/* --- Global Layout --- */
.app-shell {
    background-color: var(--background);
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Hanken Grotesk', 'SF Pro Text', sans-serif;
    color: var(--text-main);
    width: 100%;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

.main-container {
    padding: 80px 20px 140px;
    max-width: 800px;
    margin: 0 auto;
}

/* --- Form Components --- */
.form-card {
    background: var(--surface);
    padding: 24px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-soft);
    border: 1px solid var(--border);
    margin-bottom: 32px;
}

.form-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.input-label {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.text-link-btn {
    background: none;
    border: none;
    color: var(--primary);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    transition: var(--transition);
}

.text-link-btn:hover {
    background: var(--surface-hover);
}

.input-group {
    display: flex;
    gap: 12px;
}

.input-wrapper {
    flex: 1;
    background: var(--surface-muted);
    border-radius: var(--radius-md);
    padding: 14px 20px;
    border: 1px solid transparent;
    transition: var(--transition);
}

.input-wrapper.is-focused {
    background: var(--surface);
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.08);
    /* Green focus ring */
}

.phone-input {
    background: transparent;
    border: none;
    width: 100%;
    font-size: 18px;
    font-weight: 600;
    color: var(--text-main);
    outline: none;
    letter-spacing: 0.5px;
}

.phone-input::placeholder {
    color: #9CA3AF;
    font-weight: 500;
}

.network-pill {
    background: var(--surface-muted);
    border-radius: var(--radius-md);
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid transparent;
    transition: var(--transition);
    cursor: pointer;
    position: relative;
}

.network-pill:hover {
    background: var(--surface-hover);
    border-color: rgba(4, 120, 87, 0.2);
}

.network-icon {
    width: 24px;
    height: 24px;
    object-fit: cover;
    border-radius: 50%;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.drop-down {
    appearance: none;
    background: transparent;
    border: none;
    font-weight: 600;
    color: var(--text-main);
    font-size: 14px;
    cursor: pointer;
    outline: none;
    padding-right: 4px;
}

/* --- Category Tabs (iOS Segmented Control Style) --- */
.tabs-section {
    margin-bottom: 28px;
}

.tabs-scroll-container {
    display: inline-flex;
    background: var(--surface-muted);
    padding: 4px;
    border-radius: 100px;
    gap: 4px;
    overflow-x: auto;
    width: 100%;
}

.hide-scrollbar {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.hide-scrollbar::-webkit-scrollbar {
    display: none;
}

.tab-btn {
    flex: 1;
    white-space: nowrap;
    padding: 10px 20px;
    border-radius: 100px;
    background: transparent;
    color: var(--text-muted);
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.tab-btn:hover:not(.tab-active) {
    color: var(--primary);
    background: var(--surface-hover);
}

.tab-btn.tab-active {
    background: var(--surface);
    color: #047857;
    /* Green text for active tab */
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* --- Grid Section --- */
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 14px;
    margin-bottom: 48px;
}

.plan-card {
    background: var(--surface);
    padding: 20px 16px;
    border-radius: var(--radius-lg);
    text-align: center;
    border: 1px solid var(--border);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.plan-card:hover {
    border-color: rgba(4, 120, 87, 0.3);
    transform: translateY(-2px);
    box-shadow: var(--shadow-soft);
}

.plan-card:active {
    transform: scale(0.97);
}

.plan-card.plan-selected {
    border-color: var(--primary);
    background-color: #047857;
    color: var(--surface);
}

.plan-data {
    font-size: 13px;
    font-weight: 500;
    margin: 0;
    color: inherit;
    opacity: 0.9;
}

.plan-price {
    font-size: 18px;
    font-weight: 700;
    margin: 6px 0;
    color: inherit;
    letter-spacing: -0.5px;
}

.plan-validity {
    font-size: 11px;
    font-weight: 500;
    color: inherit;
    opacity: 0.7;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* --- Action Footer --- */
.action-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    /* Pure white frosted glass */
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 20px 24px 32px;
    border-top: 1px solid var(--border);
    z-index: 40;
}

.footer-inner {
    max-width: 800px;
    margin: 0 auto;
}

.buy-btn {
    width: 100%;
    background: #047857;
    color: var(--surface);
    border: none;
    padding: 18px;
    border-radius: var(--radius-lg);
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(4, 120, 87, 0.2);
    transition: var(--transition);
    display: flex;
    justify-content: center;
    align-items: center;
}

.buy-btn:hover:not(:disabled) {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(4, 120, 87, 0.25);
}

.buy-btn:disabled {
    background: var(--surface-muted);
    color: var(--text-muted);
    box-shadow: none;
    cursor: not-allowed;
    border: 1px solid var(--border);
}

.buy-btn:active:not(:disabled) {
    transform: scale(0.98);
}

/* --- Loading Spinner --- */
.loader {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-bottom-color: #FFF;
    border-radius: 50%;
    display: inline-block;
    animation: rotation 0.8s linear infinite;
}

@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.loading-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 0;
    gap: 16px;
}

.spinner {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid var(--surface-hover);
    border-top-color: var(--primary);
    animation: spin 0.8s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.loading-text {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 500;
}
</style>
