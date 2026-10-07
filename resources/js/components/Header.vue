<template>
    <header class="app-header">
        <Link href="/client_profile" class="user-profile-link">
            <div class="user-profile">
                <div class="avatar-container">
                    <Avater :letter="props?.client_name" />
                </div>
                <div class="greeting-info">
                    <span class="greeting-text">Welcome back,</span>
                    <h1 class="user-name">{{ props?.client_name || 'Guest' }}</h1>
                </div>
            </div>
        </Link>

        <div class="header-actions">
            <button @click="helpDeskNavigation" class="icon-button active-tap" aria-label="Help">
                <span class="notification-badge"></span>
                <span v-html="helpSvg" class="material-symbols-outlined icon"></span>
            </button>
        </div>
    </header>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import Avater from '@/components/Avater.vue';
import { helpSvg } from '@/svgs/svg';

const helpDeskNavigation = () => {
    router.get('/helpDesk')
}

const props = defineProps<{
    client_name?: string
}>();
</script>

<style scoped>
.app-header {
    background-color: rgba(255, 255, 255, 0.85);
    /* Adjust opacity for theme or light mode */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    padding: 0 20px;
    height: 68px;
    top: 0;
    left: 0;
    /* z-index: 50; */
    box-sizing: border-box;
}

.user-profile-link {
    text-decoration: none;
    color: inherit;
    border-radius: 28px;
    transition: opacity 0.2s ease;
}

.user-profile-link:hover {
    opacity: 0.85;
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar-container {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--primary, #000);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, border-color 0.2s ease;
}

.avatar-container:hover {
    transform: scale(1.03);
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.greeting-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.2;
}

.greeting-text {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--on-surface-variant, #666);
    font-weight: 600;
    margin-bottom: 2px;
}

.user-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--primary, #111);
    margin: 0;
    font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
    letter-spacing: -0.01em;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 50px;
    padding: 0 40px 0 0;
}

.icon-button {
    position: relative;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--surface-variant, rgba(0, 0, 0, 0.03));
    border: 1px solid rgba(0, 0, 0, 0.05);
    cursor: pointer;
    transition: all 0.2s ease;
}

.icon-button:hover {
    background-color: rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.icon {
    color: var(--primary, #222);
    font-size: 20px;
    font-weight: bold;
}

/* Red Notification Indicator with Subtle Pulse */
.notification-badge {
    position: absolute;
    top: 8px;
    right: 0px;
    width: 9px;
    height: 9px;
    background-color: #ff3b30;
    /* Premium Vibrant Red */
    border: 2px solid #ffffff;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(255, 59, 48, 0.4);
    animation: pulse-red 2s infinite;
}

@keyframes pulse-red {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 59, 48, 0.5);
    }

    70% {
        box-shadow: 0 0 0 6px rgba(255, 59, 48, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(255, 59, 48, 0);
    }
}

.active-tap:active {
    transform: scale(0.92);
}

.notification-btn {
    display: flex;
    flex-direction: row;
}
</style>
