<script lang="ts" setup>
import { useForm } from '@inertiajs/vue3';
import { Form, Field, ErrorMessage } from 'vee-validate';
import { ref } from 'vue';
import { useSnackbar } from "vue3-snackbar";
import { useRegistrationStore } from '@/stores/registerandcreateaccount';
import { importantCredentialLockSvgUpperPart } from '../../svgs/svg';
import { importantCredentialLockSvgLowerPart } from '../../svgs/svg';
import { eyeOpenSvg } from '../../svgs/svg';
import { eyeClosedSvg } from '../../svgs/svg';


const form = useForm({
    firstname: '',
    lastname: '',
    email: '',
    password: '',
    pin: '',
    bvn: '',
})

const registerationData = useRegistrationStore();

const snackbar = useSnackbar();

const showpassword = ref(false);

const pin = ref('')

const bvn = ref('')

function createAccount() {

    parseDataIntoFormObject();

    form.post('/createaccount', {
        onStart: () => {
            console.log('Processing...');
        },
        onError: (errors) => {
            console.log('form submitted');
            console.log('Errors:', errors);
        },
        onSuccess: () => {
            console.log('Account created');
        },
    });

}

function parseDataIntoFormObject() {
    Object.assign(form, {
        firstname: registerationData.firstName,
        lastname: registerationData.lastName,
        email: registerationData.email,
        password: registerationData.password,
        pin: pin.value,
        bvn:bvn.value,
    });
}


function validatePin(value: any) {
    if (!value) {
        return 'PIN is required';
    }

    if (!/^\d{4}$/.test(value)) {
        return 'PIN must be 4 digits';
    }

    return true
}

function confirmPin(value: any) {
    if (!value) {
        return 'PIN is required';
    }

    if (!/^\d{4}$/.test(value)) {
        return 'PIN must be 4 digits';
    }

    if (value !== pin.value) {
        return 'PIN does not match';
    }

    return true

}

function validateBvn(value: any) {
    if (!value) {
        return 'BVN is required';
    }

    if (!/^\d{11}$/.test(value)) {
        return 'BVN must be exactly 11 digits';
    }

    return true
}

function displayCreateAccountError(error) {

       snackbar.add({
            type: 'error',
            text: error,
        })

}


</script>

<template>
    <Form v-if="!form.processing" @submit="createAccount" class="form">
        <div class="flex-column">-
            <label>Enter Bvn </label>
        </div>


        <div class="inputForm">
            <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg">
                <path :d="importantCredentialLockSvgUpperPart()"> "></path>
                <path :d="importantCredentialLockSvgLowerPart()"> "></path>
            </svg>
            <Field v-model="bvn" name="bvn" class="input" placeholder="Enter Your Bvn" :rules="validateBvn" type="password" />
            <span v-if="!showpassword" v-html="eyeClosedSvg()"></span>
            <svg v-if="showpassword" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg">
                <path :d="eyeOpenSvg()"></path>
            </svg>
        </div>
        <ErrorMessage as="div" name="bvn" v-slot="{ message }">
            <p class="error-div">{{ message }}</p>
        </ErrorMessage>

        <div class="flex-column">-
            <label>Create Your Pin</label>
        </div>


        <div class="inputForm">
            <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg">
                <path :d="importantCredentialLockSvgUpperPart()"> "></path>
                <path :d="importantCredentialLockSvgLowerPart()"> "></path>
            </svg>
            <Field v-model="pin" name="pin" type="password" class="input" placeholder="Enter your Pin 4 digit"
                :rules="validatePin" />
            <span v-if="!showpassword" v-html="eyeClosedSvg()"></span>
            <svg v-if="showpassword" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg">
                <path :d="eyeOpenSvg()"></path>
            </svg>
        </div>
        <ErrorMessage as="div" name="pin" v-slot="{ message }">
            <p class="error-div">{{ message }}</p>
        </ErrorMessage>

        <div class="flex-column">-
            <label>Confirm Pin </label>
        </div>


        <div class="inputForm">
            <svg height="20" viewBox="-64 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg">
                <path :d="importantCredentialLockSvgUpperPart()"> "></path>
                <path :d="importantCredentialLockSvgLowerPart()"> "></path>
            </svg>
            <Field name="pin_confirm" type="password" class="input" placeholder="Confirm your pin"
                :rules="confirmPin" />
            <span v-if="!showpassword" v-html="eyeClosedSvg()"></span>
            <svg v-if="showpassword" viewBox="0 0 576 512" height="1em" xmlns="http://www.w3.org/2000/svg">
                <path :d="eyeOpenSvg()"></path>
            </svg>
        </div>

        <ErrorMessage as="div" name="pin_confirm" v-slot="{ message }">
            <p class="error-div">{{ message }}</p>
        </ErrorMessage>



        <div class="flex-row">
            <div>
                <input type="checkbox">
                <label>Remember me </label>
            </div>
            <span class="span">Forgot password?</span>
        </div>
        <button type="submit" class="button-submit">Create Account</button>
        <p class="p">Already have an account? <span class="span">Sign In</span> </p>
    </Form>

    <!-- // loading state -->
    <div v-if="form.processing" class="center-box">
        <div class="loader">loading</div>
    </div>

   <div v-if="Object.keys(form.errors).length">
    <ul>
        <li v-for="(error, key) in form.errors" :key="key">
            {{ displayCreateAccountError(error) }}

        </li>
    </ul>
  </div>

</template>

<style scoped>
/* From Uiverse.io by micaelgomestavares */
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


.loader {
    width: 50px;
    padding: 8px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: #25b09b;
    --_m:
        conic-gradient(#0000 10%, #000),
        linear-gradient(#000 0 0) content-box;
    -webkit-mask: var(--_m);
    mask: var(--_m);
    -webkit-mask-composite: source-out;
    mask-composite: subtract;
    animation: l3 1s infinite linear;
}

@keyframes l3 {
    to {
        transform: rotate(1turn)
    }
}

.center-box {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    width: 100vw;
}
</style>
