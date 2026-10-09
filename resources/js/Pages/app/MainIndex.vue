<script lang="ts" setup>
import { router, usePage } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';
import { useSnackbar } from "vue3-snackbar";
import BottomNav from '../../components/BottomNav.vue';
import Header from '../../components/Header.vue';
import PrimaryWalletCard from '../../components/PrimaryWalletCard.vue';
import QuickActions from '../../components/QuickActions.vue';
import TransactionList from '../../components/TransactionList.vue';
const props = defineProps(['balanceShowImage', 'balanceHideImage', 'clients_name', 'userId' , 'balance' , 'recentTransactions']);


const pageProp = usePage()
const snackbar = useSnackbar();
// const walletBalance = ref('');

const wallet = reactive({
    balance: props.balance,
    currency: "₦"
});


const isBalanceVisible = ref(true);


const showMessageIfAvailable = () => {
    if (pageProp.props.flash.successfull_account_creation) {
        snackbar.add({
            type: 'success',
            text: 'Account Creation Successfull'
        })
    }
}

const navigateToSendMoney = () =>{
    router.get('/cardInfo')
}

watch(undefined, () => {
    showMessageIfAvailable()
}, {
    immediate: true
})

</script>


<template>
    <div class="app-layout">
        <Header :client_name="props.clients_name" />
        <vue3-snackbar top bottom shadow :dismiss-on-action-click="false"></vue3-snackbar>
        <main class="main-content">
            <PrimaryWalletCard :wallet="wallet" :isVisible="isBalanceVisible"
                @toggle-visibility="isBalanceVisible = !isBalanceVisible"
                @navigate-send-money="navigateToSendMoney"
                :togglebtnStates="{ show: props.balanceShowImage, hide: props.balanceHideImage }" />
            <QuickActions />
            <TransactionList :recentTransactions="props.recentTransactions" />
        </main>
        <BottomNav />
    </div>
</template>



<style>

:root {
    --primary: #006c4f;
    --primary-container: #00b386;
    --background: #f8f9fb;
    --surface: #ffffff;
    --on-surface: #191c1e;
    --on-surface-variant: #3c4a43;
    --surface-container: #edeef0;
    --surface-container-lowest: #ffffff;
    --error: #ba1a1a;
    --outline: #6c7a73;
}

body {
    font-family: 'Inter', sans-serif;
    -webkit-font-smoothing: antialiased;
    background-color: var(--background);
    color: var(--on-surface);
    margin: 0;
    min-height: 100vh;
}

h1,
h2,
h3 {
    font-family: 'Manrope', sans-serif;
}

* {
    box-sizing: border-box;
}

/* Material Icons Baseline Setup */
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>

<style scoped>
.app-layout {
    min-height: 100vh;
    padding-bottom: 96px;
    /* Space for BottomNav */
}

.main-content {
    padding: 10px 9px 20px 9px;
    /* Space for Header at top */
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 375px;
    margin: 0 auto;
}
</style>
