<template>
      <header class="header">
                <div class="header-left">
                    <button @click="goBack" class="icon-button back-button active-tap">
                        <span v-html="chevronBackSvg" class="material-symbols-outlined"></span>
                    </button>
                    <h1 class="header-title">{{ props.pageName }}</h1>
                </div>
                <div class="header-actions">
                    <button class="icon-button action-icon active-tap">
                        <span class="material-symbols-outlined">help</span>
                    </button>
                    <button class="icon-button action-icon active-tap">
                        <span class="material-symbols-outlined">history</span>
                    </button>
                </div>
            </header> 
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { chevronBackSvg } from '@/svgs/svg';

function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/main-index');
    }
}

const props = defineProps([
    'pageName'
])

</script>

<style>
 .header {
    flex-shrink: 0;
    height: 64px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 50;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a231e;
    letter-spacing: -0.02em;
}


.icon-button {
    background: transparent;
    border: none;
    padding: 8px;
    margin: -8px;
    /* Expand click area */
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #1a231e;
    border-radius: 50%;
}

.header-actions {
    display: flex;
    gap: 16px;
}

.action-icon {
    color: #5c6e63;
}

</style>