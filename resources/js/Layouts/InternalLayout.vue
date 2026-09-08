<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const isSidebarOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Inventaris & Logistik', href: '/inventories', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Presensi QR', href: '/attendances', icon: 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z' },
    { name: 'Kalender Event', href: '/events', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { name: 'KTA Digital', href: '/kta', icon: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2' },
];
</script>

<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar (Desktop & Mobile) -->
        <aside 
            :class="[
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 transition-transform duration-300 ease-in-out md:static md:translate-x-0'
            ]"
        >
            <div class="flex h-16 items-center justify-center bg-gray-950">
                <span class="text-xl font-bold text-white tracking-wider">GWASync</span>
            </div>

            <nav class="mt-6 px-4 space-y-2">
                <Link 
                    v-for="item in navigation" 
                    :key="item.name" 
                    :href="item.href"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    <span class="text-sm font-medium">{{ item.name }}</span>
                </Link>
            </nav>
        </aside>

        <!-- Overlay untuk Mobile -->
        <div 
            v-if="isSidebarOpen" 
            @click="isSidebarOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/50 md:hidden"
        ></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="flex h-16 items-center justify-between bg-white px-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <button 
                        @click="isSidebarOpen = true" 
                        class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <h2 class="text-xl font-semibold text-gray-800 hidden sm:block">
                        Internal Panel
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-600">Admin</span>
                    <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                        A
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>