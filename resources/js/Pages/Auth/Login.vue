<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login'), {
        onFinish: () => form.reset('password'),
    };
};
</script>

<template>
    <Head title="Log in - GWASync" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-wider">GWASync</h1>
            <p class="text-sm text-gray-500 mt-1">Sistem Manajemen Internal MBGWA</p>
        </div>

        <div class="w-full max-w-md bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email / NIA</label>
                    <input 
                        type="email" 
                        v-model="form.email" 
                        required 
                        autofocus 
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="admin@gwasync.com"
                    />
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input 
                        type="password" 
                        v-model="form.password" 
                        required 
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        placeholder="••••••••"
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-gray-600">Ingat Saya</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    :disabled="form.processing"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm transition-colors disabled:opacity-50"
                >
                    Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>
</template>