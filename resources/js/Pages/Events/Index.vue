<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    events: Array,
    userRole: String
});

// ========== View Mode State ==========
const viewMode = ref('list'); // 'list' | 'calendar'

// ========== Modal State ==========
const showModal = ref(false);                 // Tambah Event
const showEditModal = ref(false);             // Edit Event
const showDetailModal = ref(false);           // Detail Event + Daftar Kehadiran

// ========== Detail Event State ==========
const detailEvent = ref(null);
const detailAttendances = ref([]);
const detailStats = ref({
    total: 0, hadir: 0, izin: 0, sakit: 0, alpha: 0
});

// ========== Calendar State ==========
const currentDate = ref(new Date());
const calendarYear = ref(new Date().getFullYear());
const calendarMonth = ref(new Date().getMonth());

const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// ========== Helpers ==========
const getTypeBadge = (type) => {
    switch (type) {
        case 'Latihan': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'TC': return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'Gladi': return 'bg-orange-100 text-orange-700 border-orange-200';
        case 'Kejuaraan': return 'bg-red-100 text-red-700 border-red-200';
        case 'Rapat': return 'bg-amber-100 text-amber-700 border-amber-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'Hadir': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'Izin': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'Sakit': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'Alpha': return 'bg-rose-100 text-rose-800 border-rose-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
};

const formatDateTime = (datetime) => {
    if (!datetime) return '-';
    return new Date(datetime).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
};

const formatDate = (datetime) => {
    if (!datetime) return '-';
    return new Date(datetime).toLocaleDateString('id-ID', { dateStyle: 'full' });
};

const isExpired = (event) => {
    if (!event.expired_at) return false;
    return new Date() > new Date(event.expired_at);
};

// Get events for a specific date (calendar view)
const eventsForDay = (year, month, day) => {
    if (!props.events) return [];
    const targetDate = new Date(year, month, day);
    targetDate.setHours(0, 0, 0, 0);
    return (props.events || []).filter(ev => {
        if (!ev.start_time) return false;
        const evDate = new Date(ev.start_time);
        evDate.setHours(0, 0, 0, 0);
        return evDate.getTime() === targetDate.getTime();
    });
};

// Get days in month calendar grid
const calendarDays = computed(() => {
    const year = calendarYear.value;
    const month = calendarMonth.value;
    const firstDay = new Date(year, month, 1).getDay(); // 0 = Sunday
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const prevMonthDays = new Date(year, month, 0).getDate();

    const days = [];

    // Previous month trailing days
    for (let i = firstDay - 1; i >= 0; i--) {
        days.push({
            day: prevMonthDays - i,
            month: month - 1,
            year: year,
            isCurrentMonth: false,
            events: eventsForDay(year, month - 1, prevMonthDays - i)
        });
    }

    // Current month days
    for (let i = 1; i <= daysInMonth; i++) {
        days.push({
            day: i,
            month: month,
            year: year,
            isCurrentMonth: true,
            events: eventsForDay(year, month, i),
            isToday: i === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear()
        });
    }

    // Next month leading days
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remaining = totalCells - days.length;
    for (let i = 1; i <= remaining; i++) {
        days.push({
            day: i,
            month: month + 1,
            year: year,
            isCurrentMonth: false,
            events: eventsForDay(year, month + 1, i)
        });
    }

    return days;
});

// ========== Event Detail ==========
const openDetail = async (event) => {
    detailEvent.value = event;
    detailAttendances.value = [];
    detailStats.value = { total: 0, hadir: 0, izin: 0, sakit: 0, alpha: 0 };

    try {
        const res = await fetch(route('events.attendances', event.id));
        const json = await res.json();
        detailEvent.value = json.event;
        detailAttendances.value = json.attendances;
        detailStats.value = json.stats;
    } catch (err) {
        console.error('Failed to load attendance detail', err);
    }

    showDetailModal.value = true;
};

const closeDetail = () => {
    showDetailModal.value = false;
    detailEvent.value = null;
    detailAttendances.value = [];
};

// ========== CRUD Event ==========
const form = useForm({
    title: '', description: '', type: 'Latihan',
    start_time: '', end_time: '', location: ''
});

const editForm = useForm({
    id: null, title: '', description: '', type: 'Latihan',
    start_time: '', end_time: '', location: ''
});

const submit = () => {
    form.post('/events', {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        }
    });
};

const openEdit = (event) => {
    editForm.id = event.id;
    editForm.title = event.title;
    editForm.description = event.description || '';
    editForm.type = event.type;
    editForm.start_time = event.start_time ? event.start_time.slice(0, 16) : '';
    editForm.end_time = event.end_time ? event.end_time.slice(0, 16) : '';
    editForm.location = event.location || '';
    showEditModal.value = true;
};

const update = () => {
    editForm.put(`/events/${editForm.id}`, {
        onSuccess: () => {
            showEditModal.value = false;
            editForm.reset();
        }
    });
};

const deleteEvent = (id) => {
    if (confirm('Apakah kamu yakin ingin menghapus agenda ini?')) {
        useForm().delete(`/events/${id}`);
    }
};

// Calendar navigation
const prevMonth = () => {
    calendarMonth.value = calendarMonth.value === 0 ? 11 : calendarMonth.value - 1;
    if (calendarMonth.value === 11) calendarYear.value--;
};

const nextMonth = () => {
    calendarMonth.value = calendarMonth.value === 11 ? 0 : calendarMonth.value + 1;
    if (calendarMonth.value === 0) calendarYear.value++;
};

const today = () => {
    calendarYear.value = new Date().getFullYear();
    calendarMonth.value = new Date().getMonth();
};
</script>

<template>
    <Head title="Kalender Event & Agenda" />

    <InternalLayout>
        <!-- ===== Header: Judul + Toggle View + Tombol Tambah ===== -->
        <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50 p-4 sm:p-6 rounded-xl border border-gray-100">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900">Kalender Event & Agenda</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Lihat jadwal kegiatan dan daftar kehadiran anggota.</p>
                </div>

                <!-- View Toggle -->
                <div class="flex items-center gap-2 bg-gray-200 rounded-lg p-1">
                    <button
                        @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-white text-gray-900 font-medium shadow-sm' : 'text-gray-600 hover:bg-gray-300'"
                        class="px-3 py-1.5 text-xs rounded transition-colors"
                    >
                        Daftar
                    </button>
                    <button
                        @click="viewMode = 'calendar'"
                        :class="viewMode === 'calendar' ? 'bg-white text-gray-900 font-medium shadow-sm' : 'text-gray-600 hover:bg-gray-300'"
                        class="px-3 py-1.5 text-xs rounded transition-colors"
                    >
                        Kalender
                    </button>
                </div>

                <!-- Tambah Agenda -->
                <div v-if="['ketua_umum', 'pengurus'].includes(userRole)">
                    <button 
                        @click="showModal = true"
                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors text-center shadow-xs"
                    >
                        + Tambah Agenda
                    </button>
                </div>
            </div>
        </div>

        <!-- ===== LIST VIEW (Kardus) ===== -->
        <div v-if="viewMode === 'list'" class="space-y-4">
            <div class="p-3.5 sm:p-6">
                <div v-if="!events || events.length === 0" class="text-center py-12 text-gray-500 text-sm">
                    Belum ada agenda kegiatan yang dijadwalkan.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
                    <div 
                        v-for="item in events" 
                        :key="item.id"
                        class="p-5 rounded-xl border hover:bg-white hover:shadow-md transition-all flex flex-col justify-between cursor-pointer group"
                        :class="isExpired(item) ? 'border-rose-200 bg-rose-50/50 opacity-70 grayscale-[0.3]' : 'border-gray-100 bg-gray-50/50'"
                        @click="openDetail(item)"
                    >
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getTypeBadge(item.type)" class="px-2.5 py-1 rounded-full text-xs font-semibold border">
                                    {{ item.type }}
                                </span>
                                <span v-if="isExpired(item)" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-rose-300 bg-rose-100 text-rose-700">
                                    Kadaluarsa
                                </span>
                                <div v-if="['ketua_umum', 'pengurus'].includes(userRole)" class="flex gap-1">
                                    <button @click.stop="openEdit(item)" class="text-blue-500 hover:text-blue-700 transition-colors p-1.5 rounded hover:bg-blue-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click.stop="deleteEvent(item.id)" class="text-red-500 hover:text-red-700 transition-colors p-1.5 rounded hover:bg-red-50" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <h4 class="font-bold text-gray-900 text-base mb-1 truncate">{{ item.title }}</h4>
                            <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ item.description || 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="space-y-1.5 pt-3 border-t border-gray-100 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ formatDateTime(item.start_time) }}</span>
                            </div>
                            <div class="flex items-center gap-2" v-if="item.location">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="line-clamp-1">{{ item.location }}</span>
                            </div>
                            <div v-if="item.expired_at" class="flex items-center gap-2 text-rose-500">
                                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-xs font-medium">Berakhir: {{ formatDateTime(item.expired_at) }}</span>
                            </div>
                        </div>

                        <p class="mt-2 text-[10px] text-gray-400 text-center">Klik untuk detail & daftar kehadiran</p>

                        <!-- Tombol Buka Presensi -->
                        <button
                            v-if="['ketua_umum', 'pengurus'].includes(userRole)"
                            @click.stop="router.get('/attendances', { event_id: item.id })"
                            class="w-full mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 001 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 001 1v2a1 1 0 001 1z" />
                            </svg>
                            Buka Presensi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CALENDAR VIEW (Grid Bulanan) ===== -->
        <div v-else class="space-y-4">
            <!-- Calendar Header -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Periode Kalender</p>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-sm font-semibold text-gray-900">
                            <span class="rounded-md bg-blue-50 px-2 py-1 text-blue-800">Bulan: {{ monthNames[calendarMonth.value] }}</span>
                            <span class="rounded-md bg-gray-100 px-2 py-1 text-gray-700">Tahun: {{ calendarYear.value }}</span>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button @click="prevMonth" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Bulan Sebelumnya">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button @click="today" class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors font-medium text-sm" title="Hari Ini">
                            Hari Ini
                        </button>
                        <button @click="nextMonth" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Bulan Selanjutnya">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button
                            v-if="['ketua_umum', 'pengurus'].includes(userRole)"
                            @click="showModal = true"
                            class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium"
                        >
                            + Agenda
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <!-- Weekday Headers -->
                <div class="grid grid-cols-7 border-b border-gray-100 bg-gray-50">
                    <div v-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']" :key="day" class="py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                        {{ day }}
                    </div>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7">
                    <div 
                        v-for="(cell, index) in calendarDays" 
                        :key="index"
                        class="aspect-[1.2] border-r border-b border-gray-100 p-2 sm:p-3 relative"
                        :class="[
                            !cell.isCurrentMonth ? 'bg-gray-50/50 text-gray-400' : 'bg-white',
                            cell.isToday ? 'bg-blue-50 ring-1 ring-blue-300' : '',
                            cell.events.length > 0 ? 'has-events' : ''
                        ]"
                        @click="cell.events.length > 0 ? openDetail(cell.events[0]) : null"
                        :style="cell.isToday ? 'cursor: pointer' : cell.events.length > 0 ? 'cursor: pointer' : 'cursor: default'"
                    >
                        <div class="flex flex-col h-full">
                            <!-- Day Number -->
                            <div class="flex justify-between items-start mb-1">
                                <span 
                                    class="text-xs sm:text-sm font-medium"
                                    :class="[
                                        !cell.isCurrentMonth ? 'text-gray-400' : 'text-gray-900',
                                        cell.isToday ? 'text-blue-600 font-bold' : ''
                                    ]"
                                >
                                    {{ cell.day }}
                                </span>
                                <span v-if="cell.events.length > 0" class="text-[10px] font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">
                                    {{ cell.events.length }}
                                </span>
                            </div>

                            <!-- Events List -->
                            <div v-if="cell.events.length > 0" class="flex-1 overflow-hidden">
                                <div 
                                    v-for="(ev, idx) in cell.events" 
                                    :key="ev.id"
                                    class="mt-1 flex items-center gap-0.5 rounded text-[9px] sm:text-xs font-medium text-white leading-tight"
                                    :class="[
                                        ev.type === 'Latihan' ? 'bg-blue-600' : 
                                        ev.type === 'TC' ? 'bg-purple-600' :
                                        ev.type === 'Gladi' ? 'bg-orange-600' :
                                        ev.type === 'Kejuaraan' ? 'bg-red-600' :
                                        ev.type === 'Rapat' ? 'bg-amber-600' : 'bg-gray-600'
                                    ]"
                                    :title="ev.title"
                                >
                                    <button
                                        type="button"
                                        @click.stop="openDetail(ev)"
                                        class="min-w-0 flex-1 truncate px-1.5 py-0.5 text-left"
                                        :title="`Lihat detail ${ev.title}`"
                                    >
                                        {{ ev.title }}
                                    </button>
                                    <div v-if="['ketua_umum', 'pengurus'].includes(userRole)" class="flex shrink-0 items-center pr-0.5">
                                        <button
                                            type="button"
                                            @click.stop="openEdit(ev)"
                                            class="rounded p-0.5 hover:bg-white/20"
                                            :aria-label="`Edit ${ev.title}`"
                                            title="Edit agenda"
                                        >
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click.stop="deleteEvent(ev.id)"
                                            class="rounded p-0.5 hover:bg-white/20"
                                            :aria-label="`Hapus ${ev.title}`"
                                            title="Hapus agenda"
                                        >
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="cell.events.length > 3" class="mt-1 px-1.5 py-0.5 bg-gray-100 text-[9px] text-gray-500 font-medium truncate">
                                    +{{ cell.events.length - 3 }} lagi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Modal Tambah Event ===== -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Agenda Baru</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kegiatan</label>
                        <input name="title" v-model="form.title" type="text" placeholder="Contoh: Latihan Rutin Brass & Battery" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kegiatan</label>
                        <select name="type" v-model="form.type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Latihan">Latihan</option>
                            <option value="TC">TC (Training Center)</option>
                            <option value="Gladi">Gladi</option>
                            <option value="Kejuaraan">Kejuaraan / Perform</option>
                            <option value="Rapat">Rapat Pengurus</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                            <input name="start_time" v-model="form.start_time" type="datetime-local" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
                            <input name="end_time" v-model="form.end_time" type="datetime-local" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input name="location" v-model="form.location" type="text" placeholder="Contoh: Lapangan Utama / Sanggar MBGWA" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" v-model="form.description" rows="3" placeholder="Catatan tambahan agenda..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">Simpan Agenda</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===== Modal Edit Event ===== -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Agenda</h3>
                <form @submit.prevent="update" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kegiatan</label>
                        <input name="title" v-model="editForm.title" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kegiatan</label>
                        <select name="type" v-model="editForm.type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Latihan">Latihan</option>
                            <option value="TC">TC (Training Center)</option>
                            <option value="Gladi">Gladi</option>
                            <option value="Kejuaraan">Kejuaraan / Perform</option>
                            <option value="Rapat">Rapat Pengurus</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                            <input name="start_time" v-model="editForm.start_time" type="datetime-local" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
                            <input name="end_time" v-model="editForm.end_time" type="datetime-local" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input name="location" v-model="editForm.location" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" v-model="editForm.description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="editForm.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===== Modal Detail Event + Daftar Kehadiran ===== -->
        <div v-if="showDetailModal && detailEvent" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 overflow-y-auto">
            <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[95vh]">
                <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">{{ detailEvent.title }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span v-if="isExpired(detailEvent)" class="px-2 py-0.5 rounded text-[10px] font-bold border border-rose-300 bg-rose-100 text-rose-700 uppercase">
                                Kadaluarsa
                            </span>
                            <span v-else class="px-2 py-0.5 rounded text-[10px] font-bold border border-emerald-300 bg-emerald-100 text-emerald-700 uppercase">
                                Aktif
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">{{ formatDateTime(detailEvent.start_time) }} &bull; {{ detailEvent.location || '-' }}</p>
                        <p v-if="detailEvent.expired_at" class="text-xs text-rose-600 mt-0.5 font-medium">Event kadaluarsa pada: {{ formatDateTime(detailEvent.expired_at) }}</p>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <button @click="closeDetail" class="px-3 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Tutup</button>
                    </div>
                </div>

                <div class="p-5 overflow-y-auto flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Kolom Kiri: Info Event -->
                    <div class="lg:col-span-4 space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-900 mb-3">Detail Agenda</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between"><span class="font-medium">Judul:</span> <span>{{ detailEvent.title }}</span></div>
                                <div class="flex justify-between"><span class="font-medium">Tipe:</span> <span :class="getTypeBadge(detailEvent.type)" class="px-2 py-0.5 rounded text-[10px] font-bold border">{{ detailEvent.type }}</span></div>
                                <div class="flex justify-between"><span class="font-medium">Waktu:</span> <span>{{ formatDateTime(detailEvent.start_time) }} s.d. {{ formatDateTime(detailEvent.end_time) }}</span></div>
                                <div class="flex justify-between"><span class="font-medium">Lokasi:</span> <span>{{ detailEvent.location || '-' }}</span></div>
                                <div class="flex justify-between"><span class="font-medium">Deskripsi:</span> <span class="line-clamp-2">{{ detailEvent.description || '-' }}</span></div>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <h4 class="text-sm font-bold text-gray-900 mb-3">Statistik Kehadiran</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="bg-gray-50 p-2 rounded border border-gray-100">
                                    <p class="text-[10px] text-gray-500 font-medium uppercase">Total</p>
                                    <p class="font-bold text-gray-900">{{ detailStats.total }}</p>
                                </div>
                                <div class="bg-emerald-50 p-2 rounded border border-emerald-100">
                                    <p class="text-[10px] text-emerald-600 font-medium uppercase">Hadir</p>
                                    <p class="font-bold text-emerald-700">{{ detailStats.hadir }}</p>
                                </div>
                                <div class="bg-blue-50 p-2 rounded border border-blue-100">
                                    <p class="text-[10px] text-blue-600 font-medium uppercase">Izin</p>
                                    <p class="font-bold text-blue-700">{{ detailStats.izin }}</p>
                                </div>
                                <div class="bg-amber-50 p-2 rounded border border-amber-100">
                                    <p class="text-[10px] text-amber-600 font-medium uppercase">Sakit</p>
                                    <p class="font-bold text-amber-700">{{ detailStats.sakit }}</p>
                                </div>
                                <div class="bg-rose-50 p-2 rounded border border-rose-100">
                                    <p class="text-[10px] text-rose-600 font-medium uppercase">Alpha</p>
                                    <p class="font-bold text-rose-700">{{ detailStats.alpha }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Daftar Kehadiran -->
                    <div class="lg:col-span-8 space-y-4">
                        <!-- Daftar Kehadiran -->
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                <h4 class="text-sm font-bold text-gray-900">Daftar Kehadiran</h4>
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ detailAttendances.length }} Orang</span>
                            </div>

                            <div v-if="!detailAttendances.length" class="p-8 text-center text-gray-500 text-sm">
                                Belum ada data kehadiran.
                            </div>

                            <table v-else class="w-full text-left text-xs">
                                <thead class="bg-gray-50 text-gray-600 uppercase border-b">
                                    <tr>
                                        <th class="py-3 px-4 w-5">No</th>
                                        <th class="py-3 px-4">Nama</th>
                                        <th class="py-3 px-4 w-20">NPM</th>
                                        <th class="py-3 px-4 w-24">Status</th>
                                        <th class="py-3 px-4 w-40">Keterangan</th>
                                        <th class="py-3 px-4 w-32 text-right">Waktu Scan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="(att, i) in detailAttendances" :key="att.id">
                                        <td class="py-3 px-4 text-gray-400 font-mono">{{ i + 1 }}</td>
                                        <td class="py-3 px-4 font-semibold text-gray-900">{{ att.user?.name ?? '-' }}</td>
                                        <td class="py-3 px-4 text-gray-600">{{ att.user?.nim ?? '-' }}</td>
                                        <td class="py-3 px-4">
                                            <span :class="getStatusBadge(att.status)" class="px-2 py-0.5 rounded text-[10px] font-semibold border">
                                                {{ att.status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600">{{ att.keterangan ?? '-' }}</td>
                                        <td class="py-3 px-4 text-right text-gray-500">
                                            {{ att.scan_time ? formatDateTime(att.scan_time) : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>

<style>
/* Tidak ada style tambahan */
</style>
