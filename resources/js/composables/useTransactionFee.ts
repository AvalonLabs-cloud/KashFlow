import axios from 'axios';
import { ref } from 'vue';

export function useTransactionFee() {
    const fee = ref(0);
    const feeIsLoading = ref(false);
    const feeError = ref(null);

    const fetchFee = async (amount) => {
        feeIsLoading.value = true;
        feeError.value = null;

        try {
            const response = await axios.get('/transaction-fee', {
                params: {
                    amount: amount,
                },
            });
            console.log(response);
            
            fee.value = response.data.fee;
        } catch (err) {
            fee.value = 0;
            feeError.value = err;
        } finally {
            feeIsLoading.value = false;
        }
    };

    return {
        fee,
        feeIsLoading,
        feeError,
        fetchFee,
    };
}
