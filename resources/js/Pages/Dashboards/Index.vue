<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { writeFile, utils } from 'xlsx';

const props = defineProps({
    stats: Object,
    moduleSummary: Object,
    pendingBorrowings: Array,
    recentBorrowings: Array,
    recentAttendances: Array,
    trendData: Array,
    selectedPeriod: String,
    userRole: String,
    user: Object,
});

const isManagement = computed(() => ['ketua_umum', 'pengurus'].includes(props.userRole));

// Reactive trend data (diupdate saat tombol periode diklik)
const trendData = ref(props.trendData || []);
const selectedPeriod = ref(props.selectedPeriod || 'bulanan');

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(value || 0));
};

// Quick action items based on role
const quickActions = computed(() => {
    const actions = [
        {
            title: 'Presensi QR',
            icon: 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z',
            href: '/attendances',
            color: 'bg-blue-500',
            roles: ['ketua_umum', 'pengurus', 'anggota'],
        },
        {
            title: 'Inventaris',
            icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            href: '/inventories',
            color: 'bg-purple-500',
            roles: ['ketua_umum', 'pengurus'],
        },
        {
            title: 'Kalender Event',
            icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            href: '/events',
            color: 'bg-orange-500',
            roles: ['ketua_umum', 'pengurus', 'anggota'],
        },
        {
            title: 'Peminjaman Alat',
            icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
            href: '/borrowings',
            color: 'bg-emerald-500',
            roles: ['ketua_umum', 'pengurus', 'anggota'],
        },
        {
            title: 'KTA Digital',
            icon: 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
            href: '/kta',
            color: 'bg-amber-500',
            roles: ['ketua_umum', 'pengurus', 'anggota'],
        },
        {
            title: 'Manajemen Anggota',
            icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            href: '/members',
            color: 'bg-indigo-500',
            roles: ['ketua_umum', 'pengurus'],
        },
    ];

    return actions.filter((a) => a.roles.includes(props.userRole));
});

const attendanceRate = computed(() => {
    const total =
        (props.stats?.attendanceStats?.hadir ?? 0) +
        (props.stats?.attendanceStats?.izin ?? 0) +
        (props.stats?.attendanceStats?.sakit ?? 0) +
        (props.stats?.attendanceStats?.alpha ?? 0);
    const hadir = props.stats?.attendanceStats?.hadir ?? 0;
    return total > 0 ? Math.round((hadir / total) * 100) : 0;
});

const maxTrend = computed(() => {
    return Math.max(...trendData.value.map((t) => t.count), 1);
});

const fetchTrendData = async (period) => {
    try {
        const res = await fetch('/dashboard/trend?period=' + period);
        const json = await res.json();
        trendData.value = json.trendData || [];
        selectedPeriod.value = json.selectedPeriod || period;
    } catch (err) {
        console.error('Failed to fetch trend data', err);
    }
};

const approveBorrowing = (id) => {
    router.patch(`/borrowings/${id}/status`, { status: 'Disetujui' });
};

const periodLabels = {
    harian: 'Harian',
    mingguan: 'Mingguan',
    bulanan: 'Bulanan',
    tahunan: 'Tahunan',
};

const exportLaporan = () => {
    if (!props.stats) {
        alert('Tidak ada data untuk diexport.');
        return;
    }

    const worksheetData = [
        ['LAPORAN OPERASIONAL GWASYNC', ''],
        ['Tanggal Cetak', new Date().toLocaleDateString('id-ID', { dateStyle: 'full' })],
        ['Cetak Oleh', props.user?.name ?? 'Sistem'],
        [''],
        ['STATISTIK KEANGGOTAAN', ''],
        ['Total Anggota', props.stats.totalMembers],
        ['Anggota Aktif', props.stats.activeMembers],
        [''],
        ['STATISTIK KEHADIRAN', ''],
        ['Kehadiran (' + (periodLabels[selectedPeriod.value] ?? selectedPeriod.value) + ')', props.stats.thisPeriodAttendance],
        ['Hadir', props.stats.attendanceStats?.hadir ?? 0],
        ['Izin', props.stats.attendanceStats?.izin ?? 0],
        ['Sakit', props.stats.attendanceStats?.sakit ?? 0],
        ['Alpha', props.stats.attendanceStats?.alpha ?? 0],
        ['Tingkat Kehadiran', attendanceRate.value + '%'],
        [''],
        ['STATISTIK INVENTARIS & PINJAMAN', ''],
        ['Total Event', props.stats.totalEvents],
        ['Event Mendatang', props.stats.upcomingEvents],
        ['Alat Sedang Dipinjam', props.stats.activeBorrowings],
        ['Total Peminjaman', props.stats.totalBorrowings],
        ['Peminjaman Menunggu', props.stats.pendingBorrowings],
        ['Alat Butuh Perbaikan', props.stats.damagedInventories],
    ];

    if (props.moduleSummary) {
        worksheetData.push(
            [''],
            ['RINGKASAN MODUL ORGANISASI', ''],
            ['Saldo Keuangan', props.moduleSummary.financialBalance],
            ['Pengajuan Dana Pending', props.moduleSummary.pendingFundingRequests],
            ['Total Surat', props.moduleSummary.totalLetters],
            ['Surat Menunggu Persetujuan', props.moduleSummary.pendingLetters],
            ['Total Program Kerja', props.moduleSummary.totalPrograms],
            ['Program Kerja Aktif', props.moduleSummary.activePrograms],
            ['Rekrutmen Terbuka', props.moduleSummary.openRecruitments],
            ['Pelamar Perlu Review', props.moduleSummary.pendingApplicants]
        );
    }

    worksheetData.push([''], ['TREN KEHADIRAN (' + (periodLabels[selectedPeriod.value] ?? '') + ')', ''], ['Periode', 'Jumlah Kehadiran']);

    trendData.value?.forEach((t) => {
        worksheetData.push([t.label, t.count]);
    });

    worksheetData.push(['', '']);
    worksheetData.push(['PEMINJAMAN MENUNGGU PERSETUJUAN', '']);

    props.pendingBorrowings?.forEach((req) => {
        worksheetData.push([
            req.user?.name ?? 'Pengguna',
            req.inventory?.name ?? 'Alat #' + req.inventory_id,
            req.start_date,
            req.end_date,
            req.status,
        ]);
    });

    const ws = utils.aoa_to_sheet(worksheetData);
    const wb = utils.book_new();
    utils.book_append_sheet(wb, ws, 'Laporan Operasional');

    const fileName = `Laporan_Operasional_${new Date().toISOString().slice(0, 10)}.xlsx`;
    writeFile(wb, fileName);
};
</script>

<template>
    <Head title="Dashboard - GWASync" />

    <InternalLayout>
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Dashboard Operasional</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                        Selamat datang, {{ props.user?.name }} — Ringkasan operasional UKM Marching Band Gita Widya Agni.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button @click="exportLaporan" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        Export Laporan
                    </button>

                    <!-- Period Selector -->
                    <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                        <button
                            v-for="(label, key) in periodLabels"
                            :key="key"
                            @click="fetchTrendData(key)"
                            :class="[
                                'px-3 py-1.5 text-xs font-semibold rounded-md transition-colors',
                                selectedPeriod === key
                                    ? 'bg-blue-600 text-white shadow-sm'
                                    : 'bg-white text-gray-600 hover:bg-gray-200 border border-gray-200'
                            ]"
                        >
                            {{ label }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Operational Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Total Anggota</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.totalMembers }}</p>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-lg bg-blue-100 text-blue-600 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                    {{ stats.activeMembers }} aktif dari {{ stats.totalMembers }} total
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Kehadiran {{ periodLabels[selectedPeriod] }}</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.thisPeriodAttendance }}</p>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-lg bg-green-100 text-green-600 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-3 flex items-center gap-1 text-xs">
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-semibold">{{ attendanceRate }}%</span>
                    <span class="text-gray-400">Tingkat kehadiran</span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Alat Sedang Dipinjam</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.activeBorrowings }}</p>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-lg bg-purple-100 text-purple-600 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                    {{ stats.totalBorrowings }} total peminjaman ({{ stats.pendingBorrowings }} pending)
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">Alat Butuh Perbaikan</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.damagedInventories }}</p>
                    </div>
                    <div class="p-2 sm:p-2.5 rounded-lg bg-red-100 text-red-600 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-2 sm:mt-3 text-xs text-gray-400">
                    {{ stats.totalEvents }} event, {{ stats.upcomingEvents }} mendatang
                </div>
            </div>
        </div>

        <!-- Ringkasan Modul Baru (Management only) -->
        <div v-if="isManagement && moduleSummary" class="mb-6 sm:mb-8">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ringkasan Modul Organisasi</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <Link href="/financial-transactions" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-emerald-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Keuangan</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900 mt-1 truncate">{{ formatRupiah(moduleSummary.financialBalance) }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-emerald-100 text-emerald-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        {{ moduleSummary.pendingFundingRequests }} pengajuan dana menunggu
                    </div>
                </Link>

                <Link href="/letters" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-sky-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Persuratan</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ moduleSummary.totalLetters }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-sky-100 text-sky-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        {{ moduleSummary.pendingLetters }} surat menunggu proses
                    </div>
                </Link>

                <Link href="/programs" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-amber-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Program Kerja</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ moduleSummary.totalPrograms }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-amber-100 text-amber-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        {{ moduleSummary.activePrograms }} program aktif / disetujui
                    </div>
                </Link>

                <Link href="/recruitment-applicants" class="block bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:border-indigo-300 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pelamar Rekrutmen</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ moduleSummary.pendingApplicants }}</p>
                        </div>
                        <div class="p-2 rounded-lg bg-indigo-100 text-indigo-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-400">
                        {{ moduleSummary.openRecruitments }} gelombang rekrutmen terbuka
                    </div>
                </Link>
            </div>
        </div>

        <!-- Quick Actions & Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6 lg:col-span-1">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-4 sm:mb-6">Aksi Cepat</h3>
                <div class="space-y-3">
                    <Link
                        v-for="action in quickActions"
                        :key="action.title"
                        :href="action.href"
                        class="flex items-center gap-3 p-3 sm:p-3.5 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-gray-100 hover:border-blue-200 transition-colors group"
                    >
                        <div :class="action.color + ' p-1.5 rounded-lg shrink-0'">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path :d="action.icon" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm sm:text-base font-semibold text-gray-900 group-hover:text-gray-800">{{ action.title }}</p>
                            <p class="text-xs text-gray-500 group-hover:text-gray-600">Buka halaman {{ action.title }}</p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Attendance Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6 lg:col-span-2 flex flex-col">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-4 sm:mb-6">Tren Kehadiran ({{ periodLabels[selectedPeriod] }})</h3>
                <div class="h-52 sm:h-64 flex items-end gap-2 sm:gap-3 pb-2 flex-1">
                    <div
                        v-for="(item, index) in trendData"
                        :key="index"
                        class="flex-1 h-full flex flex-col items-center justify-end gap-1 min-w-0"
                    >
                        <span class="text-xs font-medium text-gray-600">{{ item.count }}</span>
                        <div class="w-full flex-1 flex items-end">
                            <div
                                :style="{ height: item.count > 0 ? Math.max((item.count / maxTrend) * 100, 6) + '%' : '4px' }"
                                :class="item.count > 0 ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-200'"
                                class="w-full rounded-t-md transition-all duration-300"
                            ></div>
                        </div>
                        <span class="text-[10px] sm:text-xs text-gray-400 uppercase truncate max-w-full">{{ item.label }}</span>
                    </div>
                </div>
                <div class="flex justify-between text-[10px] sm:text-xs text-gray-500 mt-2" v-if="trendData.length > 0">
                    <span>{{ trendData[0]?.label }}</span>
                    <span>{{ trendData[trendData.length - 1]?.label }}</span>
                </div>
            </div>
        </div>

        <!-- Pending Approvals & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Approvals (Management only) -->
            <div v-if="isManagement" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900">Peminjaman Menunggu Persetujuan</h3>
                    <Link href="/borrowings" class="text-xs sm:text-sm text-blue-600 hover:underline font-medium">Lihat Semua &rarr;</Link>
                </div>

                <div v-if="pendingBorrowings && pendingBorrowings.length" class="space-y-3">
                    <div v-for="req in pendingBorrowings" :key="req.id" class="p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ req.user?.name ?? 'Pengguna' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ req.inventory?.name ?? 'Alat #' + req.inventory_id }} &bull; {{ req.start_date }} s/d {{ req.end_date }}</p>
                            </div>
                            <button
                                @click="approveBorrowing(req.id)"
                                class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition shadow-xs"
                            >
                                Setujui
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="!pendingBorrowings || !pendingBorrowings.length" class="text-center py-8 text-xs sm:text-sm text-gray-500">
                    Tidak ada pengajuan peminjaman baru saat ini.
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
                <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-4 sm:mb-6">Aktivitas Terbaru</h3>

                <div class="space-y-4">
                    <div v-if="recentBorrowings && recentBorrowings.length" class="mb-6">
                        <h4 class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase mb-3">Peminjaman Terbaru</h4>
                        <div class="space-y-2">
                            <div v-for="b in recentBorrowings" :key="b.id" class="flex items-start gap-3 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ b.user?.name ?? 'Pengguna' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ b.inventory?.name ?? 'Alat #' + b.inventory_id }} — {{ b.status }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="recentAttendances && recentAttendances.length">
                        <h4 class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase mb-3">Presensi Terbaru</h4>
                        <div class="space-y-2">
                            <div v-for="a in recentAttendances" :key="a.id" class="flex items-start gap-3 p-2.5 rounded-lg bg-gray-50 border border-gray-100">
                                <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900">{{ a.user?.name ?? 'Anggota' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ a.event?.title ?? 'Event' }} — {{ a.status }}</p>
                                </div>
                                <span class="text-xs text-gray-400 mt-0.5">{{ a.scan_time ? new Date(a.scan_time).toLocaleDateString('id-ID', { dateStyle: 'medium' }) : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="(!recentBorrowings || !recentBorrowings.length) && (!recentAttendances || !recentAttendances.length)" class="text-center py-8 text-xs sm:text-sm text-gray-500">
                    Belum ada aktivitas terbaru.
                </div>
            </div>
        </div>
    </InternalLayout>
</template>
