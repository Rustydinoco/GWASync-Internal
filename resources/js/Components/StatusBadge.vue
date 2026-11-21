<script setup>
defineProps({
    status: {
        type: String,
        default: 'default'
    }
});
</script>

<template>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
        <slot>{{ status }}</slot>
    </span>
</template>
