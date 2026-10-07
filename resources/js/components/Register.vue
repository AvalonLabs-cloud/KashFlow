<script lang="ts" setup>
    import { faker } from '@faker-js/faker';
    import axios from "axios";
    import { onMounted } from 'vue';

    import {
        useRegistrationStore
    } from '@/stores/registerandcreateaccount';
    const registrationStore = useRegistrationStore();

    function storeRegistrationInfo() {
        registrationStore.firstStepCompleted = true;
        registrationStore.showSecondStep = true;
    }

async function createAccount() {
    registrationStore.showSecondStep = false;
    registrationStore.loading = true;

    try {
     const response =  await axios.post('/createaccount', {
            email: registrationStore.email,
            // tx_ref: registrationStore.tx_ref,
            phonenumber: registrationStore.phone,
            firstname: registrationStore.firstName,
            lastname: registrationStore.lastName,
            narration: 'create my virtual account',
            bvn: registrationStore.bvn,
            password: registrationStore.password,
            is_permanent: true
        });e

        if (response.data.status === 200) {
            window.location.href = response.data.redirect;
        } else {
           window.location.href = response.data.failed_redirect;
        }

    } catch (error: any) {
        if (error.response) {
            throw error.response.data;
        }

        console.log(error);
    } finally {
          registrationStore.loading = false; // always resets loading, success or error
    }
}
onMounted(() => {
    registrationStore.firstName = faker.person.firstName();
    registrationStore.lastName = faker.person.lastName();

    registrationStore.email = faker.internet.email();

    // Strong password
    registrationStore.password = faker.internet.password({
        length: 12,
        memorable: false
    });

    // Nigerian-style phone number
    registrationStore.phone = '0' + faker.number.int({ min: 7000000000, max: 9099999999 });

    // BVN = 11 digits (Nigeria standard)
    registrationStore.bvn = faker.string.numeric(11);
});
</script>

<template>

      <span :class="{ custom: registrationStore.loading }" class="loader" ></span>

    <div v-if="!registrationStore.firstStepCompleted" class="register-container">
        <div class="register-card">
            <div class="form-section">
                <div class="form-header">
                    <h1>Create your account</h1>
                    <p>Get started with your free PAYNOWLTD account</p>
                </div>
                <form class="flex flex-col gap-3" id="registerForm" @submit.prevent="storeRegistrationInfo">
                    <div class="form-group"><label class="form-label" for="fullName">First Name</label>
                        <div class="input-wrapper"><i class="fas fa-user input-icon"></i> <input
                                v-model="registrationStore.firstName" id="firstName" class="form-input"
                                placeholder="John Doe" required=""></div>
                        <div class="form-error" id="fullNameError">Please enter your first name</div>
                    </div>

                    <div class="form-group"><label class="form-label" for="fullName">Last Name</label>
                        <div class="input-wrapper"><i class="fas fa-user input-icon"></i> <input
                                v-model="registrationStore.lastName" id="lastName" class="form-input"
                                placeholder="John Doe" required=""></div>
                        <div class="form-error" id="fullNameError">Please enter your last name</div>
                    </div>

                    <div class="form-group"><label class="form-label" for="email">Email Address</label>
                        <div class="input-wrapper"><i class="fas fa-envelope input-icon"></i> <input
                                v-model="registrationStore.email" type="email" id="email" class="form-input"
                                placeholder="john@company.com" required=""></div>
                        <div class="form-error" id="emailError">Please enter a valid email address</div>
                    </div>
                    <!-- <div class="form-group"><label class="form-label" for="company">Company Name (Optional)</label>
                        <div class="input-wrapper"><i class="fas fa-building input-icon"></i> <input id="company"
                                class="form-input" placeholder="Acme Inc."></div>
                    </div> -->
                    <div class="form-group"><label class="form-label" for="password">Password</label>
                        <div class="input-wrapper"><i class="fas fa-lock input-icon"></i> <input
                                v-model="registrationStore.password" type="password" id="password"
                                class="form-input input-support focus:border focus:border-primary! rounded-xl!"
                                placeholder="••••••••" required="" oninput="checkPasswordStrength()"> <i
                                class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i></div>
                        <!-- <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div> -->
                        <div class="password-strength-text" id="strengthText"></div>
                        <div class="form-error" id="passwordError">Password must be at least 8 characters</div>
                    </div>
                    <div class="form-group"><label class="form-label" for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper"><i class="fas fa-lock input-icon"></i> <input
                            v-model="registrationStore.password"
                                name="password_confirmation" type="password" id="confirmPassword"
                                class="form-input input-support focus:border focus:border-primary! rounded-xl!"
                                placeholder="••••••••" required=""> <i class="fas fa-eye password-toggle"
                                onclick="togglePassword('confirmPassword')"></i></div>
                        <div class="form-error" id="confirmPasswordError">Passwords do not match</div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox-wrapper"><input type="checkbox" id="terms" class="custom-checkbox"
                                required=""> <label for="terms" style="font-size: 0.875rem; cursor: pointer">I
                                agree to
                                the <a href="#" style="color: #667eea; text-decoration: none">Terms of
                                    Service</a> and
                                <a href="#" style="color: #667eea; text-decoration: none">Privacy
                                    Policy</a></label>
                        </div>
                        <div class="form-error" id="termsError">You must accept the terms and conditions</div>
                    </div><button type="submit" class="btn btn-primary" id="submitBtn">Create Account</button>
                    <div class="dividerRegistrationPage"><span>or continue with</span></div>
                    <!-- <div class="social-buttons"><button type="button" class="btn-secondary"
                            onclick="socialLogin('google')"><svg width="20" height="20" viewbox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4"></path>
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853"></path>
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                    fill="#FBBC05"></path>
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335"></path>
                            </svg> Google</button></div> -->
                    <div class="footer-link">Already have an account? <a href="loginpage.html">Register</a></div>
                </form>
            </div>
        </div>
    </div>
    <div v-if="registrationStore.showSecondStep" class="register-container">
        <div class="register-card">
            <div class="form-section">
                <div class="form-header">
                    <h1>Create your PAYNOWLTD account</h1>
                </div>
                <form class="flex flex-col gap-3" id="registerForm" @submit.prevent="createAccount">
                    <div class="form-group"><label class="form-label" for="fullName">Phone number</label>
                        <div class="input-wrapper"><i class="fas fa-user input-icon"></i> <input
                                v-model="registrationStore.phone" id="firstName" class="form-input"
                                placeholder="John Doe" required=""></div>
                        <div class="form-error" id="fullNameError">Please enter your phone number</div>
                    </div>

                    <div class="form-group"><label class="form-label" for="fullName">BVN</label>
                        <div class="input-wrapper"><i class="fas fa-user input-icon"></i> <input
                                v-model="registrationStore.bvn" id="bvn" class="form-input"
                                placeholder="John Doe" required=""></div>
                        <div class="form-error" id="fullNameError">Please enter your BVN</div>
                    </div>

                    <!-- <div class="form-group"><label class="form-label" for="fullName">Last Name</label>
                        <div class="input-wrapper"><i class="fas fa-user input-icon"></i> <input v-model="registrationStore.lastName" id="lastName"
                                class="form-input" placeholder="John Doe" required=""></div>
                        <div class="form-error" id="fullNameError">Please enter your last name</div>
                    </div> -->

                    <!-- <div class="form-group"><label class="form-label" for="email">Email Address</label>
                        <div class="input-wrapper"><i class="fas fa-envelope input-icon"></i> <input v-model="registrationStore.email" type="email"
                                id="email" class="form-input" placeholder="john@company.com" required=""></div>
                        <div class="form-error" id="emailError">Please enter a valid email address</div>
                    </div> -->
                    <!-- <div class="form-group"><label class="form-label" for="company">Company Name (Optional)</label>
                        <div class="input-wrapper"><i class="fas fa-building input-icon"></i> <input id="company"
                                class="form-input" placeholder="Acme Inc."></div>
                    </div> -->
                    <!-- <div class="form-group"><label class="form-label" for="password">Password</label>
                        <div class="input-wrapper"><i class="fas fa-lock input-icon"></i> <input v-model="registrationStore.password" type="password"
                                id="password"
                                class="form-input input-support focus:border focus:border-primary! rounded-xl!"
                                placeholder="••••••••" required="" oninput="checkPasswordStrength()"> <i
                                class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i></div>
                         <div class="password-strength">
                            <div class="password-strength-bar" id="strengthBar"></div>
                        </div> -->
                    <!-- <div class="password-strength-text" id="strengthText"></div>
                        <div class="form-error" id="passwordError">Password must be at least 8 characters</div>
                    </div> -->
                    <!-- <div class="form-group"><label class="form-label" for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper"><i class="fas fa-lock input-icon"></i> <input name="password_confirmation" type="password"
                                id="confirmPassword"
                                class="form-input input-support focus:border focus:border-primary! rounded-xl!"
                                placeholder="••••••••" required=""> <i class="fas fa-eye password-toggle"
                                onclick="togglePassword('confirmPassword')"></i></div>
                        <div class="form-error" id="confirmPasswordError">Passwords do not match</div>
                    </div> -->
                    <div class="form-group">
                        <div class="checkbox-wrapper"><input type="checkbox" id="terms" class="custom-checkbox"
                                required=""> <label for="terms" style="font-size: 0.875rem; cursor: pointer">I
                                agree to
                                the <a href="#" style="color: #667eea; text-decoration: none">Terms of
                                    Service</a> and
                                <a href="#" style="color: #667eea; text-decoration: none">Privacy
                                    Policy</a></label>
                        </div>
                        <div class="form-error" id="termsError">You must accept the terms and conditions</div>
                    </div><button type="submit" class="btn btn-primary" id="submitBtn">Create Account</button>
                    <div class="dividerRegistrationPage"><span>or continue with</span></div>
                    <!-- <div class="social-buttons"><button type="button" class="btn-secondary"
                            onclick="socialLogin('google')"><svg width="20" height="20" viewbox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4"></path>
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853"></path>
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                    fill="#FBBC05"></path>
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335"></path>
                            </svg> Google</button></div> -->
                    <div class="footer-link">Already have an account? <a href="loginpage.html">Create Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>



<style>

</style>
