import { ref } from 'vue'

export function useError() {
    const error = ref(false)
    const messageContainer = ref('')

    const showError = (message = '') => {
        error.value = true

        if (message) {
            messageContainer.value = message
        }

        setTimeout(() => {
            error.value = false
        }, 3000)
    }

    return {
        error,
        showError,
    }
}