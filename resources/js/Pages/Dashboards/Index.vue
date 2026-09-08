<script setup>
import InternalLayout from '@/Layouts/InternalLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <InternalLayout>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Selamat Datang di GWASync!</h3>
            <p class="text-gray-600">Sistem manajemen logistik dan operasional MBGWA.</p>
        </div>
    </InternalLayout>
</template>