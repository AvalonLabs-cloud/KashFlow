<script lang="ts" setup>
import { ref, computed } from 'vue';
import { useRegistrationStore } from '@/stores/registerandcreateaccount';
import stepone from './registerform/stepone.vue';
import steptwo from './registerform/steptwo.vue';

type StepTypes = keyof typeof components;

const components = {
    stepone: stepone,
    steptwo: steptwo,
}

const loading = ref(false);
const currentStep = ref<StepTypes>('stepone');
const currentComponent = computed(() => components[currentStep.value as StepTypes]);

const goToNextStep = () => {

    // front end validation
    loading.value = true;
    // store value in session
    // storeData();
    setTimeout(() => {
        if (currentStep.value === 'stepone') {
            loading.value = false;
            currentStep.value = 'steptwo';
        } else if (currentStep.value === 'steptwo') {
            //  Handle form submission or navigate to the next step
            console.log('form submitted'); // placeholder for form submission logic

        }
    }, 2000);
}

function goToPreviousStep() {
    if (currentStep.value === 'steptwo') {
        currentStep.value = 'stepone';
    }
}

</script>

<template>
    <div>
        <vue3-snackbar top bottom shadow :dismiss-on-action-click="false"></vue3-snackbar>
        <div class="form-container">
            <Transition name="slide" mode="out-in">
                <component v-if="!loading" :is="currentComponent" :key="currentStep" @next="goToNextStep"
                    @previous="goToPreviousStep" style="margin-top: 17vh;" />
            </Transition>
            <div v-if="loading" class="center-box">
                <div class="loader">loading</div>
            </div>

        </div>
    </div>

</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.4s ease;
    position: absolute;
    width: 100%;
}

.slide-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.slide-enter-to {
    transform: translateX(0%);
    opacity: 1;
}

.slide-leave-from {
    transform: translateX(0%);
    opacity: 1;
}

.slide-leave-to {
    transform: translateX(-100%);
    opacity: 0;
}

.form-container {
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
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
