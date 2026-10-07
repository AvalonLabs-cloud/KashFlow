import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

export function usePhoneVerification() {
    const loading = ref(false);
    const error = ref(null);
    const success = ref(false);
    const responseData = ref(null);

    const verifyPhone = async (phoneNumber) => {
        loading.value = true;
        error.value = null;
        success.value = false;
        responseData.value = null;
        const form = useForm({
            phone_number: '0' + phoneNumber,
        });

        try {
            form.post('/onboarding/send_phone_verification_sms');
            success.value = true;
            //   return response.data
        } catch (err) {
            if (err.response) {
                // Backend returned an error responses
                error.value =
                    err.response.data?.message ||
                    err.response.data?.error ||
                    'Verification failed';
            } else if (err.request) {
                // No response received
                error.value = 'Unable to connect to the server';
            } else {
                // Axios/config error
                error.value = err.message || 'An unexpected error occurred';
            }

            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        loading,
        error,
        success,
        responseData,
        verifyPhone,
    };
}
