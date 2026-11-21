<script setup>
import { ref, computed, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const isSidebarOpen = ref(false);
const page = usePage();

const userRole = computed(() => page.props.auth?.user?.role ?? 'anggota');

const flashSuccess = ref(null);
const flashError = ref(null);

watch(
    () => page.props.flash,
    (flash) => {
        flashSuccess.value = flash?.success ?? null;
        flashError.value = flash?.error ?? null;
    },
    { immediate: true, deep: true }
);

const navGroups = [
    {
        id: 'utama',
        label: 'Utama',
        items: [
            {
                name: 'Dashboard',
                href: '/dashboard',
                icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                roles: ['ketua_umum', 'pengurus', 'anggota'],
            },
            {
                name: 'KTA Digital',
                href: '/kta',
                icon: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
                roles: ['ketua_umum', 'pengurus', 'anggota'],
            },
        ],
    },
    {
        id: 'akses',
        label: 'Organisasi & Akses',
        icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        items: [
            { name: 'Roles', href: '/roles', roles: ['ketua_umum'] },
            { name: 'Jabatan Pengurus', href: '/staff-positions', roles: ['ketua_umum'] },
            { name: 'Unit Kerja', href: '/work-units', roles: ['ketua_umum'] },
            { name: 'Alur Persetujuan', href: '/approval-flows', roles: ['ketua_umum'] },
            { name: 'Tahap Persetujuan', href: '/approval-flow-steps', roles: ['ketua_umum'] },
        ],
    },
    {
        id: 'anggota',
        label: 'Anggota & Rekrutmen',
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        items: [
            { name: 'Master Anggota', href: '/members', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Akun Pengguna', href: '/users', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Rekrutmen', href: '/recruitments', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Pelamar Rekrutmen', href: '/recruitment-applicants', roles: ['ketua_umum', 'pengurus'] },
        ],
    },
    {
        id: 'kegiatan',
        label: 'Program & Kegiatan',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        items: [
            { name: 'Program Kerja', href: '/programs', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Kalender Event', href: '/events', roles: ['ketua_umum', 'pengurus', 'anggota'] },
            { name: 'Presensi QR', href: '/attendances', roles: ['ketua_umum', 'pengurus', 'anggota'] },
            { name: 'Sesi Presensi', href: '/attendance-sessions', roles: ['ketua_umum', 'pengurus'] },
        ],
    },
    {
        id: 'inventaris',
        label: 'Inventaris & Logistik',
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        items: [
            { name: 'Master Inventaris', href: '/inventories', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Peminjaman Alat', href: '/borrowings', roles: ['ketua_umum', 'pengurus', 'anggota'] },
        ],
    },
    {
        id: 'keuangan',
        label: 'Keuangan',
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        items: [
            { name: 'Akun Keuangan', href: '/financial-accounts', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Kategori Keuangan', href: '/financial-categories', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Transaksi Keuangan', href: '/financial-transactions', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Pengajuan Dana', href: '/funding-requests', roles: ['ketua_umum', 'pengurus'] },
        ],
    },
    {
        id: 'persuratan',
        label: 'Persuratan',
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        items: [
            { name: 'Surat Masuk/Keluar', href: '/letters', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Persetujuan Surat', href: '/letter-approvals', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Disposisi Surat', href: '/letter-dispositions', roles: ['ketua_umum', 'pengurus'] },
        ],
    },
    {
        id: 'kemitraan',
        label: 'Kemitraan & Sponsor',
        icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        items: [
            { name: 'Paket Sponsorship', href: '/sponsorship-packages', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Data Sponsorship', href: '/sponsorships', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Perjanjian Mitra', href: '/partnership-agreements', roles: ['ketua_umum', 'pengurus'] },
        ],
    },
    {
        id: 'evaluasi',
        label: 'Evaluasi & Formulir',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        items: [
            { name: 'Daftar Formulir', href: '/forms', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Pertanyaan Formulir', href: '/form-questions', roles: ['ketua_umum', 'pengurus'] },
            { name: 'Respon Formulir', href: '/form-responses', roles: ['ketua_umum', 'pengurus'] },
        ],
    },
];

const filteredGroups = computed(() =>
    navGroups
        .map((group) => ({
            ...group,
            items: group.items.filter((item) => item.roles.includes(userRole.value)),
        }))
        .filter((group) => group.items.length > 0)
);

const isCurrentUrl = (path) => {
    const cleanUrl = page.url.split('?')[0];
    return cleanUrl === path || cleanUrl.startsWith(path + '/');
};

const openGroups = ref({});

watch(
    () => page.url,
    () => {
        for (const group of filteredGroups.value) {
            if (group.id === 'utama') continue;
            if (group.items.some((item) => isCurrentUrl(item.href))) {
                openGroups.value[group.id] = true;
            }
        }
    },
    { immediate: true }
);

const toggleGroup = (id) => {
    openGroups.value[id] = !openGroups.value[id];
};
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-gray-50 print:h-auto print:overflow-visible print:bg-white">
        <!-- Sidebar Drawer (Desktop & Mobile) -->
        <aside
            :class="[
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-gray-900 transition-transform duration-300 ease-in-out md:static md:w-64 md:translate-x-0 print:hidden flex flex-col justify-between'
            ]"
        >
            <div class="flex flex-col min-h-0 flex-1">
                <!-- Brand Header -->
                <div class="flex h-16 shrink-0 items-center justify-between px-5 bg-gray-950">
                    <Link href="/dashboard" class="flex items-center gap-2.5">
                        <img src="/LOGO GWA BARU.png" alt="Logo GWA" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg object-contain" />
                        <span class="text-lg font-bold text-white tracking-wider">GWASync</span>
                    </Link>

                    <button
                        @click="isSidebarOpen = false"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-white md:hidden"
                        title="Tutup Menu"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Grouped Navigation Links -->
                <nav class="mt-3 px-3 pb-4 space-y-1.5 overflow-y-auto flex-1">
                    <template v-for="group in filteredGroups" :key="group.id">
                        <!-- Top-level items (Utama) -->
                        <template v-if="group.id === 'utama'">
                            <Link
                                v-for="item in group.items"
                                :key="item.href"
                                :href="item.href"
                                @click="isSidebarOpen = false"
                                :class="[
                                    isCurrentUrl(item.href)
                                        ? 'bg-blue-600 text-white font-semibold shadow-sm'
                                        : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                ]"
                                class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-colors"
                            >
                                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                <span class="truncate">{{ item.name }}</span>
                            </Link>
                        </template>

                        <!-- Collapsible module groups -->
                        <div v-else class="pt-1">
                            <button
                                type="button"
                                @click="toggleGroup(group.id)"
                                :class="[
                                    group.items.some((i) => isCurrentUrl(i.href))
                                        ? 'text-white bg-gray-800/80'
                                        : 'text-gray-300 hover:bg-gray-800 hover:text-white'
                                ]"
                                class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors"
                            >
                                <span class="flex items-center gap-3 min-w-0">
                                    <svg class="w-5 h-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="group.icon" />
                                    </svg>
                                    <span class="truncate">{{ group.label }}</span>
                                </span>
                                <svg
                                    :class="openGroups[group.id] ? 'rotate-180' : ''"
                                    class="w-4 h-4 shrink-0 text-gray-400 transition-transform duration-200"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div v-show="openGroups[group.id]" class="mt-1 ml-4 pl-3 border-l border-gray-700/80 space-y-1">
                                <Link
                                    v-for="item in group.items"
                                    :key="item.href"
                                    :href="item.href"
                                    @click="isSidebarOpen = false"
                                    :class="[
                                        isCurrentUrl(item.href)
                                            ? 'bg-blue-600 text-white font-semibold shadow-sm'
                                            : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                    ]"
                                    class="block px-3 py-2 rounded-lg text-xs sm:text-sm transition-colors truncate"
                                >
                                    {{ item.name }}
                                </Link>
                            </div>
                        </div>
                    </template>
                </nav>
            </div>

            <!-- User Info di bagian bawah Sidebar -->
            <div class="p-4 border-t border-gray-800/80 bg-gray-950/40 shrink-0">
                <div class="flex items-center justify-between gap-2">
                    <Link href="/profile" @click="isSidebarOpen = false" class="flex items-center gap-2.5 min-w-0 group">
                        <div class="h-9 w-9 shrink-0 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ $page.props.auth?.user?.name ? $page.props.auth.user.name.charAt(0).toUpperCase() : 'A' }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-white group-hover:text-blue-400 truncate leading-tight transition-colors">
                                {{ $page.props.auth?.user?.name ?? 'Admin' }}
                            </p>
                            <p class="text-[10px] text-gray-400 capitalize truncate">
                                {{ ($page.props.auth?.user?.role ?? 'Member').replace('_', ' ') }}
                            </p>
                        </div>
                    </Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="p-2 text-gray-400 hover:text-red-400 rounded-lg transition-colors"
                        title="Keluar"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Overlay Mobile -->
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/60 backdrop-blur-xs md:hidden print:hidden transition-opacity"
        ></div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 print:block">
            <!-- Topbar Header -->
            <header class="flex h-14 sm:h-16 items-center justify-between bg-white px-4 sm:px-6 shadow-xs border-b border-gray-100 print:hidden z-10 shrink-0">
                <div class="flex items-center gap-3">
                    <button
                        @click="isSidebarOpen = true"
                        class="p-2 -ml-1 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100 md:hidden"
                        title="Buka Menu"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <h2 class="text-base sm:text-lg font-bold text-gray-800 truncate">
                        GWASync Internal
                    </h2>
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    <Link href="/profile" class="flex items-center gap-2.5 group">
                        <div class="text-right hidden sm:block" v-if="$page.props.auth?.user">
                            <span class="text-xs sm:text-sm font-semibold text-gray-800 group-hover:text-blue-600 block leading-tight transition-colors">
                                {{ $page.props.auth.user.name }}
                            </span>
                            <span class="text-[10px] sm:text-xs text-gray-500 capitalize">
                                {{ ($page.props.auth.user.role || 'Member').replace('_', ' ') }}
                            </span>
                        </div>

                        <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs sm:text-sm shadow-xs">
                            {{ $page.props.auth?.user?.name ? $page.props.auth.user.name.charAt(0).toUpperCase() : 'A' }}
                        </div>
                    </Link>

                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="p-1.5 sm:p-2 text-gray-400 hover:text-red-600 rounded-lg transition-colors"
                        title="Keluar"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </Link>
                </div>
            </header>

            <!-- Main Scrollable Body -->
            <main class="flex-1 overflow-y-auto p-3.5 sm:p-6 pb-24 md:pb-6 print:p-0 print:overflow-visible print:m-0">
                <!-- Flash Notifications -->
                <div v-if="flashSuccess" class="mb-4 max-w-6xl mx-auto flex items-center justify-between gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 print:hidden">
                    <span>{{ flashSuccess }}</span>
                    <button type="button" @click="flashSuccess = null" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
                </div>
                <div v-if="flashError" class="mb-4 max-w-6xl mx-auto flex items-center justify-between gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 print:hidden">
                    <span>{{ flashError }}</span>
                    <button type="button" @click="flashError = null" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
                </div>

                <slot />
            </main>
        </div>

        <!-- MOBILE BOTTOM NAVIGATION BAR -->
        <nav class="fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-gray-200 py-1.5 px-3 flex items-center justify-around md:hidden print:hidden shadow-lg">
            <Link
                href="/dashboard"
                :class="isCurrentUrl('/dashboard') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-800'"
                class="flex flex-col items-center py-1 px-2 text-[10px] transition-colors"
                v-if="userRole !== 'calon'"
            >
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </Link>

            <Link
                href="/inventories"
                :class="isCurrentUrl('/inventories') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-800'"
                class="flex flex-col items-center py-1 px-2 text-[10px] transition-colors"
                v-if="['ketua_umum', 'pengurus'].includes(userRole)"
            >
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Alat</span>
            </Link>

            <Link
                href="/attendances"
                class="flex flex-col items-center -mt-4 py-1 px-2.5 transition-transform active:scale-95"
                v-if="['ketua_umum', 'pengurus', 'anggota'].includes(userRole)"
            >
                <div :class="isCurrentUrl('/attendances') ? 'bg-blue-700 ring-4 ring-blue-100' : 'bg-blue-600'" class="w-12 h-12 rounded-full flex items-center justify-center text-white shadow-md">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 001 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 001 1z" />
                    </svg>
                </div>
                <span class="text-[10px] font-semibold text-blue-600 mt-0.5">Presensi</span>
            </Link>

            <Link
                href="/kta"
                :class="isCurrentUrl('/kta') ? 'text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-800'"
                class="flex flex-col items-center py-1 px-2 text-[10px] transition-colors"
                v-if="['ketua_umum', 'pengurus', 'anggota'].includes(userRole)"
            >
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                </svg>
                <span>KTA</span>
            </Link>

            <button
                @click="isSidebarOpen = true"
                class="flex flex-col items-center py-1 px-2 text-[10px] text-gray-500 hover:text-gray-800 transition-colors"
            >
                <svg class="w-5 h-5 mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>Menu</span>
            </button>
        </nav>
    </div>
</template>
