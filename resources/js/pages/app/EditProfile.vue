<script lang="ts" setup>
import { useForm } from "@inertiajs/vue3";


import { ref} from 'vue'
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
});

const form = useForm({
    first_name: props.first_name,
    middle_name: props.middle_name,
    last_name: props.last_name,
    password: null,
    password_current: null,
    password_confirmation: null,
});



// UI State
const isSaving = ref(false)
const showToast = ref(false)

// Password Visibility State
const showCurrentPassword = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const formSubmit = () => {
    isSaving.value = false
    showToast.value = true
    form.patch('/edit-profile')
    setTimeout(() => {
        showToast.value = false
    }, 3000)
}



</script>

<template>
     <Header :client_name="props.first_name ? props.first_name : 'Avater'  " />
    <div class="mvp-container">
        <!-- Success Notification -->
        <div v-if="showToast" class="toast">
            Changes saved successfully.
        </div>


        <form @submit.prevent="formSubmit" class="form-card">
            <section class="form-section">
                <h2>Personal Information</h2>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input id="firstName" v-model="form.first_name" type="text"
                      />
                    </div>
                    <div class="form-group">
                        <label for="lastName">Middle Name</label>
                        <input id="lastName" v-model="form.middle_name" type="text"
                   />
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input id="lastName" v-model="form.last_name" type="text"
                     />
                    </div>
                </div>
            </section>

            <hr class="divider" />

            <section class="form-section">
                <h2>Security & Password</h2>

                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <div class="input-wrapper">
                        <input id="currentPassword" v-model="form.password_current"
                            :type="showCurrentPassword ? 'text' : 'password'" placeholder="Enter current password" />
                        <button type="button" class="toggle-btn" @click="showCurrentPassword = !showCurrentPassword">
                            {{ showCurrentPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="input-wrapper">
                        <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter new password" />
                        <button type="button" class="toggle-btn" @click="showPassword = !showPassword">
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="passwordConfirmation">Confirm New Password</label>
                    <div class="input-wrapper">
                        <input id="passwordConfirmation" v-model="form.password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'" placeholder="Confirm new password" />
                        <button type="button" class="toggle-btn" @click="showConfirmPassword = !showConfirmPassword">
                            {{ showConfirmPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                </div>
            </section>

            <div class="actions">
                <button type="submit" class="btn-submit" :disabled="isSaving">
                    {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
    </div>
    <BottomNav/>
</template>

<style scoped>
/* MVP Base Styling */
.mvp-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem 1rem;
    font-family: 'Hanken Grotesk', system-ui, sans-serif;
    color: #191c1e;
    background-color: #f7f9fb;
    height: auto;
    /* min-height: 100vh; */
}

.header {
    margin-bottom: 2rem;
}

.header h1 {
    font-size: 2rem;
    margin: 0 0 0.5rem 0;
}

.header p {
    color: #3e4943;
    margin: 0;
}

/* Toast */
.toast {
    background-color: #047857;
    color: #ffffff;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
    font-weight: 500;
}

/* Form Layout */
.form-card {
    background-color: #ffffff;
    padding: 2rem;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-section h2 {
    font-size: 1.25rem;
    margin: 0 0 0.5rem 0;
    color: #191c1e;
}

.divider {
    border: 0;
    height: 1px;
    background-color: #e0e3e5;
    margin: 2rem 0;
}

/* Grid for side-by-side inputs */
.grid-2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 640px) {
    .grid-2 {
        grid-template-columns: 1fr 1fr;
    }
}

/* Inputs */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #191c1e;
}

input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #bdc9c1;
    border-radius: 0.5rem;
    background-color: #f7f9fb;
    font-family: inherit;
    font-size: 1rem;
    box-sizing: border-box;
}

input:focus {
    outline: none;
    border-color: #005d42;
    background-color: #ffffff;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.toggle-btn {
    position: absolute;
    right: 0.75rem;
    background: none;
    border: none;
    color: #3e4943;
    font-size: 0.875rem;
    cursor: pointer;
    padding: 0.25rem;
}

.toggle-btn:hover {
    color: #191c1e;
}

/* Buttons */
.actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e0e3e5;
}

button {
    cursor: pointer;
    font-family: inherit;
    font-size: 1rem;
    font-weight: 500;
}

.btn-cancel {
    padding: 0.75rem 1.5rem;
    background-color: transparent;
    border: 1px solid transparent;
    color: #3e4943;
    border-radius: 0.5rem;
}

.btn-cancel:hover {
    background-color: #f2f4f6;
}

.btn-submit {
    padding: 0.75rem 1.5rem;
    background-color: #005d42;
    border: none;
    color: #ffffff;
    border-radius: 0.5rem;
    transition: background-color 0.2s;
}

.btn-submit:hover:not(:disabled) {
    background-color: #047857;
}

.btn-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

@media (max-width: 640px) {
    .actions {
        flex-direction: column;
    }

    .btn-submit,
    .btn-cancel {
        width: 100%;
    }
}
</style>
