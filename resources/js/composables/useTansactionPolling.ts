import axios from 'axios';
import { ref, onUnmounted } from 'vue';
export function useTransactionPolling() {
    const status = ref('pending');
    const isPolling = ref(false);
    const error = ref(null);

    let timeoutId = null;
    let startedAt = null;

    const delays = [2000, 3000, 5000, 8000, 10000];
    const maxPollingTime = 5 * 60 * 1000; // 5 minutes

    const stopPolling = () => {
        isPolling.value = false;

        if (timeoutId) {
            clearTimeout(timeoutId);
            timeoutId = null;
        }
    };

    const poll = async (transactionReference, attempt = 0) => {
        if (!isPolling.value) {
            return;
        }

        // Maximum polling duration
        if (Date.now() - startedAt >= maxPollingTime) {
            stopPolling();

            error.value = 'Transaction is taking longer than expected.';

            return;
        }

        try {
            const response = await axios.get(
                `/transactions/${transactionReference}/status`,
            );

            console.log(response.data.status);

            status.value = response.data.status;

            // Terminal states
            if (
                response.data.status === 'successful' ||
                response.data.status === 'failed'
            ) {
                stopPolling();

                return;
            }

            // Keep polling
            const delay = delays[Math.min(attempt, delays.length - 1)];

            timeoutId = setTimeout(() => {
                poll(transactionReference, attempt + 1);
            }, delay);
        } catch (err) {
            error.value = 'Unable to check transaction status.';

            // You could either stop here or retry.
            const delay = delays[Math.min(attempt, delays.length - 1)];

            timeoutId = setTimeout(() => {
                poll(transactionReference, attempt + 1);
            }, delay);
        }
    };

    const startPolling = (transactionReference) => {
        stopPolling();
        isPolling.value = true;
        startedAt = Date.now();
        status.value = 'pending';
        error.value = null;
        poll(transactionReference);
    };

    onUnmounted(() => {
        stopPolling();
    });

    return {
        status,
        isPolling,
        error,
        startPolling,
        stopPolling,
    };
}
