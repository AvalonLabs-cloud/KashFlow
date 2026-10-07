// TransactionList.vue
<template>
  <section class="transaction-section">
    <div class="section-header">
      <h2 class="section-title">Recent Transactions</h2>
      <button @click="historyNavigate" class="history-btn">History</button>
    </div>

    <div class="transaction-list">
  <InfiniteScroll data="recentTransactions">

      <TransactionItem
        v-for="tx in recentTransactions.data"
        :key="tx.id"
        :transaction="tx"
      />
        </InfiniteScroll>
  <h3 v-if="!recentTransactions.data.length" class="empty-transactions">
    No transactions yet
</h3>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { InfiniteScroll } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import TransactionItem from '../components/TransactionItem.vue';

const historyNavigate = ()=> {
   router.get('/history');
}

defineProps([
    'recentTransactions',
]);
</script>

<style scoped>
.empty-transactions {
    display: flex;
    align-items: center;
    justify-content: center;

    min-height: 180px;
    margin: 0;
    padding: 2rem;

    color: #8a8f98;
    font-size: 0.95rem;
    font-weight: 500;
    letter-spacing: -0.01em;
    text-align: center;

    background: #fafafa;
    border: 1px dashed #e2e5e9;
    border-radius: 14px;
}
.transaction-section {
  margin-top: 8px;
}

.section-header {
  display: flex;
  justify-content: space-around;
  align-items: center;
  margin-bottom: 12px;
}

.section-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--on-surface);
  letter-spacing: -0.025em;
  margin: 0;
}

.history-btn {
  font-size: 12px;
  font-weight: 700;
  color: var(--primary);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.transaction-list {
  background-color: var(--surface);
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
  max-height: 400px;
  overflow-y: auto;
}

/* Simulate divide-y */
.transaction-list > *:not(:last-child) {
  border-bottom: 1px solid var(--surface-container);
}
</style>
