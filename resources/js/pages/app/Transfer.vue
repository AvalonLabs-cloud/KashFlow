<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import axios from 'axios'

import { createIcons, ChevronLeft } from 'lucide';

import {
    ref,
    reactive,
    computed,
    watch,
    onMounted,
    toRefs
} from 'vue'

createIcons({
  icons: {
    ChevronLeft
  }
});

import { useTransferStore } from '@/stores/transfer'

const transferStore = useTransferStore()
const accountName = ref('')
const isValidating = ref(false)
const banks = ref()


const form = reactive({
    accountNumber: '',
    selectedBank: '',
    amount: 1000
})

const {accountNumber , selectedBank } = toRefs(form)

watch([accountNumber , selectedBank], ([currentValueAccountNumber , currentValueBankName]) => {
    if (currentValueAccountNumber.length === 10 && currentValueBankName) {
        fetchAccountName()
    }

})

async function getBanks() {
    const bankList = await axios.get('/flutterwave/banks');
    console.log(bankList)

    if (bankList) {
        banks.value = bankList.data.data
    }
}

onMounted(() => {
    getBanks();
});

const transactions = ref([{
    id: 1,
    name: "John Doe",
    amount: -15000,
    bank: "GTBank",
    status: "Success"
},
{
    id: 2,
    name: "Sarah",
    amount: -5000,
    bank: "UBA",
    status: "Pending"
}
])

// Fake API
const fetchAccountName = async () => {
    isValidating.value = true

    try {
        const response = await axios.post('/flutterwave/accounts/resolve', {
            accountNumber: form.accountNumber,
            bankCode: form.selectedBank
        })

        console.log(response)

        accountName.value = response.data.data.account_name

        console.log(accountName.value)

    } catch (error) {
        console.error(error)
    } finally {
        isValidating.value = false
    }
}

const handleAccountInput = () => {
    if (form.accountNumber.length === 10) {
        fetchAccountName()
    } else {
        accountName.value = ''
    }
}

const isFormValid = computed(() => {
    return (
        form.accountNumber.length === 10 &&
        form.selectedBank &&
        accountName.value
    )
})

const handleTransfer = () => {
    if (!isFormValid.value) {
        return
    }

    const id = transferStore.addTransfer({
        accountNumber: form.accountNumber,
        bankName: 'Access',
        bankCode: form.selectedBank,
        accountName: accountName.value,
    })

    const transferId = id

    router.get(`/complete_transfer?transferId=${transferId}`)

    // transactions.value.unshift({
    //     id: Date.now(),
    //     bank: form.selectedBank,
    //     name: accountName.value,
    //     amount: form.amount,
    //     status: 'Success'
    // })

    // if (form.amount !== null) {
    //     balance.value -= form.amount
    //     form.accountNumber = ''
    //     form.selectedBank = ''
    //     form.amount = 0
    //     accountName.value = ''
    // }



}
</script>


<template>
    <div class="text-on-surface min-h-screen flex flex-col">
<BackAndHistoryHeader/>
        <!-- Main -->
        <main class="flex-grow pt-20 pb-28 px-4 space-y-6">

            <!-- Form -->
            <section class="bg-white rounded-xl p-6 shadow-sm border border-outline-variant/10 space-y-5">

                <!-- Account -->
                <div>
                    <label class="text-xs font-semibold">Account Number</label>
                    <input v-model="form.accountNumber" @input="handleAccountInput" maxlength="10"
                        class="w-full h-12 px-4 rounded-lg bg-gray-100" placeholder="Enter 10 digits" />
                </div>

                <!-- Bank -->
                <div>
                    <label class="text-xs font-semibold">Bank</label>
                    <select v-model="form.selectedBank" class="w-full h-12 px-4 rounded-lg bg-gray-100">
                        <option disabled value="">Select Bank</option>
                        <option v-for="bank in banks" :key="bank.code" :value="bank.code">
                            {{ bank.name }}
                        </option>
                    </select>
                </div>

                <!-- Account Name -->
                <div v-if="accountName" class="p-3 bg-green-50 rounded-lg">
                    {{ accountName }}
                </div>

                <!-- Amount -->
                <!-- <div>
                    <label class="text-xs font-semibold">Amount</label>
                    <input v-model.number="form.amount" type="number"
                        class="w-full h-14 px-4 rounded-lg bg-gray-100" />
                </div> -->

                <!-- Button -->
                <button :disabled="!isFormValid" @click="handleTransfer"
                    class="w-full py-3 rounded-lg bg-green-600 text-white disabled:opacity-50">
                    Next
                </button>

            </section>

            <!-- Transactions -->
            <section>
                <h2 class="font-bold mb-3">Transactions</h2>

                <div v-if="transactions.length === 0" class="text-gray-400">
                    No transactions yet
                </div>

                <div class="space-y-3">
                    <div v-for="tx in transactions" :key="tx.id"
                        class="bg-white p-4 rounded-lg shadow-sm flex justify-between">
                        <div>
                            <p class="font-semibold">{{ tx.bank }}</p>
                            <p class="text-sm text-gray-500">{{ tx.name }}</p>
                        </div>

                        <div class="text-right">
                            <p class="font-bold">
                                -₦{{ tx.amount.toLocaleString() }}
                            </p>
                            <span class="text-xs text-green-600">
                                {{ tx.status }}
                            </span>
                        </div>
                    </div>
                </div>

            </section>

        </main>

    </div>
</template>


<style scoped>
/* keep empty or add styles if needed */
</style>
