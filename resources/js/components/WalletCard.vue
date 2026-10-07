<script lang="ts" setup>

import { Send } from 'lucide-vue-next';
const props = defineProps<{
  balance: number;
}>();

const emit = defineEmits(['transfer-toggle'])
</script>
<template>
    <div class="2xl:col-span-2 col-span-1">
                    <div class="wallet-card-primary fade-in h-full relative">
                        <div
                            class="flex flex-col gap-y-6 customXl2::gap-y-10 relative z-10 h-full customXl2:justify-between">
                            <div
                                class="flex sm:flex-row flex-col customXl2:items-start sm:items-center items-start customXl2:gap-x-0 gap-x-2.5 justify-between customXl2:mb-8">
                                <div class="relative w-full sm:w-auto">
                                    <p class="text-white/80 text-xs sm:text-sm md:text-base font-semibold mb-1 sm:mb-2">
                                        Primary Wallet</p>
                                    <h2 class="text-2xl sm:text-3xl md:text-4xl xl:text-5xl font-bold text-white mb-2">
                                        ${{ props.balance }}</h2>
                                    <div class="flex items-center gap-1 sm:gap-2"><span
                                            class="badge-modern bg-white/20 text-white border-white/30 text-xs sm:text-sm md:text-base px-2 py-1 rounded"><i
                                                data-lucide="trending-up" class="w-3 h-3 sm:w-4 sm:h-4"></i> +12.5% this
                                            month</span></div><span
                                        class="absolute h-full w-full top-0 left-0 bg-[rgba(255,255,255,0.2)] rounded-lg shadow-[0_4px_30px_rgba(0,0,0,0.1)] backdrop-blur-[9.9px] hidden"
                                        id="amountHide"></span>
                                </div>
                                <div class="flex flex-row-reverse gap-x-3 sm:gap-x-4 md:gap-x-5 mt-4 sm:mt-0 relative">
                                    <button
                                        class="w-10 sm:w-12 md:w-14 h-10 sm:h-12 md:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-white/30 transition-all"
                                        onclick="primaryWalletMenu()"><i data-lucide="more-vertical"
                                            class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i></button>
                                    <div class="absolute flex flex-col gap-y-3 sm:gap-y-4 md:gap-y-5 bg-panel backdrop-blur-[20px] py-2 px-1.5 rounded-lg top-[120%] right-0 sm:right-5 z-40 w-full max-w-[200px] md:max-w-[250px] transition-all duration-200 ease-linear opacity-0 pointer-events-none max-h-[300px] overflow-y-auto"
                                        id="primaryWalletMenu"><button
                                            class="flex flex-row justify-center gap-x-2.5 text-muted transition-all duration-150 ease-linear hover:text-text"><span><i
                                                    data-lucide="hat-glasses"></i></span> option 1</button> <button
                                            class="flex flex-row justify-center gap-x-2.5 text-muted transition-all duration-150 ease-linear hover:text-text"><span><i
                                                    data-lucide="hat-glasses"></i></span> option 2</button> <button
                                            class="flex flex-row justify-center gap-x-2.5 text-muted transition-all duration-150 ease-linear hover:text-text"><span><i
                                                    data-lucide="hat-glasses"></i></span> option 3</button> <button
                                            class="flex flex-row justify-center gap-x-2.5 text-muted transition-all duration-150 ease-linear hover:text-text"><span><i
                                                    data-lucide="hat-glasses"></i></span> option 4</button></div><button
                                        class="w-10 sm:w-12 md:w-14 h-10 sm:h-12 md:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center hover:bg-white/30 transition-all"
                                        id="walletEyeIcon" onclick="walletAmountHide()"><i data-lucide="eye"
                                            class="w-5 sm:w-6 h-5 sm:h-6 text-white"></i></button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mt-4 sm:mt-6"><button
                                    class="quick-action-btn" onclick="openModal('send-modal')">
                                    <Send @click="emit('transfer-toggle')" class="w-4 sm:w-5 h-4 sm:h-5"/>
                                    <!-- <i data-lucide="send"
                                        class="w-4 sm:w-5 h-4 sm:h-5"></i> -->

                                        <span
                                        class="hidden sm:inline">Send</span></button> <button class="quick-action-btn"
                                    onclick="openModal('receive-modal')"><i data-lucide="download"
                                        class="w-4 sm:w-5 h-4 sm:h-5"></i> <span
                                        class="hidden sm:inline">Receive</span></button> <a class="quick-action-btn"
                                    href="invoice.html"><i data-lucide="file-text" class="w-4 sm:w-5 h-4 sm:h-5"></i>
                                    <span class="hidden sm:inline">Invoice</span> </a><a class="quick-action-btn"
                                    href="activecards.html"><i data-lucide="credit-card"
                                        class="w-4 sm:w-5 h-4 sm:h-5"></i> <span
                                        class="hidden sm:inline">Card</span></a></div>
                            <div class="flex flex-row flex-wrap gap-x-3 gap-y-4 sm:gap-x-5 sm:gap-y-5"
                                id="renderUserList"></div>
                        </div>
                    </div>
                </div>

</template>



<style>

</style>
