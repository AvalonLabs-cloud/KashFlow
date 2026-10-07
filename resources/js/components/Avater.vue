<script setup lang="ts">
interface Props {
    letter: string;
}

defineProps<Props>();
</script>

<template>
    <div class="avatar">
        {{ letter.charAt(0).toUpperCase() }}
    </div>
</template>

<style scoped>
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;

    background: #e8f5e9;
    color: #198754;

    font-size: 16px;
    font-weight: 600;
    text-transform: uppercase;

    user-select: none;
}
</style>