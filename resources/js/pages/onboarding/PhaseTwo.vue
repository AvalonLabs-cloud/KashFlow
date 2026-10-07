<template>
    <div>
        <div class="email-verification-page">
            <div v-show="$page.flash.message" class="success-toast">
                {{ $page.flash.message }}
            </div>

            <main class="veri-main">
                <div class="content-wrapper">
                    <div class="verification-card">
                        <div class="icon-container">
                            <svg class="email-icon" xmlns="http://www.w3.org/2000/svg" height="48px"
                                viewBox="0 -960 960 960" width="48px" fill="#0058be">
                                <path
                                    d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T912-720v480q0 33-23.5 56.5T840-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z" />
                            </svg>
                        </div>

                        <div class="text-group">
                            <h1 class="card-title">Verify your email</h1>
                            <p class="card-description">
                                We've sent a verification link to your email address. Please check your inbox and click
                                the link to verify your account.
                            </p>
                        </div>

                        <div class="actions-group">
                            <button @click="navigateToPhaseThree" class="btn btn-primary">
                                <span>I've verified my email</span>
                                <!-- <span class="material-symbols-outlined font-icon-arrow" data-icon="arrow_forward">arrow_forward</span> -->
                            </button>

                            <form @click.prevent="resendVerificationEmail">
                                <button type="submit" class="btn btn-secondary" id="resendBtn">
                                    <span id="btnText">Resend verification email</span>
                                    <div class="loader hidden" id="btnLoader"></div>
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            </main>

            <div class="toast-container hidden" id="toast">
                <div class="toast-card toast-entrance">
                    <span class="material-symbols-outlined icon-success" data-icon="check_circle">check_circle</span>
                    <span class="toast-text">Verification link has been resent.</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { router } from "@inertiajs/vue3";
import axios from 'axios';

const resendVerificationEmail = async () => {
    try {
        const response = await axios.post(
            '/onboarding/email/verification-notification'
        );
        console.log(response.data);

        // this.success = response.data.message;
        // this.error = null;
    } catch (error) {
        console.log(error);
        // this.error
        //     error.response?.data?.message ||
        //     'Failed to send verification email.';
    }
}

const navigateToPhaseThree = async () => {
    router.visit('/onboarding/phase_three', { method: 'get' });
}

</script>

<style>
.email-verification-page {
    --colors-background: #f8f9ff;
    --colors-surface-container-lowest: #ffffff;
    --colors-surface-container-low: #eff4ff;
    --colors-inverse-surface: #213145;
    --colors-inverse-on-surface: #eaf1ff;
    --colors-primary: #00be20;
    --colors-primary-container: #21e472;
    --colors-on-primary: #ffffff;
    --colors-on-surface: #0b1c30;
    --colors-on-surface-variant: #424754;
    --colors-secondary-fixed: #6ffbbe;

    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
    --spacing-2xl: 48px;
    --spacing-3xl: 64px;

    background-color: var(--colors-background);
    color: var(--colors-on-surface);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.5;
    font-weight: 400;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow: hidden;
    box-sizing: border-box;
}

.email-verification-page *,
.email-verification-page *::before,
.email-verification-page *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.email-verification-page .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    display: inline-block;
    line-height: 1;
    width: 1em;
    height: 1em;
}

.email-verification-page .veri-header {
    background-color: transparent;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 96px;
    width: 100%;
    padding-left: var(--spacing-xl);
    padding-right: var(--spacing-xl);
    max-width: 1440px;
    margin-left: auto;
    margin-right: auto;
}

.email-verification-page .logo-text {
    font-size: 24px;
    line-height: 1.3;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--colors-primary);
}

.email-verification-page .veri-main {
    flex-grow: 1;
    display: flex;
    items-center: center;
    justify-content: center;
    align-items: center;
    padding-left: var(--spacing-md);
    padding-right: var(--spacing-md);
    width: 100%;
    padding-top: var(--spacing-lg);
    padding-bottom: var(--spacing-lg);
}

@media (min-width: 768px) {
    .email-verification-page .veri-main {
        padding-left: var(--spacing-xl);
        padding-right: var(--spacing-xl);
    }
}

.email-verification-page .content-wrapper {
    width: 100%;
    max-width: 520px;
}

.email-verification-page .verification-card {
    background-color: var(--colors-surface-container-lowest);
    border-radius: 1.5rem;
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.04), 0 20px 25px -5px rgba(0, 0, 0, 0.02);
    padding: var(--spacing-xl);
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

@media (min-width: 768px) {
    .email-verification-page .verification-card {
        padding: var(--spacing-3xl);
    }
}

.email-verification-page .icon-container {
    margin-bottom: var(--spacing-xl);
}

.email-verification-page .email-icon {
    display: block;
}

.email-verification-page .text-group {
    margin-bottom: var(--spacing-xl);
}

.email-verification-page .text-group>*+* {
    margin-top: var(--spacing-lg);
}

.email-verification-page .card-title {
    font-size: 32px;
    line-height: 1.2;
    letter-spacing: -0.01em;
    font-weight: 600;
    color: var(--colors-on-surface);
}

.email-verification-page .card-description {
    font-size: 18px;
    line-height: 1.6;
    font-weight: 400;
    color: var(--colors-on-surface-variant);
    max-width: 24rem;
    margin-left: auto;
    margin-right: auto;
}

.email-verification-page .email-display {
    width: 100%;
    background-color: var(--colors-surface-container-low);
    border-radius: 0.75rem;
    padding-top: var(--spacing-lg);
    padding-bottom: var(--spacing-lg);
    padding-left: var(--spacing-xl);
    padding-right: var(--spacing-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-bottom: var(--spacing-xl);
}

.email-verification-page .icon-email {
    color: rgba(0, 88, 190, 0.7);
}

.email-verification-page .email-address {
    font-size: 16px;
    line-height: 1.5;
    font-weight: 600;
    color: var(--colors-on-surface);
    letter-spacing: 0.02em;
}

.email-verification-page .actions-group {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
}

.email-verification-page .btn {
    width: 100%;
    height: 56px;
    border-radius: 0.75rem;
    font-size: 14px;
    line-height: 1;
    letter-spacing: 0.01em;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
    cursor: pointer;
    border: none;
    transition: all 0.2s ease;
    position: relative;
    outline: none;
}

.email-verification-page .btn-primary {
    /* background-color:#00be20; */
    /* color: var(--colors-on-primary); */
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.email-verification-page .btn-primary:hover {
    background-color: var(--colors-primary-container);
}

.email-verification-page .btn-primary:active {
    transform: scale(0.99);
}

.email-verification-page .btn-secondary {
    background-color: transparent;
    /* color: var(--colors-primary); */
    overflow: hidden;
}

.email-verification-page .btn-secondary:hover:not(:disabled) {
    background-color: var(--colors-surface-container-low);
}

.email-verification-page .btn-secondary:disabled {
    cursor: not-allowed;
}

.email-verification-page .font-icon-arrow {
    font-size: 20px;
}

.email-verification-page .footer-links {
    margin-top: var(--spacing-2xl);
    text-align: center;
}

.email-verification-page .footer-links>*+* {
    margin-top: var(--spacing-xl);
}

.email-verification-page .spam-notice {
    font-size: 14px;
    line-height: 1.5;
    font-weight: 400;
    color: rgba(66, 71, 84, 0.8);
}

.email-verification-page .spam-link {
    color: var(--colors-primary);
    text-decoration: none;
    font-weight: 500;
}

.email-verification-page .spam-link:hover {
    text-decoration: underline;
}

.email-verification-page .extra-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--spacing-xl);
}

.email-verification-page .action-link-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 11px;
    line-height: 1;
    letter-spacing: 0.05em;
    font-weight: 600;
    text-transform: uppercase;
    color: rgba(0, 88, 190, 0.7);
    transition: color 0.2s ease;
    outline: none;
}

.email-verification-page .action-link-btn:hover {
    color: var(--colors-primary);
}

.email-verification-page .toast-container {
    position: fixed;
    bottom: var(--spacing-xl);
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
}

.email-verification-page .toast-container.hidden {
    display: none;
}

.email-verification-page .toast-card {
    background-color: var(--colors-inverse-surface);
    color: var(--colors-inverse-on-surface);
    padding-left: var(--spacing-xl);
    padding-right: var(--spacing-xl);
    padding-top: var(--spacing-md);
    padding-bottom: var(--spacing-md);
    border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    gap: 12px;
}

.email-verification-page .toast-entrance {
    animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.email-verification-page .icon-success {
    color: var(--colors-secondary-fixed);
    font-variation-settings: 'FILL' 1 !important;
}

.email-verification-page .toast-text {
    font-size: 14px;
    line-height: 1;
    letter-spacing: 0.01em;
    font-weight: 500;
}

.email-verification-page .loader {
    border: 2px solid rgba(0, 88, 190, 0.1);
    border-top: 2px solid #0058be;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 0.8s linear infinite;
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -8px;
    margin-top: -8px;
}

.email-verification-page .loader.hidden {
    display: none;
}

.email-verification-page .opacity-0 {
    opacity: 0;
}

@keyframes slideUp {
    from {
        transform: translate(-50%, 100%);
        opacity: 0;
    }

    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

/* Script-applied state classes */
.email-verification-page .btn-secondary.text-on-surface-variant\/40 {
    color: rgba(66, 71, 84, 0.4);
}

.success-toast {
    position: fixed;
    top: 20px;
    right: 20px;

    background: #ecfdf3;
    color: #166534;
    border: 1px solid #86efac;

    padding: 12px 16px;
    border-radius: 8px;

    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);

    font-size: 14px;
    font-weight: 500;

    z-index: 9999;

    animation: fadeInOut 3s ease forwards;
}

@keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: translateY(-10px);
    }

    10% {
        opacity: 1;
        transform: translateY(0);
    }

    80% {
        opacity: 1;
    }

    100% {
        opacity: 0;
        transform: translateY(-10px);
    }
}
</style>
