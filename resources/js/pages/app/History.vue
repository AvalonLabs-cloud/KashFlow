<template>
    <div class="app-container">
        <!-- Top App Bar -->
        <header class="top-app-bar">
            <div class="app-bar-left">
                <button @click="navigateback" class="icon-button" aria-label="Go back">
                    <span class="material-symbols-outlined icon-primary">arrow_back</span>
                </button>
                <h1 class="app-title">Transaction History</h1>
            </div>
            <!-- <button class="icon-button" aria-label="Search transactions">
        <span class="material-symbols-outlined icon-primary">search</span>
      </button> -->
        </header>

        <main class="main-content">
            <!-- Horizontal Filter Chips & Filter Toggle -->
            <div class="filter-toolbar">
                <div class="filter-chips-container no-scrollbar">
                    <button v-for="category in categories" :key="category" class="chip"
                        :class="{ 'chip-active': selectedCategory === category }" @click="selectCategory(category)">
                        {{ category }}
                    </button>
                </div>
                <button class="filter-toggle-btn" @click="toggleFilterSheet" aria-label="Open advanced filters"
                    :aria-expanded="isFilterSheetOpen">
                    <span class="material-symbols-outlined">filter_list</span>
                </button>
            </div>

            <!-- Transaction List -->
            <div class="transaction-list">



                <!-- Grouped Transactions -->
                <!-- <section v-for="(group, groupIndex) in transactionGroups" :key="groupIndex" class="transaction-group"> -->
                <!-- <h2 class="group-header">{{ group.date }}</h2> -->
                <InfiniteScroll data="history">
                    <div class="group-items">
                        <div v-for="tx in historyData.data" :key="tx.id" class="transaction-card"
                        @click="goToDetails(tx.transaction_reference)"
                            :class="{ 'opacity-dim': tx.status === 'Failed' }"
                            role="button" tabindex="0">
                            <!-- <div class="tx-icon-wrapper" :class="`bg-${tx.iconTheme}`">
                <span class="material-symbols-outlined tx-icon" :class="`text-${tx.iconTheme}`" style="font-variation-settings: 'FILL' 1;">
                  {{ tx.icon }}
                </span>
              </div> -->

                            <div class="tx-details">
                                <h3 class="tx-title">{{ tx.recipient }}</h3>
                                <p class="tx-subtitle">{{ tx.type }}</p>
                            </div>
                            <div class="tx-amount-col">
                                <p class="tx-amount" :class="getAmountColorClass(tx.type)">
                                    {{ tx.direction === 'credit' ? '+' : '-' }}${{ tx.amount }}
                                </p>
                                <!-- <span class="tx-status" :class="`status-${tx.status.toLowerCase()}`">
                  {{ tx.status }}
                </span> -->

                            </div>

                        </div>
                    </div>
                </InfiniteScroll>


                <!-- </section> -->



            </div>
        </main>

        <!-- Filter Bottom Sheet Overlay -->
        <div class="bottom-sheet-overlay" :class="{ 'overlay-visible': isFilterSheetOpen }" @click="toggleFilterSheet">
        </div>

        <!-- Filter Bottom Sheet -->
        <div class="bottom-sheet" :class="{ 'sheet-open': isFilterSheetOpen }">
            <div class="sheet-drag-handle"></div>

            <div class="sheet-header">
                <h2 class="sheet-title">Filter By</h2>
                <button class="sheet-reset-btn" @click="resetFilters">Reset</button>
            </div>

            <div class="sheet-content">
                <div class="filter-section">
                    <p class="filter-section-title">Date Range</p>
                    <div class="date-range-grid">
                        <div class="">
                        <button @click="openStartDatePicker" class="date-btn">Start Date</button>
                        <input id="date-picker" type="date" @change="handleStartDateChange"
                            style="position: absolute; opacity: 0; pointer-events: none;" />

                        <p v-if="selectedStartDate">
                            {{ selectedStartDate }}
                        </p>
                        </div>
                         <div class="">
                        <button @click="openEndDatePicker" class="date-btn">End Date</button>
                        <input id="date-picker-2" type="date" @change="handleEndDateChange"
                            style="position: absolute; opacity: 0; pointer-events: none;" />

                        <p v-if="selectedEndDate">
                            {{ selectedEndDate }}
                        </p>
                            </div>
                    </div>
                </div>

                <div class="filter-section">
                    <p class="filter-section-title">Status</p>
                    <div class="status-chips">
                        <button v-for="status in availableStatuses" :key="status" class="status-chip"
                            :class="{ 'status-chip-active': filterForm.statuses.includes(status) }"
                            @click="toggleStatus(status)">
                            {{ status }}
                        </button>
                    </div>
                </div>
            </div>

            <button class="apply-filters-btn" @click="applyFilters">
                Apply Filters
            </button>
            <div class="sheet-bottom-spacer"></div>
        </div>

        <!-- Bottom Nav Bar -->
        <nav class="bottom-nav">
            <button v-for="item in navItems" :key="item.label" class="nav-item"
                :class="{ 'nav-item-active': activeTab === item.label }" @click="selectTab(item.label)">
                <span class="material-symbols-outlined nav-icon"
                    :style="activeTab === item.label ? `font-variation-settings: 'FILL' 1;` : ''">
                    {{ item.icon }}
                </span>
                <span class="nav-label">{{ item.label }}</span>
            </button>
        </nav>
    </div>
</template>

<script lang="ts" setup>
import { InfiniteScroll } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
const props = defineProps([
    'history',
])


// Mock Data Definitions
const categories = [
    'All', 'transfer', 'airtime', 'data',
];

const availableStatuses = ['Successful', 'Pending', 'Failed'];

const navItems = [
    { label: 'Home', icon: 'home' },
    { label: 'Transactions', icon: 'account_balance_wallet' },
    { label: 'Cards', icon: 'credit_card' },
    { label: 'Profile', icon: 'person' },
];

const transactions = [
    { id: 1, title: 'John Doe', category: 'Transfer', source: 'Kuda Bank', amount: '500.00', type: 'credit', status: 'Successful', icon: 'account_balance_wallet', iconTheme: 'primary-container' },
    { id: 2, title: 'MTN Airtime', category: 'Airtime', source: 'Self', amount: '2,000.00', type: 'debit', status: 'Successful', icon: 'phone_android', iconTheme: 'secondary' },
];

const historyData = computed(() => props.history);
console.log(historyData.value);

const selectedStartDate = ref(null);

const openStartDatePicker = () => {
    const input = document.getElementById('date-picker');

    input.showPicker();
};

const handleStartDateChange = (event) => {
    selectedStartDate.value = event.target.value;

    console.log(selectedStartDate.value);
};

const selectedEndDate = ref(null);

const startDate = computed(()=> selectedStartDate.value)
const endDate = computed(()=> selectedEndDate.value)

const openEndDatePicker = () => {
    const input = document.getElementById('date-picker-2');

    input.showPicker();
};

const handleEndDateChange = (event) => {
    selectedEndDate.value = event.target.value;

    console.log(selectedStartDate.value);
};

const goToDetails = (reference) => {
     router.get('/transaction_detail' , {
      reference: reference
   })
}


// Reactive State
const selectedCategory = ref('All Transactions');
const activeTab = ref('Transactions');
const isFilterSheetOpen = ref(false);

const filterForm = reactive({
    startDate: null,
    endDate: null,
    statuses: []
});

const navigateback = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/main-index');
    }

}

// Methods
const selectCategory = (category) => {
    selectedCategory.value = category;
    submitFilters();
};

const toggleFilterSheet = () => {
    isFilterSheetOpen.value = !isFilterSheetOpen.value;

    if (isFilterSheetOpen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

const toggleStatus = (status) => {
    const index = filterForm.statuses.indexOf(status);

    if (index === -1) {
        filterForm.statuses.push(status);
    } else {
        filterForm.statuses.splice(index, 1);
    }
};

const resetFilters = () => {
    filterForm.startDate = null;
    selectedStartDate.value = null;
    selectedEndDate.value = null;
    filterForm.statuses = [];
};

const applyFilters = () => {
    toggleFilterSheet();
    submitFilters();
};

const submitFilters = () => {
    router.get('/history', {
        category: selectedCategory.value,
        startDate: startDate.value,
        endDate: endDate.value,
        statuses: filterForm.statuses
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['history'],
        reset: ['history']
    });
};

const selectTab = (tab) => {
    activeTab.value = tab;
    // Inertia visit could go here based on tab routing in a real app
};


// Utilities
const getAmountColorClass = (type) => {

    if (type === 'credit') {
        return 'text-amount-credit';
    }

    if (type === 'debit') {
        return 'text-amount-debit';
    }

    return 'text-amount-neutral';
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');

/* Base Variables */
:root,
.app-container {
    --color-background: #f8f9fa;
    --color-surface: #f8f9fa;
    --color-surface-container-lowest: #ffffff;
    --color-surface-container-low: #f3f4f5;
    --color-surface-container: #edeeef;
    --color-surface-container-high: #e7e8e9;
    --color-surface-container-highest: #e1e3e4;

    --color-on-background: #191c1d;
    --color-on-surface: #191c1d;
    --color-on-surface-variant: #3d4a3d;
    --color-outline-variant: #bccbb9;

    --color-primary: #006e2f;
    --color-on-primary: #ffffff;
    --color-primary-container: #22c55e;
    --color-on-primary-container: #004b1e;

    --color-secondary: #b61722;
    --color-tertiary: #565e74;

    --color-error: #ba1a1a;
    --color-error-container: #ffdad6;
    --color-on-error-container: #93000a;

    --spacing-mobile: 16px;
    --radius-xl: 16px;
    --radius-full: 9999px;
    --header-height: 64px;
    --nav-height: 72px;

    /* Typography */
    --font-family: 'Inter', sans-serif;
}

/* Global Reset/Setup */
.app-container {
    font-family: var(--font-family);
    background-color: var(--color-background);
    color: var(--color-on-background);
    min-height: 100vh;
    padding-bottom: 96px;
    /* space for bottom nav */
    position: relative;
    -webkit-font-smoothing: antialiased;
}

.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* Utilities */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s ease-in-out;
}

button:active {
    transform: scale(0.95);
}

/* Top App Bar */
.top-app-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: var(--header-height);
    background-color: var(--color-surface);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 var(--spacing-mobile);
    z-index: 50;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.app-bar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.icon-button {
    padding: 8px;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-button:hover {
    background-color: var(--color-surface-container-high);
}

.icon-primary {
    color: var(--color-primary);
}

.app-title {
    font-size: 24px;
    line-height: 32px;
    font-weight: 600;
    letter-spacing: -0.01em;
    color: var(--color-on-surface);
    margin: 0;
}

/* Main Content */
.main-content {
    padding-top: var(--header-height);
    padding-left: var(--spacing-mobile);
    padding-right: var(--spacing-mobile);
}

/* Filter Toolbar */
.filter-toolbar {
    position: sticky;
    top: var(--header-height);
    background-color: var(--color-background);
    z-index: 40;
    padding: 16px 0;
    margin: 0 calc(var(--spacing-mobile) * -1);
    padding-left: var(--spacing-mobile);
    padding-right: var(--spacing-mobile);
    display: flex;
    align-items: center;
    gap: 8px;
    overflow: hidden;
}

.filter-chips-container {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding-right: 16px;
}

.chip {
    white-space: nowrap;
    padding: 8px 16px;
    border-radius: var(--radius-full);
    background-color: var(--color-surface-container-low);
    color: var(--color-on-surface-variant);
    font-size: 14px;
    font-weight: 500;
    line-height: 20px;
    letter-spacing: 0.05em;
}

.chip:hover {
    background-color: var(--color-surface-container-high);
}

.chip-active {
    background-color: var(--color-primary);
    color: var(--color-on-primary);
}

.chip-active:hover {
    background-color: var(--color-primary);
    /* Keep solid on hover if active */
}

.filter-toggle-btn {
    padding: 10px;
    border-radius: 12px;
    background-color: var(--color-surface-container-highest);
    color: var(--color-on-surface);
    border: 1px solid rgba(188, 203, 185, 0.2);
    /* outline-variant/20 */
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: center;
}

.filter-toggle-btn:hover {
    background-color: var(--color-surface-container);
}

.filter-toggle-btn:active {
    transform: scale(0.9);
}

/* Transaction List */
.transaction-list {
    margin-top: 8px;
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.group-header {
    font-size: 12px;
    line-height: 16px;
    font-weight: 600;
    color: rgba(61, 74, 61, 0.6);
    /* on-surface-variant/60 */
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 16px;
}

.group-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.transaction-card {
    background-color: var(--color-surface-container-lowest);
    padding: 16px;
    border-radius: var(--radius-xl);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
    border: 1px solid var(--color-surface-container);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    cursor: pointer;
    outline: none;
}

.transaction-card:hover {
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
}

.transaction-card:focus-visible {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 2px rgba(0, 110, 47, 0.2);
}

.transaction-card:active {
    transform: scale(0.98);
}

.opacity-dim {
    opacity: 0.75;
}

/* Icon Themes */
.tx-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-primary-container {
    background-color: rgba(34, 197, 94, 0.1);
}

.text-primary-container {
    color: var(--color-primary-container);
}

.bg-secondary {
    background-color: rgba(182, 23, 34, 0.1);
}

.text-secondary {
    color: var(--color-secondary);
}

.bg-tertiary {
    background-color: rgba(86, 94, 116, 0.1);
}

.text-tertiary {
    color: var(--color-tertiary);
}

.bg-error {
    background-color: rgba(186, 26, 26, 0.1);
}

.text-error {
    color: var(--color-error);
}

.tx-details {
    flex: 1;
    min-width: 0;
    /* for truncation */
}

.tx-title {
    font-size: 16px;
    line-height: 24px;
    font-weight: 700;
    color: var(--color-on-surface);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0;
}

.tx-subtitle {
    font-size: 12px;
    line-height: 16px;
    font-weight: 600;
    color: var(--color-on-surface-variant);
    margin: 0;
}

.tx-amount-col {
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 2px;
}

.tx-amount {
    font-size: 18px;
    line-height: 28px;
    font-weight: 700;
    margin: 0;
}

.text-amount-credit {
    color: var(--color-primary);
}

.text-amount-debit {
    color: var(--color-error);
}

.text-amount-neutral {
    color: var(--color-on-surface-variant);
}

.tx-status {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: var(--radius-full);
}

.status-successful {
    background-color: rgba(34, 197, 94, 0.2);
    /* primary-container/20 */
    color: var(--color-on-primary-container);
}

.status-pending {
    background-color: var(--color-surface-container-high);
    color: var(--color-on-surface-variant);
}

.status-failed {
    background-color: rgba(186, 26, 26, 0.1);
    /* error/10 */
    color: var(--color-error);
}

/* Bottom Sheet */
.bottom-sheet-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(25, 28, 29, 0.4);
    /* on-background/40 */
    backdrop-filter: blur(4px);
    z-index: 60;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.bottom-sheet-overlay.overlay-visible {
    opacity: 1;
    pointer-events: auto;
}

.bottom-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: var(--color-surface-container-lowest);
    z-index: 70;
    border-top-left-radius: 24px;
    border-top-right-radius: 24px;
    padding: var(--spacing-mobile);
    padding-top: 16px;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.1);
    transform: translateY(100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.bottom-sheet.sheet-open {
    transform: translateY(0%);
}

.sheet-drag-handle {
    width: 48px;
    height: 6px;
    background-color: rgba(188, 203, 185, 0.3);
    /* outline-variant/30 */
    border-radius: var(--radius-full);
    margin: 0 auto 24px auto;
}

.sheet-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
}

.sheet-title {
    font-size: 24px;
    line-height: 32px;
    font-weight: 600;
    color: var(--color-on-surface);
    margin: 0;
}

.sheet-reset-btn {
    color: var(--color-primary);
    font-weight: 700;
    font-size: 14px;
}

.sheet-reset-btn:hover {
    opacity: 0.8;
}

.sheet-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-bottom: 40px;
}

.filter-section-title {
    font-size: 12px;
    line-height: 16px;
    font-weight: 600;
    color: var(--color-on-surface-variant);
    text-transform: uppercase;
    margin-bottom: 12px;
}

.date-range-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.date-btn {
    background-color: var(--color-surface-container);
    padding: 12px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--color-on-surface);
    border: 1px solid rgba(188, 203, 185, 0.1);
    text-align: center;
}

.date-btn:hover {
    background-color: var(--color-surface-container-high);
}

.status-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.status-chip {
    padding: 8px 16px;
    border-radius: var(--radius-full);
    border: 1px solid rgba(188, 203, 185, 0.3);
    color: var(--color-on-surface);
    font-size: 14px;
    font-weight: 500;
}

.status-chip:hover {
    background-color: var(--color-surface-container-high);
}

.status-chip-active {
    background-color: var(--color-primary);
    color: var(--color-on-primary);
    border-color: var(--color-primary);
}

.apply-filters-btn {
    width: 100%;
    padding: 16px;
    background-color: var(--color-on-background);
    color: var(--color-on-primary);
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
}

.apply-filters-btn:active {
    transform: scale(0.97);
}

.sheet-bottom-spacer {
    height: 32px;
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: var(--color-surface-container-lowest);
    border-top: 1px solid rgba(188, 203, 185, 0.3);
    /* outline-variant/30 */
    box-shadow: 0 -4px 12px rgba(15, 23, 42, 0.05);
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 12px var(--spacing-mobile);
    z-index: 50;
    padding-bottom: max(12px, env(safe-area-inset-bottom));
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--color-on-surface-variant);
    padding: 4px 16px;
    border-radius: var(--radius-full);
}

.nav-item:hover {
    color: var(--color-primary);
}

.nav-item-active {
    background-color: var(--color-primary-container);
    color: var(--color-on-primary-container);
}

.nav-item-active:hover {
    color: var(--color-on-primary-container);
}

.nav-icon {
    font-size: 24px;
    margin-bottom: 2px;
}

.nav-label {
    font-size: 12px;
    font-weight: 600;
    line-height: 16px;
}

/* Responsive adjustments */
@media (min-width: 768px) {
    .app-container {
        max-width: 768px;
        margin: 0 auto;
        border-left: 1px solid var(--color-surface-container);
        border-right: 1px solid var(--color-surface-container);
        box-shadow: 0 0 40px rgba(0, 0, 0, 0.05);
    }

    .top-app-bar,
    .bottom-nav,
    .bottom-sheet {
        max-width: 768px;
        left: 50%;
        transform: translateX(-50%);
    }

    .bottom-sheet {
        transform: translate(-50%, 100%);
    }

    .bottom-sheet.sheet-open {
        transform: translate(-50%, 0%);
    }
}
</style>
