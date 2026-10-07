<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
    error?: string | null
    message?: string | null
}>()

const visibleError = ref<string | null>(null)

watch(
    () => props.error,
    (error) => {
        visibleError.value = error ?? ''

        if (error) {
            setTimeout(() => {
                visibleError.value = ''
            }, 2000)
        }
    },
    { immediate: true }
)
</script>

<template>
    <Transition name="toast">
        <div v-if="visibleError" class="toast-popup" role="alert">
            <div class="toast-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
            </div>

            <div class="toast-content">
                <p class="toast-title">Something went wrong</p>
                <p v-if="visibleError" class="toast-message">
                    {{ visibleError }}
                </p>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.toast-popup {
    position: fixed;
    top: 28px;
    left: 50%;
    transform: translateX(-50%);

    display: flex;
    align-items: flex-start;
    gap: 12px;

    width: calc(100% - 32px);
    max-width: 400px;
    padding: 14px 16px;

    /* Frosted minimalist porcelain style */
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);

    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 14px;

    /* Multi-layered soft ambient shadow */
    box-shadow:
        0 4px 6px -1px rgba(0, 0, 0, 0.02),
        0 12px 32px -4px rgba(0, 0, 0, 0.06);

    z-index: 9999;
    pointer-events: auto;
    user-select: none;
}

.toast-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 28px;
    height: 28px;
    flex-shrink: 0;

    background: rgba(225, 29, 72, 0.08);
    color: #e11d48;

    border-radius: 50%;
    margin-top: 1px;
}

.toast-content {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.toast-title {
    margin: 0;
    color: #18181b;
    font-size: 13.5px;
    font-weight: 550;
    letter-spacing: -0.01em;
}

.toast-message {
    margin: 0;
    color: #71717a;
    font-size: 12.5px;
    line-height: 1.45;
    letter-spacing: -0.005em;
}

/* Fluid Apple-style spring transition */
.toast-enter-active {
    transition:
        opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1),
        transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}

.toast-leave-active {
    transition:
        opacity 0.2s cubic-bezier(0.4, 0, 1, 1),
        transform 0.2s cubic-bezier(0.4, 0, 1, 1);
}

.toast-enter-from {
    opacity: 0;
    transform: translate(-50%, -12px) scale(0.96);
}

.toast-leave-to {
    opacity: 0;
    transform: translate(-50%, -8px) scale(0.98);
}
</style>

