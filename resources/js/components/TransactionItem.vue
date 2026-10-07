// TransactionItem.vue
<template>
  <div  @click="navigateToTransactionDetail" class="transaction-item active-tap">
    <!-- <div class="tx-icon" :class="isCredit ? 'tx-icon-credit' : 'tx-icon-debit'">
      <span class="material-symbols-outlined">{{ iconName }}</span>
    </div> -->

    <div class="tx-details">
      <p class="tx-title">{{ formattedTransactionType }}</p>
      <p class="tx-date">{{ formattedInitiatedAt }}</p>
    </div>

    <div class="tx-amount-wrap">
      <p class="tx-amount" :class="isCredit ? 'text-credit' : 'text-debit'">
        {{ isCredit ? '+' : '' }}{{ formattedAmount }}
      </p>
      <p class="tx-status">{{ transaction.status || 'Successful' }}</p>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { router } from "@inertiajs/vue3";
import { computed } from 'vue';

const props = defineProps({
  transaction: {
    type: Object,
    required: true
  }
});

const date = new Date(props.transaction.initiated_at);

const formattedInitiatedAt = date.toLocaleDateString('en-NG', {
     day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: false
});

const formattedTransactionType = props.transaction.type[0].toUpperCase() + props.transaction.type.slice(1);


const isCredit = computed(() => props.transaction.direction === 'credit');

const navigateToTransactionDetail = () =>{
   router.get('/transaction_detail' , {
      reference: props.transaction.transaction_reference
   })
}

// const iconName = computed(() => {
//   if (props.transaction.icon){
//      return props.transaction.icon;
//   }

//   return isCredit.value ? 'south_west' : 'arrow_outward';
// });

const formattedAmount = computed(() => {
  const symbol = props.transaction.amount < 0 ? '-' : '';
  const num = Math.abs(props.transaction.amount).toLocaleString('en-US', { minimumFractionDigits: 2 });

  return `${symbol}₦${num}`;
});
</script>

<style scoped>
.transaction-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  cursor: pointer;
}

.tx-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tx-icon-debit {
  background-color: var(--surface-container);
  color: var(--on-surface-variant);
}

.tx-icon-credit {
  background-color: rgba(0, 108, 79, 0.1);
  color: var(--primary);
}

.tx-details {
  flex: 1;
}

.tx-title {
  font-size: 12px;
  font-weight: 700;
  color: var(--on-surface);
  margin: 0 0 2px 0;
}

.tx-date {
  font-size: 10px;
  color: var(--on-surface-variant);
  margin: 0;
}

.tx-amount-wrap {
  text-align: right;
}

.tx-amount {
  font-size: 12px;
  font-weight: 700;
  margin: 0 0 2px 0;
}

.text-debit {
  color: var(--error);
}

.text-credit {
  color: var(--primary);
}

.tx-status {
  font-size: 9px;
  color: var(--on-surface-variant);
  margin: 0;
}

.active-tap:active {
  background-color: rgba(0,0,0,0.02);
}
</style>
