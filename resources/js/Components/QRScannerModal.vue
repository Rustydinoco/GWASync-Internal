<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

defineEmits(['close', 'scanned']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-bold text-gray-900">Scan QR Code</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="py-6 text-center text-sm text-gray-500">
                <slot>Kamera siap untuk memindai.</slot>
            </div>
        </div>
    </div>
</template>
