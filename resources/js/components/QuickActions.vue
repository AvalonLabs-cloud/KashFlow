// QuickActions.vue
<template>
    <section class="quick-actions">
        <ActionButton role="btn" @click="handleSendClick" :icon="sendMoneySvg()" label="Send" />
        <ActionButton role="btn" @click="handleAirtime" :icon="airtimeSvg()" label="Airtime" />
        <ActionButton role="btn" @click="handleData" :icon="dataSvg()" label="Data" />
        <UpComingFeature/>
    </section>
</template>

<script lang="ts" setup>
import { router } from '@inertiajs/vue3';
import { sendMoneySvg } from '@/svgs/svg';
import {airtimeSvg} from '@/svgs/svg';
import {dataSvg} from '@/svgs/svg';
import ActionButton from '../components/ActionButton.vue';
import UpComingFeature from './notitifications/UpComingFeature.vue';

const handleSendClick = () => {
    router.visit('/transfer', {
        method: 'get',
    });
};

const handleAirtime = () => {
    router.visit('/airtime', {
        method: 'get',
    });
};

const handleData = () => {
    router.visit('/data', {
        method: 'get',
    });
};
</script>

<style scoped>
.quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
</style>


