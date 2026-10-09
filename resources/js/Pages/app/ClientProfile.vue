<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BottomNav from '../../components/BottomNav.vue';
import Header from '../../components/Header.vue';

const props = defineProps({
    first_name: {
        type: String,
        default: '',
    },
    middle_name: {
        type: String,
        default: '',
    },
    last_name: {
        type: String,
        default: '',
    },
    email: {
        type: String,
        default: '',
    },
    account_status: {
        type: String,
        default: '',
    },
    account_number: {
        type: String,
        default: '',
    },
    account_name: {
        type: String,
        default: '',
    },
    bank_name: {
        type: String,
        default: '',
    },
});


const fullName = computed(() => {
    return [
        props.first_name,
        props.middle_name,
        props.last_name,
    ]
        .filter(Boolean)
        .join(' ');
});

// Toast/Notification State
const toastMessage = ref('')
const showToast = ref(false)
const copyFullName = async () => {
    await navigator.clipboard.writeText(props.account_number)
}
const goToEditPage = () => {
    router.get('/edit-profile')
}
</script>

<template>
    <Header :client_name="props.first_name ? props.first_name : props.account_name" />
    <div class="mvp-layout">
        <!-- Header Section -->
        <header class="page-header">
            <div>
                <div class="breadcrumb">Profile & Security / Personal Details</div>
                <h1>Account Profile</h1>
            </div>
            <button @click="goToEditPage" class="btn-primary">Edit Profile</button>
        </header>

        <!-- Main Profile Card -->
        <section class="card profile-summary">
            <div class="profile-info">
                <div class="name-row">
                    <h2>{{ fullName }}</h2>
                </div>
                <p class="email">{{ props.email }}</p>
                <div class="status">
                    <span class="status-dot"></span>
                    Account Status: <strong>{{ props.account_status }}</strong>
                </div>
            </div>
        </section>

        <!-- Details Grid -->
        <div class="grid-layout">
            <!-- Legal Info -->
            <section class="card">
                <div class="card-header">
                    <h3>Personal Legal Information</h3>
                </div>
                <div class="card-body">
                    <div class="info-row split">
                        <span class="label">Full Legal Name</span>
                        <div class="value-with-action">
                            <strong>{{ fullName }}</strong>
                            <button class="btn-text" @click="copyFullName">Copy</button>
                        </div>
                    </div>

                    <hr class="divider" />

                    <div class="name-grid">
                        <div v-if="props.first_name" class="info-box">
                            <span class="label">First Name</span>
                            <span class="value">{{ props.first_name }}</span>
                        </div>
                        <div v-if="props.middle_name" class="info-box">
                            <span class="label">Middle Name</span>
                            <span class="value">{{ props.middle_name }}</span>
                        </div>
                        <div v-if="props.last_name" class="info-box">
                            <span class="label">Last Name</span>
                            <span class="value">{{ props.last_name }}</span>
                        </div>
                    </div>

                    <hr class="divider" />

                    <div class="info-row split">
                        <span class="label">Email Address</span>
                        <div class="value-with-badge">
                            <span>{{ props.email }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Security -->
            <section class="card">
                <div class="card-body security-list">
                    <div class="security-item split">
                        <div>
                            <span class="label">Account Number</span>
                            <div class="password-mask">{{ props.account_number }}</div>
                        </div>
                        <div>
                            <span class="label">Bank Name</span>
                            <div class="password-mask">{{ props.bank_name }}</div>
                        </div>
                        <div>
                            <span class="label">Account Name</span>
                            <div class="password-mask">{{ props.account_name }}</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <!-- Toast Notification -->
        <Transition name="fade-up">
            <div v-if="showToast" class="toast">
                {{ toastMessage }}
            </div>
        </Transition>
    </div>
    <BottomNav />
</template>

<style scoped>
/* MVP Base Variables & Layout */
.mvp-layout {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    font-family: 'Hanken Grotesk', system-ui, sans-serif;
    color: #191c1e;
    background-color: #f7f9fb;
    height: fit-content;
    box-sizing: border-box;
}

/* Header */
.page-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .page-header {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
}

.breadcrumb {
    font-size: 0.75rem;
    color: #3e4943;
    margin-bottom: 0.25rem;
}

h1 {
    font-size: 2.25rem;
    margin: 0;
    font-weight: 600;
    letter-spacing: -0.025em;
}

.subtitle {
    color: #3e4943;
    font-size: 0.875rem;
    margin: 0.25rem 0 0 0;
}

/* Cards */
.card {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.card-header {
    padding: 1.25rem 1.5rem;
    background-color: #f2f4f6;
    border-bottom: 1px solid #e0e3e5;
}

.card-header h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* Profile Summary Specific */
.profile-summary {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem;
    gap: 1.5rem;
    text-align: center;
}

@media (min-width: 640px) {
    .profile-summary {
        flex-direction: row;
        text-align: left;
    }
}

.avatar {
    width: 6rem;
    height: 6rem;
    border-radius: 50%;
    object-fit: cover;
    background-color: #e6e8ea;
}

.name-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

@media (min-width: 640px) {
    .name-row {
        justify-content: flex-start;
    }
}

.name-row h2 {
    margin: 0;
    font-size: 1.5rem;
}

.email {
    font-family: 'JetBrains Mono', monospace;
    color: #3e4943;
    margin: 0.5rem 0;
    font-size: 0.875rem;
}

.status {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #3e4943;
}

.status-dot {
    width: 8px;
    height: 8px;
    background-color: #005d42;
    border-radius: 50%;
}

/* Layout Grids */
.grid-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 1024px) {
    .grid-layout {
        grid-template-columns: 7fr 5fr;
    }
}

.name-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    height: auto;
}

@media (min-width: 640px) {
    .name-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* Typography & Info Rows */
.label {
    display: block;
    font-size: 0.6875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #3e4943;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.value {
    font-size: 1rem;
    font-weight: 500;
}

.text-muted {
    color: #6e7a73;
    font-size: 0.75rem;
}

.info-box {
    background-color: #f2f4f6;
    padding: 0.875rem;
    border-radius: 0.5rem;
}

.split {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;

}

@media (min-width: 640px) {
    .split {
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
    }
}

.divider {
    border: 0;
    height: 1px;
    background-color: #eceef0;
    margin: 0;
}

.value-with-action,
.value-with-badge {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.password-mask {
    font-family: monospace;
    font-weight: bold;
    letter-spacing: 2px;
    margin: 0.25rem 0;
}

/* Security List */
.security-list {
    gap: 1rem;
}

.security-item {
    background-color: #f2f4f6;
    padding: 1rem;
    border-radius: 0.5rem;
}

/* Badges */
.badge {
    font-size: 0.6875rem;
    padding: 0.125rem 0.5rem;
    border-radius: 1rem;
    font-weight: 500;
}

.badge-outline {
    background-color: rgba(176, 241, 199, 0.5);
    color: #0f5132;
}

.badge-success {
    background-color: rgba(176, 241, 199, 0.5);
    color: #002111;
}

.badge-active {
    background-color: rgba(176, 241, 199, 0.5);
    color: #0f5132;
}

/* Buttons */
button {
    cursor: pointer;
    font-family: inherit;
    border: none;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #047857;
    color: white;
    padding: 0.625rem 1rem;
    font-size: 0.8125rem;
}

.btn-primary:hover {
    background-color: #005d42;
}

.btn-secondary {
    background-color: #ffffff;
    color: #191c1e;
    padding: 0.5rem 0.75rem;
    font-size: 0.8125rem;
    border: 1px solid #e0e3e5;
}

.btn-secondary:hover {
    background-color: #eceef0;
}

.btn-text {
    background: transparent;
    color: #005d42;
    font-size: 0.8125rem;
    padding: 0.25rem 0.5rem;
}

.btn-text:hover {
    text-decoration: underline;
}

/* Toast */
.toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    background-color: #2d3133;
    color: #eff1f3;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 50;
}

/* Transitions */
.fade-up-enter-active,
.fade-up-leave-active {
    transition: all 0.3s ease;
}

.fade-up-enter-from,
.fade-up-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
</style>
