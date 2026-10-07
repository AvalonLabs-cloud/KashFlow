<template>
    <div class="features-card">
        <div class="features-icon">✨</div>

        <div class="features-content">
            <span class="features-title">
                More features are on the way
            </span>

            <span class="features-text">
                We're constantly improving the platform and adding new features to make your experience better.
            </span>
        </div>
    </div>
</template>

<script lang="ts" setup>

</script>


<style scoped>
.quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.features-card {
    grid-column: 1 / -1;

    width: 100%;
    min-height: 44px;
    padding: 8px 12px;

    display: flex;
    align-items: center;
    gap: 9px;

    box-sizing: border-box;

    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

.features-icon {
    width: 28px;
    height: 28px;
    flex: 0 0 28px;

    display: grid;
    place-items: center;

    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 7px;

    font-size: 13px;
}

.features-content {
    min-width: 0;
    display: flex;
    align-items: baseline;
    gap: 6px;
    flex-wrap: wrap;
}

.features-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
}

.features-text {
    font-size: 12px;
    line-height: 1.35;
    color: #64748b;
}

/* Mobile */
@media (max-width: 480px) {
    .features-card {
        min-height: 46px;
        padding: 7px 10px;
        gap: 8px;
    }

    .features-icon {
        width: 26px;
        height: 26px;
        flex-basis: 26px;
    }

    .features-content {
        display: block;
    }

    .features-title,
    .features-text {
        font-size: 11px;
        line-height: 1.35;
    }

    .features-title {
        margin-right: 4px;
    }
}
</style>