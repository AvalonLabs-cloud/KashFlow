<script lang="ts" setup>
import { usePage } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue';
import { onMounted, nextTick } from 'vue'
import ConfirmTransaction from '@/components/ConfirmTransaction.vue';
import Spinner from '@/components/Spinner.vue';
import TransferHeader from '@/components/TransferHeader.vue';
import ErrorNotification from '@/components/ui/ErrorNotification.vue';
import { useTransactionFee } from '@/composables/useTransactionFee';
import { useTransferStore } from '@/stores/transfer';

const input = ref<HTMLInputElement | null>(null)
const openKeyboard = () => {
    input.value?.focus()
}

const props = defineProps([
    'available_balance',
])
const page = usePage();

watch(
    () => page.props.flash.error,
    (error) => {
        console.log('FLASH ERROR CHANGED:', error)
    },
    { immediate: true }
)

onMounted(async () => {
    await nextTick()

})
const amount = ref('0')

const { 
        fee,
        feeIsLoading,
        fetchFee,
 } = useTransactionFee()


const handleConfirm = () => {

    fetchFee(amount.value)
    confirm.value = !confirm.value;
};

const confirm = ref(false);
const transactionType = ref('transfer')




interface Transfer {
    id: string;
    accountNumber: string;
    bankName: string;
    bankCode: string;
    accountName: string;
    createdAt: string;
}
const form = useForm({
    amount: '',
    accountNumber: '',
    bankCode: '',
    recipientname: '',
    transactionType: '',
})

function assignFormObjectparameteres() {
    Object.assign(form, {
        amount: amount.value,
        accountNumber: transferData.value?.accountNumber,
        bankCode: transferData.value?.bankCode,
        recipientname: transferData.value?.accountName,
        transactionType: transactionType.value,
    })
}

const transferStore = useTransferStore();

const transferId = computed(() => {
    const urlParts = page.url.split('?');

    if (urlParts.length < 2) {
        return null
    }

    const params = new URLSearchParams(urlParts[1]);

    return params.get('transferId');
});

const transferData = computed<Transfer | null>(() => {
    return transferId.value ? transferStore.getTransfer(transferId.value) : null;
});

const handleCompleteProcess = () => {
    assignFormObjectparameteres()
    form.post('/transaction/transfer')
    toggleConfirm()
}

const toggleConfirm = () => {
    confirm.value = !confirm.value;
}
</script>

<style scoped>
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* Ensure global fonts are available via main layout or index.html */
.font-headline {
    font-family: 'Manrope', sans-serif;
}

.font-body {
    font-family: 'Inter', sans-serif;
}

.font-label {
    font-family: 'Inter', sans-serif;
}
</style>

<template>
    <div class="bg-surface text-on-surface min-h-screen flex flex-col items-center font-body">
        <TransferHeader :name="'Transfer Amount'" />
        <ErrorNotification v-if="page.props.flash.error" :error="page.props.flash.error"
            :message="page.props.flash.error" />
        <main class="w-full max-w-md px-6 pt-8 pb-32 space-y-10">
            <section class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <span
                        class="font-label text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant/50">
                        Recipient Details
                    </span>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors duration-500"
                        :class="transferData ? 'bg-primary/10 text-primary' : 'bg-error/10 text-error'">
                        <span class="w-1.5 h-1.5 rounded-full animate-pulse"
                            :class="transferData ? 'bg-primary' : 'bg-error'"></span>
                        {{ transferData ? 'Verified' : 'Searching' }}
                    </div>
                </div>

                <div class="relative bg-white rounded-2xl p-6 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border transition-all duration-500 overflow-hidden"
                    :class="transferData ? 'border-primary/10' : 'border-error/10'">
                    <div class="absolute -top-12 -right-12 w-32 h-32 rounded-full blur-3xl transition-colors duration-700"
                        :class="transferData ? 'bg-primary/5' : 'bg-error/5'"></div>
                    <div
                        class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-white/80 to-transparent">
                    </div>

                    <div class="flex flex-col items-center relative z-10">

                        <h2 class="font-headline text-lg font-extrabold text-on-surface tracking-tight">
                            {{ transferData?.accountName || 'Fetching Account...' }}
                        </h2>

                        <div class="flex flex-col items-center mt-1 space-y-1">
                            <span
                                class="font-body text-sm font-medium text-on-surface-variant/70 tabular-nums bg-surface-container-low px-3 py-0.5 rounded-full border border-white/50">
                                {{ transferData?.accountNumber || '••••••••••' }}
                            </span>
                            <span
                                class="font-body text-[11px] font-semibold text-on-surface-variant/40 uppercase tracking-[0.15em]">
                                {{ transferData?.bankName || 'Verifying Bank' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <div
                    class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_12px_40px_rgb(0,0,0,0.04)] border border-primary/5">
                    <label
                        class="font-label text-xs font-semibold uppercase tracking-widest text-on-surface-variant block mb-6">Amount</label>
                    <div class="relative flex items-center group">
                        <span class="font-headline text-4xl font-extrabold text-primary mr-3">₦</span>
                        <input @click="openKeyboard" v-model="amount"
                            class="w-full bg-transparent border-none p-0 font-headline text-5xl font-bold text-on-surface focus:ring-0 focus:outline-none placeholder:text-surface-variant transition-all"
                            inputmode="numeric" placeholder="0" type="text" />
                    </div>
                    <div class="mt-8 pt-6 border-t border-surface-variant/30 flex justify-between items-center">
                        <span class="text-on-surface-variant text-sm font-medium">Available Balance</span>
                        <span class="text-on-surface font-semibold">₦{{ props.available_balance }}</span>
                    </div>
                </div>
            </section>
        </main>

        <div class="fixed bottom-0 left-0 w-full p-6 bg-surface/80 backdrop-blur-xl z-40">
            <div class="max-w-md mx-auto">
                <button @click="handleConfirm"
                    class="w-full h-16 bg-gradient-to-br from-[#006e2a] to-[#00c853] text-white font-headline font-bold text-lg rounded-xl shadow-lg shadow-primary/20 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    <span v-if="!form.processing">Next</span>
                    <span v-else>
                        <Spinner />
                    </span>
                </button>
            </div>
        </div>
    </div>
    <ConfirmTransaction :transactionFeesLoading="feeIsLoading" :transactionType="transactionType" :show="confirm" :amount="amount" @close="toggleConfirm"
        @completeProcess="handleCompleteProcess" :availableBalance="$page.props.availableBalance" :transactionFees="fee" :transferData="transferData"/>
</template>
