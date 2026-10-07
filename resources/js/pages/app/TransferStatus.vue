<script lang="ts" setup>
import { usePage, router } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import TransferFailure from '@/components/TransferFailure.vue';
import TransferProcessing from "@/components/TransferProcessing.vue";
import TransferSuccess from '@/components/TransferSuccess.vue';
import ErrorNotification from '@/components/ui/ErrorNotification.vue';
import { useTransactionPolling } from '@/composables/useTansactionPolling';
import BottomNav from '../../components/BottomNav.vue';
import Header from '../../components/Header.vue';

const {
    status,
    error,
    startPolling,
} = useTransactionPolling()

const props = defineProps([
    'transactionReference',
    'transaction',
    'clients_name',
])


const transactionReference = computed(() => {
    return props.transactionReference
})

console.log('props for transfer status page ', props)
console.log('status for transfer status page ', status.value)


onMounted(() => {
    startPolling(transactionReference.value)
});


const goHome = () => router.visit('/dashboard');
</script>

<template>
   <Header :client_name="props.clients_name" />
    <ErrorNotification v-if="error" :error="error" :message="'something went wrong'" />
    <div>
        <TransferProcessing v-if="status === 'pending'" :data=transaction />

        <TransferSuccess v-if="status == 'successful'" @done="goHome" :data=transaction />
        <!--
        <TransferFailure v-if="status === 'failed'" :data=transaction  @go-home="goHome" /> -->
    </div>
    <BottomNav/>
</template>



<style></style>
