
import { useForm } from '@inertiajs/vue3'
import { ref } from "vue";



interface CreateUserPayload {
    email: string;
    password: string;
    password_confirmation: string;
}

interface User {
    id: string;
    name: string;
    email: string;
}

export function useCreateUser() {
    const loading = ref(false);
    const error = ref<string | null>(null);
    const user = ref<User | null>(null);


    const createUser = async (payload: CreateUserPayload) => {
        loading.value = true;
        error.value = null;
       const form = useForm({
       ...payload
    });

        try {
            await form.post(
                "/onboarding/phase_one",
            );
            error.value = null;
            // user.value = data;
        } catch (err: any) {
            console.log('Error in createUser:', err);
            error.value =
                err?.response?.data?.message ||
                err.message ||
                "Failed to create user";

            return null;
        } finally {
            loading.value = false;
            console.log(error.value)
        }
    };

    return {
        createUser,
        user,
        loading,
        error,
    };
}


