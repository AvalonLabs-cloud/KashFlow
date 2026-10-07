<script lang="ts" setup>
import axios from "axios";
import { reactive, toRefs, watch, computed, } from 'vue';

const transferState = reactive({
    transferFee: 0,
    transferLoader: false,
    transferFeeError: false,
})

const { transferFee, transferLoader, transferFeeError } = toRefs(transferState)




interface Transfer {
    id: string;
    accountNumber: string;
    bankName: string;
    bankCode: string;
    accountName: string;
    createdAt: string;
}

const props = withDefaults(defineProps<{
    isOpen?: boolean
    amount?: number
    transferInfo?: Transfer | null
}>(), {
    isOpen: false,
    amount: 0,
    transferInfo: null,
})

const emit = defineEmits<{
    (e: 'close'): void
    (e: 'confirm'): void
}>()

const completeMoneyToTranfer = computed(() => {
    return props.amount + transferFee.value
})

watch(
    () => props.isOpen,
    async (newwValue) => {
        if (newwValue) {
            transferFeeError.value = false;
            transferLoader.value = true;
            await axios.get('/flutterwave/transaction_fees', {
                params: {
                    amount: props.amount
                }
            })
                .then(response => {
                    console.log(response);

                    transferFee.value = response.data[0].fee;
                })
                .catch(error => {
                    transferFeeError.value = true;
                    console.log(error);

                })
                .finally(() => {
                    transferLoader.value = false
                })
        }
    })



</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="props.isOpen" @click="emit('close')"
                class="fixed inset-0 bg-on-background/20 backdrop-blur-sm z-[60]"></div>
        </Transition>

        <Transition enter-active-class="transition duration-500 ease-out" enter-from-class="translate-y-full"
            enter-to-class="translate-y-0" leave-active-class="transition duration-400 ease-in"
            leave-from-class="translate-y-0" leave-to-class="translate-y-full">
            <div v-if="props.isOpen" class="fixed bottom-0 left-0 right-0 z-[70]">
                <div
                    class="bg-surface-container-lowest rounded-t-[2rem] shadow-[0_-12px_40px_rgba(0,0,0,0.15)] pb-10 pt-4 px-6 max-w-2xl mx-auto border-t border-outline-variant/10">

                    <div class="w-12 h-1.5 bg-outline-variant/30 rounded-full mx-auto mb-8"></div>

                    <div class="space-y-8">
                        <div class="text-center space-y-1">
                            <p class="text-on-surface-variant font-medium text-sm tracking-wide">TOTAL PAYMENT</p>
                            <h2 class="font-headline font-extrabold text-5xl text-on-surface">₦{{
                                completeMoneyToTranfer }}</h2>
                        </div>

                        <div class="bg-surface-container-low/30 rounded-3xl p-6 space-y-5">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-on-surface-variant">Amount</span>
                                <span class="font-bold text-on-surface">₦{{ props.amount }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-on-surface-variant">Transaction Fee</span>
                                <span v-if="transferFeeError" class="font-bold "> <small>failed to retrieve transfer
                                        fee</small> </span>
                                <span v-if="!transferLoader && !transferFeeError" class="font-bold ">
                                    ₦ {{ transferFee }}
                                </span>
                                <div v-if="transferLoader" class="loader"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-on-surface-variant">Total</span>
                                <span v-if="transferFeeError" class="font-bold "><small>failed to retrieve transfer
                                        fee</small> </span>
                                <span v-if="!transferLoader && !transferFeeError" class="font-bold">₦ {{
                                    completeMoneyToTranfer }}</span>
                                <div v-if="transferLoader" class="loader"></div>
                            </div>
                            <div class="h-px bg-outline-variant/10 w-full"></div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-on-surface-variant">Account Number</span>
                                <div class="text-right">
                                    <span class="font-bold text-on-surface block">{{ props.transferInfo?.accountNumber}}</span>
                                    <!-- <span class="text-[10px] font-bold text-tertiary uppercase tracking-tighter">{{ props.transferInfo?.bankName }}</span> -->
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-on-surface-variant">bank</span>
                                <span class="font-bold">{{ props.transferInfo?.bankName }}</span>
                            </div>
                        </div>

                        <div class="space-y-4 pt-2">
                            <div role="button" @click="emit('confirm')"
                                class="w-full h-16 rounded-full bg-gradient-to-r from-primary to-primary-container text-on-primary-fixed font-headline font-extrabold text-lg shadow-lg shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                                <button class="w-full bg-green-600 hover:bg-green-700 active:bg-green-800
         text-white font-semibold text-base
         py-3 px-4 rounded-xl
         shadow-md hover:shadow-lg
         transition-all duration-200
         active:scale-95
         focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2">
                                    Confirm to Pay
                                </button>
                                <!-- <span class="material-symbols-outlined text-2xl">arrow_forward</span> -->
                            </div>
                            <button @click="emit('close')"
                                class="w-full text-center py-2 text-on-surface-variant font-medium text-sm hover:text-error transition-colors">
                                Cancel Transaction
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>

</template>


<style scoped>
.loader {
    width: 50px;
    padding: 8px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: #25b09b;
    --_m:
        conic-gradient(#0000 10%, #000),
        linear-gradient(#000 0 0) content-box;
    -webkit-mask: var(--_m);
    mask: var(--_m);
    -webkit-mask-composite: source-out;
    mask-composite: subtract;
    animation: l3 1s infinite linear;
}

@keyframes l3 {
    to {
        transform: rotate(1turn)
    }
}
</style>
