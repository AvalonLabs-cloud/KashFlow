<script lang="ts" setup>
import { computed } from 'vue';

interface TransferFailureData {
  amount?: number | string;
  recipientName?: string;
  bankName?: string;
  accountNumber?: string;
  reference?: string;
  narration?: string;
  errorMessage?: string;
}

const props = defineProps<{
  data: TransferFailureData;
}>();

const maskAccount = (acc?: string) => {
  if (!acc) {
    return '';
  }

  return `•••• ${acc.slice(-4)}`;
};

const amount = computed(() => props.data?.amount ?? '');
const recipientName = computed(() => props.data?.recipientName ?? '');
const bankName = computed(() => props.data?.bankName ?? '');
const accountNumber = computed(() => props.data?.accountNumber ?? '');
const reference = computed(() => props.data?.reference ?? '');
const narration = computed(() => props.data?.narration ?? '');
const errorMessage = computed(() => props.data?.errorMessage ?? '');
</script>

<template>
  <div class="failure-page-wrapper">
    <div class="container">
      <section class="status-section">
        <div class="icon-container">
          <div class="icon-circle">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="3"
            >
              <path
                d="M18 6L6 18M6 6l12 12"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </div>
        </div>

        <h1 class="title">Transfer Failed</h1>

        <p class="error-message">
          {{ errorMessage || 'Transaction could not be completed.' }}
        </p>
      </section>

      <div class="amount-display">
        <span class="currency">₦</span>{{ amount }}
      </div>

      <div class="details-card">
        <div class="detail-row">
          <span class="label">Recipient</span>
          <span class="value truncate">{{ recipientName }}</span>
        </div>

        <div class="divider"></div>

        <div class="detail-row">
          <span class="label">Bank</span>
          <span class="value">{{ bankName }}</span>
        </div>

        <div class="divider"></div>

        <div class="detail-row">
          <span class="label">Account</span>
          <span class="value">{{ maskAccount(accountNumber) }}</span>
        </div>

        <div class="divider"></div>

        <div class="detail-row">
          <span class="label">Reference</span>
          <span class="value text-secondary">{{ reference }}</span>
        </div>

        <template v-if="narration">
          <div class="divider"></div>

          <div class="detail-row">
            <span class="label">Narration</span>
            <span class="value">{{ narration }}</span>
          </div>
        </template>
      </div>

      <div class="spacer"></div>

      <footer class="action-section">
        <!--
        <button @click="onGoHome" class="btn-ghost">
          Back to Home
        </button>
        -->
      </footer>
    </div>
  </div>
</template>
