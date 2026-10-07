<template>
<div class="spinner"></div>
</template>

<script lang="ts" setup>

</script>

<style>
.spinner {
    width: 24px;
    height: 24px;
    border: 3px solid #dcfce7;
    border-top-color: #16a34a;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
