<script lang="ts" setup>
import { Form, Field, ErrorMessage } from 'vee-validate';
import { reactive, ref } from 'vue';
import { useSnackbar } from "vue3-snackbar";
import { useRegistrationStore } from '@/stores/registerandcreateaccount';
import { eyeSvg } from '../../svgs/svg';
import { importantCredentialLockSvgUpperPart } from '../../svgs/svg';
import { importantCredentialLockSvgLowerPart } from '../../svgs/svg';
import { eyeOpenSvg } from '../../svgs/svg';
import { eyeClosedSvg } from '../../svgs/svg';


const registerStore = useRegistrationStore();
const snackbar = useSnackbar();


const emit = defineEmits(['next']);

const showpassword = ref(false);

const passwordValidationStore = reactive({
    password: '',
})


async function nextStep(value) {
    const firstStage = registerStore.setFirstStageCredentials(value)

    if (firstStage) {
        emit('next')

        return
    } else {
        snackbar.add({
            type: 'error',
            text: 'Something went wrong'
        })

        return
    }

}

function validatefirstname(value: any) {
    console.log(value);

    if (!value) {
        return 'First name is required';
    }

    if (value.length < 3) {
        return 'First name must be at least 3 characters';
    }

    if (value.length > 23) {
        return 'First name must be at most 20 characters';
    }

    return true
}

function validateLastName(value: any) {
    if (!value) {
        return 'Last name is required';
    }

    if (value.length < 3) {
        return 'Last name must be at least 3 characters';
    }

    if (value.length > 23) {
        return 'Last name must be at most 23 characters';
    }

    return true
}

function validateEmail(value: any) {
    if (!value) {
        return 'Email is required';
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(value)) {
        return 'Invalid email format';
    }

    return true
}

function validatePassword(value: any) {
    if (!value) {
        return 'Password is required';
    }

    if (value.length < 8) {
        return 'Password must be at least 8 characters';
    }

    // Regex: 1 Uppercase, 1 Lowercase, 1 Number [1, 11]
    const strongPassword = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

    if (!strongPassword.test(value)) {
        return 'Password must include uppercase, lowercase, and a number';
    }

    return true
}


function validateConfirmPassword(confirmPassword: any) {


    if (!confirmPassword) {
        return 'Password is required';
    }

    if (confirmPassword !== passwordValidationStore.password) {
        return 'Passwords do not match';
    }

    return true

}

</script>

<template>
    <Form @submit="nextStep" class="form">
        <div class="flex-column">
            <label>firstname</label>
            <div class="inputForm">
                <Field name="firstname" class="input" placeholder="Enter your first name" :rules="validatefirstname" />
            </div>
            <ErrorMessage as="div" name="firstname" v-slot="{ message }">
                <p class="error-div">{{ message }}</p>
            </ErrorMessage>
        </div>

        <div class="flex-column">
            <label>lastname</label>
            <div class="inputForm">
                <Field name="lastname" type="text" class="input" placeholder="Enter your last name"
                    :rules="validateLastName" />
            </div>
            <ErrorMessage as="div" name="lastname" v-slot="{ message }">
                <p class="error-div">{{ message }}</p>
            </ErrorMessage>
        </div>

        <div class="flex-column">
            <label>Email </label>
            <div class="inputForm">
                <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg">
                    <g id="Layer_3" data-name="Layer 3">
                        <path :d="eyeSvg()">"></path>
                    </g>
                </svg>
                <Field name="email" type="email" class="input" placeholder="abadomemmanuel123@gmail.com"
                    :rules="validateEmail" />
            </div>
            <ErrorMessage as="div" name="email" v-slot="{ message }">
                <p class="error-div">{{ message }}</p>
            </ErrorMessage>
        </div>

        <div class="flex-column">-
            <label>Password </label>
        </div>
        <div class="inputForm">
            <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg">
                <path :d="importantCredentialLockSvgUpperPart()"> "></path>
                <path :d="importantCredentialLockSvgLowerPart()"> "></path>
            </svg>
            <Field v-model="passwordValidationStore.password" name="password" type="password" class="input"
                placeholder="Hashirama@123" :rules="validatePassword" />
            <span v-if="!showpassword" v-html="eyeClosedSvg()"></span>
            <svg v-if="showpassword" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg">
                <path :d="eyeOpenSvg()"></path>
            </svg>
        </div>

        <ErrorMessage as="div" name="password" v-slot="{ message }">
            <p class="error-div">{{ message }}</p>

        </ErrorMessage>

        <div class="flex-column">-
            <label>Confirm Password </label>
        </div>
        <div class="inputForm">
            <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg">
                <path :d="importantCredentialLockSvgUpperPart()"> "></path>
                <path :d="importantCredentialLockSvgLowerPart()"> "></path>
            </svg>
            <Field name="password_confirmation" type="password" class="input" placeholder="confirm password"
                :rules="validateConfirmPassword" />
            <span v-if="!showpassword" v-html="eyeClosedSvg()"></span>
            <svg v-if="showpassword" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg">
                <path :d="eyeOpenSvg()"></path>
            </svg>
        </div>
        <ErrorMessage as="div" name="password_confirmation" v-slot="{ message }">
            <p class="error-div">{{ message }}</p>

        </ErrorMessage>

        <button type="submit" class="button-submit">Continue</button>
        <p class="p">Already have an account? <span class="span">Sign In</span>
        </p>
    </Form>
</template>

<style scoped>
.form {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background-color: #ffffff;
    padding: 30px;
    width: 450px;
    border-radius: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

::placeholder {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
}

.form button {
    align-self: flex-end;
}

.flex-column>label {
    color: #151717;
    font-weight: 600;
}

.inputForm {
    border: 1.5px solid #ecedec;
    border-radius: 10px;
    height: 50px;
    display: flex;
    align-items: center;
    padding-left: 10px;
    transition: 0.2s ease-in-out;
}

.input {
    margin-left: 10px;
    border-radius: 10px;
    border: none;
    width: 85%;
    height: 100%;
}

.input:focus {
    outline: none;
}

.inputForm:focus-within {
    border: 1.5px solid #2d79f3;
}

.flex-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    justify-content: space-between;
}

.flex-row>div>label {
    font-size: 14px;
    color: black;
    font-weight: 400;
}

.span {
    font-size: 14px;
    margin-left: 5px;
    color: #2d79f3;
    font-weight: 500;
    cursor: pointer;
}

.button-submit {
    margin: 20px 0 10px 0;
    background-color: #151717;
    border: none;
    color: white;
    font-size: 15px;
    font-weight: 500;
    border-radius: 10px;
    height: 50px;
    width: 100%;
    cursor: pointer;
}

.button-submit:hover {
    background-color: #252727;
}

.p {
    text-align: center;
    color: black;
    font-size: 14px;
    margin: 5px 0;
}

.btn {
    margin-top: 10px;
    width: 100%;
    height: 50px;
    border-radius: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 500;
    gap: 10px;
    border: 1px solid #ededef;
    background-color: white;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}

.btn:hover {
    border: 1px solid #2d79f3;
    ;
}

.error-text {
    color: #e53935;
    font-size: 0.85rem;
    margin-top: 4px;
}

.error-div {
    background-color: #f8d7da;
    /* Light red background */
    color: #721c24;
    /* Dark red text for contrast */
    border: 1px solid #f5c6cb;
    /* Subtle red border */
    padding: 15px;
    /* Internal spacing */
    margin: 10px 0;
    /* External spacing from other elements */
    border-radius: 4px;
    /* Rounded corners */
    font-family: sans-serif;
    display: block;
    /* Ensures it takes up the full width */
}
</style>
