<script setup lang="ts">
import { ref, onBeforeUnmount } from 'vue'

const message = ref('')
const isVisible = ref(false)

let timeout: ReturnType<typeof setTimeout> | undefined

function showSuccess(text: string = '') {
    message.value = text
    isVisible.value = true

    // Reset the timer if another notification appears
    if (timeout) {
        clearTimeout(timeout)
    }

    timeout = setTimeout(() => {
        isVisible.value = false
    }, 3000)
}

onBeforeUnmount(() => {
    if (timeout) {
        clearTimeout(timeout)
    }
})

// Example: showSuccess('Your profile has been updated.')
defineExpose({ showSuccess })
</script>

<template>
    <Transition name="flash">
        <div v-if="isVisible" class="success-notification" role="status" aria-live="polite">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round" aria-hidden="true">
                    <path d="m5 12 4 4L19 6" />
                </svg>
            </div>

            <span class="message">{{ message }}</span>
        </div>
    </Transition>
</template>

<style scoped>
.success-notification {
    position: fixed;
    top: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;

    display: flex;
    align-items: center;
    gap: 12px;

    width: max-content;
    max-width: calc(100vw - 32px);
    padding: 14px 20px;

    background: #ffffff;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgb(0 0 0 / 10%);

    font-size: 14px;
    font-weight: 500;
}

.success-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;

    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #dcfce7;
    color: #16a34a;
}

.success-icon svg {
    width: 17px;
    height: 17px;
}

.message {
    overflow-wrap: anywhere;
}

.flash-enter-active,
.flash-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}

.flash-enter-from,
.flash-leave-to {
    opacity: 0;
    transform: translate(-50%, -12px);
}

@media (prefers-reduced-motion: reduce) {

    .flash-enter-active,
    .flash-leave-active {
        transition: none;
    }
}
</style>
