<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, watch, nextTick, onUnmounted } from 'vue';
import QRCode from 'qrcode';
import { writeFile, utils } from 'xlsx';
import { Html5Qrcode } from 'html5-qrcode';

const props = defineProps({
    events: Array,
    selectedEvent: Object,
    attendances: Array,
    stats: Object,
    members: Array,
    userRole: String
});

const selectedEventId = ref(props.selectedEvent?.id || '');
const qrDataUrl = ref('');
const showQrModal = ref(false);
const showManualModal = ref(false);
const showScanInputModal = ref(false);
const showCameraModal = ref(false);
const showFullScreenQr = ref(false);
const showSelfAttendModal = ref(false); // Hanya untuk anggota

let html5QrcodeInstance = null;

const scanForm = useForm({
    qr_token: '',
});

const manualForm = useForm({
    event_id: props.selectedEvent?.id || '',
    user_id: '',
    status: 'Hadir',
});

// Self-attendance form (untuk anggota)
const selfAttendForm = useForm({
    event_id: props.selectedEvent?.id || '',
    status: 'Hadir',
    qr_token: '',
    keterangan: '',
});

const generateQrImage = async () => {
    if (props.selectedEvent?.qr_token) {
        try {
            qrDataUrl.value = await QRCode.toDataURL(props.selectedEvent.qr_token, {
                width: 400,
                margin: 2,
                color: {
                    dark: '#0f172a',
                    light: '#ffffff',
                },
            });
        } catch (err) {
            console.error('Failed to generate QR code', err);
        }
    }
};

onMounted(() => {
    generateQrImage();
});

watch(() => props.selectedEvent, () => {
    generateQrImage();
    if (props.selectedEvent) {
        manualForm.event_id = props.selectedEvent.id;
        selfAttendForm.event_id = props.selectedEvent.id;
    }
});

watch(() => selfAttendForm.status, (newStatus) => {
    if (newStatus !== 'Izin' && newStatus !== 'Terlambat') {
        selfAttendForm.keterangan = '';
    }
});

onUnmounted(() => {
    stopQrCamera();
});

const changeEvent = (id) => {
    router.get('/attendances', { event_id: id }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const submitManualAttendance = () => {
    manualForm.post('/attendances', {
        onSuccess: () => {
            showManualModal.value = false;
            manualForm.reset('user_id');
        },
    });
};

const submitScanToken = async () => {
    try {
        const res = await fetch('/attendances/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                qr_token: scanForm.qr_token,
            }),
        });

        const json = await res.json();

        if (res.ok) {
            alert(json.message || 'Presensi berhasil dicatat.');
            showScanInputModal.value = false;
            scanForm.reset();
            window.location.reload();
        } else {
            alert(json.message || 'Token QR tidak valid.');
        }
    } catch (err) {
        alert('Terjadi kesalahan saat verifikasi presensi.');
    }
};

const submitSelfAttend = async () => {
    try {
        const res = await fetch(route('events.selfAttend', props.selectedEvent.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                status: selfAttendForm.status,
                qr_token: selfAttendForm.qr_token,
                keterangan: selfAttendForm.keterangan,
            }),
        });

        const json = await res.json();

        if (res.ok) {
            alert(json.message || 'Presensi berhasil dicatat.');
            showSelfAttendModal.value = false;
            selfAttendForm.reset();
            selfAttendForm.status = 'Hadir';
            selfAttendForm.qr_token = '';
            selfAttendForm.keterangan = '';
            window.location.reload();
        } else {
            alert(json.message || 'Gagal mencatat presensi.');
        }
    } catch (err) {
        alert('Terjadi kesalahan saat mengirim presensi.');
    }
};

const updateStatus = (attendanceId, newStatus) => {
    useForm({ status: newStatus }).patch(`/attendances/${attendanceId}`);
};

const populateMembers = () => {
    if (confirm('Inisialisasi semua anggota aktif ke sesi agenda ini dengan status Alpha?')) {
        useForm().post(`/attendances/events/${props.selectedEvent.id}/populate`);
    }
};

const refreshToken = () => {
    if (confirm('Perbarui QR Token acara ini? QR Code lama tidak akan berlaku lagi.')) {
        useForm().post(`/attendances/events/${props.selectedEvent.id}/token`, {
            onSuccess: () => generateQrImage(),
        });
    }
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'Hadir': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        case 'Izin': return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'Sakit': return 'bg-amber-100 text-amber-800 border-amber-200';
        default: return 'bg-rose-100 text-rose-800 border-rose-200';
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

const exportToExcel = () => {
    if (!props.selectedEvent || !props.attendances?.length) {
        alert('Tidak ada data absensi untuk diexport.');
        return;
    }

    const worksheetData = [
        ['REKAP ABSENSI', ''],
        ['Event', props.selectedEvent.title],
        ['Tipe', props.selectedEvent.type],
        ['Tanggal', formatDate(props.selectedEvent.start_time)],
        ['Lokasi', props.selectedEvent.location || '-'],
        [''],
        ['Statistik', ''],
        ['Total', props.stats.total],
        ['Hadir', props.stats.hadir],
        ['Izin', props.stats.izin],
        ['Sakit', props.stats.sakit],
        ['Alpha', props.stats.alpha],
        [''],
        ['No', 'Nama', 'NPM', 'Divisi', 'Status', 'Keterangan', 'Waktu Scan'],
    ];

    props.attendances.forEach((att, i) => {
        worksheetData.push([
            i + 1,
            att.user?.name ?? '-',
            att.user?.nim ?? '-',
            att.user?.division ?? '-',
            att.status,
            att.keterangan ?? '-',
            att.scan_time ? formatDateTime(att.scan_time) : '-',
        ]);
    });

    const ws = utils.aoa_to_sheet(worksheetData);
    const wb = utils.book_new();
    utils.book_append_sheet(wb, ws, 'Rekap Absensi');

    const fileName = `Rekap_Absensi_${props.selectedEvent.title.replace(/\s+/g, '_')}.xlsx`;
    writeFile(wb, fileName);
};

// ========== QR Camera Scan ==========
const startQrCamera = async () => {
    showCameraModal.value = true;
    await nextTick();
    try {
        html5QrcodeInstance = new Html5Qrcode('qr-reader-attendance');
        await html5QrcodeInstance.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                scanForm.qr_token = decodedText;
                stopQrCamera();
                submitScanToken();
            },
            (errorMessage) => {
                // ignore scan errors (continuous scanning)
            }
        );
    } catch (err) {
        alert('Gagal membuka kamera. Pastikan kamera diizinkan dan gunakan HTTPS/localhost.');
        showCameraModal.value = false;
    }
};

const stopQrCamera = () => {
    if (html5QrcodeInstance) {
        html5QrcodeInstance.stop().then(() => {
            html5QrcodeInstance.clear();
        }).catch(() => {});
        html5QrcodeInstance = null;
    }
    showCameraModal.value = false;
};
</script>

<template>
    <Head title="Presensi & Absensi QR" />

    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-5xl mx-auto">
            <!-- ===== Header: Info Event + Pilih Agenda ===== -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">{{ props.selectedEvent?.title || 'Pilih Agenda' }}</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                            {{ props.selectedEvent ? (props.selectedEvent.location || '-') + ' • ' + new Date(props.selectedEvent.start_time).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '' }}
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-end gap-2 w-full sm:w-auto">
                        <div class="w-full sm:min-w-[200px]">
                            <select
                                v-model="selectedEventId"
                                @change="changeEvent(selectedEventId)"
                                class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2 bg-gray-50"
                            >
                                <option v-if="!events || !events.length" disabled value="">Belum ada agenda</option>
                                <option v-for="ev in events" :key="ev.id" :value="ev.id">
                                    {{ ev.title }} ({{ ev.type }})
                                </option>
                            </select>
                        </div>

                        <!-- Aksi Pengurus -->
                        <div v-if="['ketua_umum', 'pengurus'].includes(userRole) && props.selectedEvent" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <button
                                @click="showFullScreenQr = true"
                                class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 001 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 001 1v2a1 1 0 001 1z" />
                                </svg>
                                QR Layar Penuh
                            </button>
                            <button
                                @click="refreshToken"
                                class="w-full sm:w-auto px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Token Baru
                            </button>
                        </div>

                        <!-- Aksi Anggota: Scan Kamera & Input Token -->
                        <div v-if="userRole === 'anggota' && props.selectedEvent" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                            <button
                                @click="startQrCamera"
                                class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Scan Kamera
                            </button>
                            <button
                                @click="showScanInputModal = true"
                                class="w-full sm:w-auto px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition-colors text-center"
                            >
                                Input Token
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="!props.selectedEvent" class="bg-white p-8 sm:p-12 text-center rounded-xl border border-gray-100 text-gray-500 text-sm">
                <p class="font-medium mb-2">Belum ada agenda dipilih.</p>
                <p class="text-xs">Silakan pilih agenda dari dropdown di atas, atau buat agenda baru di menu <strong>Kalender Event</strong>.</p>
            </div>

            <div v-else class="space-y-4 sm:space-y-6">
                <!-- ===== Form Presensi Sendiri (Hanya Anggota) ===== -->
                <div v-if="userRole === 'anggota'" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 sm:p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">
                            ?
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Presensi Saya</h3>
                    </div>

                    <form @submit.prevent="submitSelfAttend" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Status Kehadiran</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <button
                                    type="button"
                                    @click="() => { selfAttendForm.status = 'Hadir'; selfAttendForm.qr_token = ''; selfAttendForm.keterangan = ''; }"
                                    :class="selfAttendForm.status === 'Hadir' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'"
                                    class="px-3 py-3 rounded-lg text-sm font-medium transition-all flex flex-col items-center gap-1.5"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Hadir</span>
                                </button>
                                <button
                                    type="button"
                                    @click="selfAttendForm.status = 'Izin'"
                                    :class="selfAttendForm.status === 'Izin' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'"
                                    class="px-3 py-3 rounded-lg text-sm font-medium transition-all flex flex-col items-center gap-1.5"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    <span>Izin</span>
                                </button>
                                <button
                                    type="button"
                                    @click="selfAttendForm.status = 'Terlambat'"
                                    :class="selfAttendForm.status === 'Terlambat' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'"
                                    class="px-3 py-3 rounded-lg text-sm font-medium transition-all flex flex-col items-center gap-1.5"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Terlambat</span>
                                </button>
                            </div>
                        </div>

                        <!-- QR Token (wajib untuk Hadir) -->
                        <div v-if="selfAttendForm.status === 'Hadir'" class="space-y-2 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <label class="block text-xs font-semibold text-blue-800 mb-1">QR Token</label>
                            <input
                                v-model="selfAttendForm.qr_token"
                                type="text"
                                placeholder="Masukkan token dari QR Code..."
                                class="w-full border rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            />
                            <p class="text-[10px] text-blue-600 mt-1">Masukkan 32 karakter token yang ditampilkan pada layar QR presensi.</p>
                        </div>

                        <!-- Keterangan (hanya untuk Izin dan Terlambat) -->
                        <div v-if="selfAttendForm.status === 'Izin' || selfAttendForm.status === 'Terlambat'" class="space-y-2 p-4 bg-amber-50 rounded-lg border border-amber-100">
                            <label class="block text-xs font-semibold text-amber-800 mb-1">Alasan / Keterangan</label>
                            <textarea
                                v-model="selfAttendForm.keterangan"
                                rows="3"
                                placeholder="Masukkan alasan kehadiran Anda..."
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none"
                            ></textarea>
                            <p class="text-[10px] text-amber-600 mt-1">Silakan jelaskan alasan Anda {{ selfAttendForm.status === 'Izin' ? 'tidak hadir' : 'terlambat' }}.</p>
                        </div>

                        <div class="mt-6">
                            <button
                                type="submit"
                                :disabled="selfAttendForm.processing"
                                class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm flex justify-center items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Presensi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ===== Statistik Kehadiran (Hanya Pengurus) ===== -->
                <div v-if="['ketua_umum', 'pengurus'].includes(userRole)" class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-4">
                    <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-100 shadow-xs">
                        <span class="text-[11px] text-gray-500 font-medium">Total Anggota</span>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-0.5">{{ stats.total }}</p>
                    </div>
                    <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-100 shadow-xs">
                        <span class="text-[11px] text-emerald-600 font-medium">Hadir</span>
                        <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-0.5">{{ stats.hadir }}</p>
                    </div>
                    <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-100 shadow-xs">
                        <span class="text-[11px] text-blue-600 font-medium">Izin</span>
                        <p class="text-xl sm:text-2xl font-bold text-blue-600 mt-0.5">{{ stats.izin }}</p>
                    </div>
                    <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-100 shadow-xs">
                        <span class="text-[11px] text-amber-600 font-medium">Sakit</span>
                        <p class="text-xl sm:text-2xl font-bold text-amber-600 mt-0.5">{{ stats.sakit }}</p>
                    </div>
                    <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-100 shadow-xs col-span-2 sm:col-span-1">
                        <span class="text-[11px] text-rose-600 font-medium">Alpha</span>
                        <p class="text-xl sm:text-2xl font-bold text-rose-600 mt-0.5">{{ stats.alpha }}</p>
                    </div>
                </div>

                <!-- ===== QR Code Besar (Hanya Pengurus) ===== -->
                <div v-if="['ketua_umum', 'pengurus'].includes(userRole)" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 sm:p-6">
                    <div class="flex flex-col items-center justify-center text-center">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">QR Code Presensi</p>

                        <div class="relative inline-block mb-4">
                            <img
                                v-if="qrDataUrl"
                                :src="qrDataUrl"
                                alt="QR Code"
                                class="w-48 h-48 sm:w-64 sm:h-64 object-contain shadow-md border-4 border-white"
                            />
                            <div v-else class="w-48 h-48 sm:w-64 sm:h-64 bg-gray-100 flex items-center justify-center rounded-lg border border-gray-200">
                                <span class="text-xs text-gray-400">Memuat...</span>
                            </div>
                        </div>

                        <p class="text-xs font-mono text-gray-400 truncate max-w-sm mb-4">{{ props.selectedEvent.qr_token }}</p>

                        <div class="flex flex-col sm:flex-row gap-3 w-full max-w-sm justify-center">
                            <button
                                @click="showQrModal = true"
                                class="flex-1 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition"
                            >
                                Lihat Modal
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ===== Aksi Cepat Lapangan (Hanya Pengurus) ===== -->
                <div v-if="['ketua_umum', 'pengurus'].includes(userRole)" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <button
                        @click="showManualModal = true"
                        class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold shadow-sm flex flex-col items-center gap-1.5 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Catat Manual</span>
                    </button>

                    <button
                        @click="populateMembers"
                        class="px-4 py-3 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-semibold shadow-sm flex flex-col items-center gap-1.5 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Inisialisasi</span>
                    </button>

                    <button
                        v-if="props.attendances?.length"
                        @click="exportToExcel"
                        class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm flex flex-col items-center gap-1.5 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Export Excel</span>
                    </button>

                    <button
                        @click="startQrCamera"
                        class="px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-sm flex flex-col items-center gap-1.5 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Scan Kamera</span>
                    </button>
                </div>

                <!-- ===== Daftar Kehadiran (Hanya Pengurus) ===== -->
                <div v-if="['ketua_umum', 'pengurus'].includes(userRole)" class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gray-50/50">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm sm:text-base">Daftar Kehadiran</h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ props.attendances?.length ?? 0 }} anggota</p>
                        </div>
                    </div>

                    <!-- DESKTOP TABLE VIEW -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                                <tr>
                                    <th class="py-3 px-6">Nama Anggota</th>
                                    <th class="py-3 px-6">NPM</th>
                                    <th class="py-3 px-6">Divisi</th>
                                    <th class="py-3 px-6">Waktu Scan</th>
                                    <th class="py-3 px-6">Status</th>
                                    <th class="py-3 px-6 text-right">Ubah Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="!props.attendances || !props.attendances.length">
                                    <td colspan="6" class="py-12 text-center text-gray-500 text-sm">
                                        Belum ada data presensi. Anggota dapat scan QR Code acara atau klik "Inisialisasi".
                                    </td>
                                </tr>
                                <tr v-else v-for="att in props.attendances" :key="att.id" class="hover:bg-gray-50">
                                    <td class="py-3.5 px-6 font-semibold text-gray-900">{{ att.user?.name ?? '-' }}</td>
                                    <td class="py-3.5 px-6 text-gray-600">{{ att.user?.nim ?? '-' }}</td>
                                    <td class="py-3.5 px-6 text-gray-600">{{ att.user?.division ?? '-' }}</td>
                                    <td class="py-3.5 px-6 text-gray-500 text-xs">
                                        {{ att.scan_time ? new Date(att.scan_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-' }}
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <span :class="getStatusBadge(att.status)" class="px-2.5 py-1 rounded-full text-xs font-semibold border">
                                            {{ att.status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <div class="inline-flex rounded-md shadow-xs text-xs">
                                            <button
                                                @click="updateStatus(att.id, 'Hadir')"
                                                :class="att.status === 'Hadir' ? 'bg-emerald-600 text-white font-bold' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                                                class="px-2.5 py-1 rounded-l-md border-r border-gray-200 transition"
                                            >
                                                Hadir
                                            </button>
                                            <button
                                                @click="updateStatus(att.id, 'Izin')"
                                                :class="att.status === 'Izin' ? 'bg-blue-600 text-white font-bold' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                                                class="px-2.5 py-1 border-r border-gray-200 transition"
                                            >
                                                Izin
                                            </button>
                                            <button
                                                @click="updateStatus(att.id, 'Sakit')"
                                                :class="att.status === 'Sakit' ? 'bg-amber-600 text-white font-bold' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                                                class="px-2.5 py-1 border-r border-gray-200 transition"
                                            >
                                                Sakit
                                            </button>
                                            <button
                                                @click="updateStatus(att.id, 'Alpha')"
                                                :class="att.status === 'Alpha' ? 'bg-rose-600 text-white font-bold' : 'bg-gray-100 hover:bg-gray-200 text-gray-700'"
                                                class="px-2.5 py-1 rounded-r-md transition"
                                            >
                                                Alpha
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- MOBILE CARD VIEW -->
                    <div class="block md:hidden divide-y divide-gray-100">
                        <div v-if="!props.attendances || !props.attendances.length" class="p-8 text-center text-gray-500 text-xs">
                            Belum ada data presensi. Anggota dapat scan QR Code acara atau klik "Inisialisasi".
                        </div>
                        <div
                            v-else
                            v-for="att in props.attendances"
                            :key="att.id"
                            class="p-3.5 bg-white hover:bg-gray-50 flex flex-col gap-2.5 transition"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm leading-tight">{{ att.user?.name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 font-mono mt-0.5">NPM: {{ att.user?.nim ?? '-' }} &bull; {{ att.user?.division ?? 'Umum' }}</p>
                                </div>
                                <span :class="getStatusBadge(att.status)" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border shrink-0">
                                    {{ att.status }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-[11px] text-gray-400">
                                <span>Scan: {{ att.scan_time ? new Date(att.scan_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'Manual' }}</span>
                            </div>

                            <div class="grid grid-cols-4 gap-1.5 pt-0.5">
                                <button
                                    @click="updateStatus(att.id, 'Hadir')"
                                    :class="att.status === 'Hadir' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-gray-100 text-gray-700 active:bg-gray-200'"
                                    class="py-2 text-xs rounded-lg font-medium transition text-center"
                                >
                                    Hadir
                                </button>
                                <button
                                    @click="updateStatus(att.id, 'Izin')"
                                    :class="att.status === 'Izin' ? 'bg-blue-600 text-white font-bold ring-2 ring-blue-300' : 'bg-gray-100 text-gray-700 active:bg-gray-200'"
                                    class="py-2 text-xs rounded-lg font-medium transition text-center"
                                >
                                    Izin
                                </button>
                                <button
                                    @click="updateStatus(att.id, 'Sakit')"
                                    :class="att.status === 'Sakit' ? 'bg-amber-600 text-white font-bold ring-2 ring-amber-300' : 'bg-gray-100 text-gray-700 active:bg-gray-200'"
                                    class="py-2 text-xs rounded-lg font-medium transition text-center"
                                >
                                    Sakit
                                </button>
                                <button
                                    @click="updateStatus(att.id, 'Alpha')"
                                    :class="att.status === 'Alpha' ? 'bg-rose-600 text-white font-bold ring-2 ring-rose-300' : 'bg-gray-100 text-gray-700 active:bg-gray-200'"
                                    class="py-2 text-xs rounded-lg font-medium transition text-center"
                                >
                                    Alpha
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== Modal QR Code Agenda (Pengurus) ===== -->
            <div v-if="showQrModal && props.selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl text-center">
                    <div class="flex justify-between items-center pb-3 border-b mb-4">
                        <h3 class="font-bold text-gray-900">QR Presensi Agenda</h3>
                        <button @click="showQrModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <div class="flex justify-center p-3 bg-gray-50 rounded-xl mb-4 border">
                        <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code" class="w-64 h-64 object-contain" />
                        <p v-else class="text-sm text-gray-400 py-16">Menyiapkan QR Code...</p>
                    </div>

                    <p class="font-bold text-gray-800 text-base">{{ props.selectedEvent.title }}</p>
                    <p class="text-xs text-gray-500 mt-1 mb-5">Tampilkan layar ini kepada anggota untuk discan.</p>

                    <div class="flex gap-2">
                        <button
                            @click="refreshToken"
                            class="flex-1 py-2 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                        >
                            Generate Ulang Token
                        </button>
                        <button
                            @click="showQrModal = false"
                            class="flex-1 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

            <!-- ===== Modal QR Layar Penuh (Pengurus) ===== -->
            <div v-if="showFullScreenQr" class="fixed inset-0 z-[70] bg-[#0f172a] flex items-center justify-center p-4">
                <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-md text-center border border-blue-200">
                    <div class="inline-block p-1 bg-blue-100 rounded-lg mb-4 shadow-lg border border-blue-300">
                        <img :src="qrDataUrl" alt="QR Presensi" class="w-64 h-64 object-contain" />
                    </div>
                    <h4 class="text-lg font-bold text-[#0f172a] mb-1">Scan QR Code Ini</h4>
                    <p class="text-sm text-gray-600 mb-6">Pemain memindai QR ini dengan ponsel mereka untuk absensi.</p>

                    <div class="bg-blue-50 p-3 rounded-lg text-left mb-6 border border-blue-100">
                        <p class="text-xs font-bold text-blue-800 uppercase mb-2">Cara Penggunaan:</p>
                        <p class="text-xs text-blue-700 mb-1">• Buka kamera ponsel</p>
                        <p class="text-xs text-blue-700 mb-1">• Arahkan ke QR Code di atas</p>
                        <p class="text-xs text-blue-700">• Masukkan token pada form presensi di ponsel</p>
                    </div>

                    <button @click="showFullScreenQr = false" class="px-6 py-2.5 bg-[#0f172a] hover:bg-[#1e293b] text-white rounded-lg text-sm font-semibold transition-colors w-full">
                        Tutup QR
                    </button>
                </div>
            </div>

            <!-- ===== Modal Catat Manual (Pengurus) ===== -->
            <div v-if="showManualModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="flex justify-between items-center pb-3 border-b mb-4">
                        <h3 class="font-bold text-gray-900">Catat Presensi Anggota</h3>
                        <button @click="showManualModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form @submit.prevent="submitManualAttendance" class="space-y-4">
                        <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Anggota</label>
                            <select v-model="manualForm.user_id" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled>-- Pilih Anggota Aktif --</option>
                                <option v-for="m in props.members" :key="m.id" :value="m.id">
                                    {{ m.name }} ({{ m.nim || 'Tanpa NPM' }}) - {{ m.division || 'Umum' }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kehadiran</label>
                            <select v-model="manualForm.status" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showManualModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" :disabled="manualForm.processing" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===== Modal Scan / Token Input (Semua Role) ===== -->
            <div v-if="showScanInputModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <div class="flex justify-between items-center pb-3 border-b mb-4">
                        <h3 class="font-bold text-gray-900">Scan / Masukkan Token Presensi</h3>
                        <button @click="showScanInputModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>

                    <form @submit.prevent="submitScanToken" class="space-y-4">
                        <p class="text-xs text-gray-500">Masukkan 32-karakter QR Token dari agenda yang sedang berlangsung untuk verifikasi kehadiran akun Anda:</p>

                        <div>
                            <input
                                v-model="scanForm.qr_token"
                                type="text"
                                required
                                placeholder="Tempel / ketik token di sini..."
                                class="w-full border-gray-300 rounded-lg text-sm font-mono focus:ring-blue-500 focus:border-blue-500"
                            />
                            <p v-if="scanForm.errors.qr_token" class="text-xs text-red-600 mt-1">{{ scanForm.errors.qr_token }}</p>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showScanInputModal = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" :disabled="scanForm.processing" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg">Verifikasi Hadir</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ===== Modal Kamera QR Scan ===== -->
            <div v-if="showCameraModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 flex flex-col items-center">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pemindaian QR Code</h3>
                    <p class="text-sm text-gray-500 mb-6 text-center">Arahkan kamera ke QR Code presensi yang ditampilkan layar.</p>

                    <div id="qr-reader-attendance" class="w-full max-w-sm aspect-video bg-gray-100 rounded-xl border border-gray-200 mb-4"></div>

                    <div class="flex gap-3 w-full justify-center">
                        <button @click="stopQrCamera" class="flex-1 px-4 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </InternalLayout>
</template>

<style>
/* Tidak ada style tambahan */
</style>
