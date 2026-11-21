<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import QRCode from 'qrcode';

const props = defineProps({
    member: Object,
    allMembers: Array,
    isManagement: Boolean,
});

const qrDataUrl = ref('');
const logoUrl = '/LOGO%20GWA%20BARU-transparent-cropped.png';
const selectedUserId = ref(props.member?.id || '');
const currentSide = ref('front'); // 'front' | 'back'
const printMode = ref('single'); // 'single' (kartu aktif saja) | 'both' (depan & belakang)

const generateQr = async () => {
    if (props.member) {
        const payload = props.member.kta_qr_code || `MBGWA:${props.member.nia || props.member.id}:${props.member.email}`;
        try {
            qrDataUrl.value = await QRCode.toDataURL(payload, {
                width: 300,
                margin: 1,
                color: {
                    dark: '#071a33',
                    light: '#ffffff',
                },
            });
        } catch (err) {
            console.error('Failed to generate KTA QR', err);
        }
    }
};

onMounted(() => {
    generateQr();
});

watch(() => props.member, () => {
    generateQr();
    selectedUserId.value = props.member?.id;
});

const changeMember = (id) => {
    router.get('/kta', { user_id: id }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const printCard = (mode = 'single') => {
    printMode.value = mode;
    setTimeout(() => {
        window.print();
    }, 50);
};

const toggleSide = () => {
    currentSide.value = currentSide.value === 'front' ? 'back' : 'front';
};
</script>

<template>
    <Head title="Kartu Tanda Anggota (KTA) Digital" />

    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-4xl mx-auto">
            <!-- Header Section (Disembunyikan saat cetak) -->
            <div class="print:hidden flex flex-col md:flex-row md:items-center justify-between gap-3.5 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Kartu Tanda Anggota (KTA) Digital</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Identitas resmi anggota MB Gita Widya Agni dengan verifikasi QR Code.</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 w-full md:w-auto">
                    <!-- Dropdown Pilih Anggota (Khusus Pengurus) -->
                    <div v-if="isManagement && allMembers && allMembers.length" class="w-full sm:min-w-[200px]">
                        <select 
                            v-model="selectedUserId" 
                            @change="changeMember(selectedUserId)"
                            class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 py-2"
                        >
                            <option v-for="m in allMembers" :key="m.id" :value="m.id">
                                {{ m.name }} ({{ m.nim || m.nia || 'Anggota' }})
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
                        <!-- Tombol Putar Kartu -->
                        <button 
                            @click="toggleSide"
                            class="px-3 py-2 text-xs sm:text-sm font-medium rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition flex items-center justify-center gap-1.5"
                            title="Balik Kartu Depan / Belakang"
                        >
                            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>{{ currentSide === 'front' ? 'Belakang' : 'Depan' }}</span>
                        </button>

                        <!-- Tombol Cetak -->
                        <button 
                            @click="printCard('single')"
                        class="px-3.5 py-2 text-xs sm:text-sm font-semibold rounded-lg bg-[#0a2f61] hover:bg-[#071f42] text-white transition flex items-center justify-center gap-1.5 shadow-xs"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak KTA</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Kontrol Pilihan Sisi di Layar (Print Hidden) -->
            <div class="print:hidden flex flex-wrap justify-center gap-2">
                <button 
                    @click="currentSide = 'front'"
                    :class="currentSide === 'front' ? 'bg-[#0a2f61] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition"
                >
                    Sisi Depan
                </button>
                <button 
                    @click="currentSide = 'back'"
                    :class="currentSide === 'back' ? 'bg-[#0a2f61] text-white shadow-xs' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition"
                >
                    Sisi Belakang
                </button>
                <button 
                    @click="printCard('both')"
                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition flex items-center gap-1.5"
                >
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" />
                    </svg>
                    Cetak Bolak-Balik
                </button>
            </div>

            <!-- AREA CETAK KHUSUS KTA (Hanya elemen ini yang muncul saat cetak) -->
            <div id="printable-kta-container" class="flex justify-center items-center py-2 sm:py-4">
                
                <!-- MODE: Single Card (Sesuai sisi yang aktif atau pilihan cetak single) -->
                <div v-if="printMode === 'single'" class="kta-card-box w-full max-w-[360px] sm:max-w-[420px]">
                    
                    <!-- KARTU SISI DEPAN (FRONT) -->
                    <div 
                        v-if="currentSide === 'front'"
                        class="kta-card relative w-full h-[230px] sm:h-[260px] rounded-2xl shadow-xl overflow-hidden bg-gradient-to-tr from-[#061a35] via-[#0a2f61] to-[#16477f] text-white p-4 sm:p-5 border border-white/20 flex flex-col justify-between"
                    >
                        <!-- Pattern Watermark Background -->
                        <img :src="logoUrl" alt="" aria-hidden="true" class="absolute left-1/2 top-1/2 w-48 sm:w-56 -translate-x-1/2 -translate-y-1/2 opacity-[0.16] pointer-events-none" />

                        <!-- Top Header -->
                        <div class="flex items-center justify-between z-10 border-b border-white/20 pb-2 sm:pb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center overflow-hidden">
                                    <img :src="logoUrl" alt="Logo MB Gita Widya Agni" class="w-full h-full object-contain p-0.5" />
                                </div>
                                <div>
                                    <h3 class="text-[11px] sm:text-xs uppercase font-extrabold tracking-wider text-white leading-tight">MB Gita Widya Agni</h3>
                                    <p class="text-[8px] sm:text-[9px] text-slate-200 tracking-wider">KARTU TANDA ANGGOTA RESMI</p>
                                </div>
                            </div>

                        </div>

                        <!-- Middle Content -->
                        <div class="flex items-center justify-between gap-2.5 my-auto z-10">
                            <div class="space-y-0.5 min-w-0 flex-1 pr-2">
                                <p class="text-[8px] sm:text-[9px] uppercase tracking-[0.18em] text-slate-300">Identitas Anggota</p>
                                <h4 class="text-sm sm:text-base font-extrabold text-white tracking-wide truncate leading-tight">{{ member.name }}</h4>
                                <p class="text-[10px] sm:text-[11px] text-white font-mono truncate">NPM: {{ member.nim || '-' }}</p>
                                <p class="text-[9px] sm:text-[10px] text-slate-200 font-mono truncate">NIA: {{ member.nia || '-' }}</p>
                                <div class="pt-1 flex flex-wrap gap-1 text-[9px] sm:text-[10px]">
                                    <span class="bg-white/10 px-1.5 py-0.5 rounded border border-white/20 text-slate-100 truncate max-w-[120px]">{{ member.division || 'Umum' }}</span>
                                    <span class="bg-white/10 px-1.5 py-0.5 rounded border border-white/20 text-slate-100 capitalize">{{ member.role || 'anggota' }}</span>
                                </div>
                            </div>

                            <!-- QR Code Preview -->
                            <div class="bg-white p-1 rounded-xl shadow-md shrink-0 border border-slate-200">
                                <img v-if="qrDataUrl" :src="qrDataUrl" alt="KTA QR Code" class="w-14 h-14 sm:w-16 sm:h-16 object-contain" />
                                <div v-else class="w-14 h-14 sm:w-16 sm:h-16 bg-gray-100 flex items-center justify-center text-[7px] text-gray-400">Loading...</div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between text-[8px] sm:text-[9px] text-slate-200 border-t border-white/20 pt-1.5 sm:pt-2 z-10">
                            <span>GWASync Digital Identity</span>
                            <span class="font-mono">ID: {{ member.id.toString().padStart(6, '0') }}</span>
                        </div>
                    </div>

                    <!-- KARTU SISI BELAKANG (BACK) -->
                    <div 
                        v-else
                        class="kta-card relative w-full h-[230px] sm:h-[260px] rounded-2xl shadow-xl overflow-hidden bg-gradient-to-tr from-[#061a35] via-[#0a2f61] to-[#16477f] text-white p-4 sm:p-5 border border-white/20 flex flex-col justify-between"
                    >
                        <img :src="logoUrl" alt="" aria-hidden="true" class="absolute left-1/2 top-1/2 w-48 sm:w-56 -translate-x-1/2 -translate-y-1/2 opacity-[0.16] pointer-events-none" />
                        <!-- Top Header -->
                        <div class="flex items-center justify-between border-b border-white/20 pb-2">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded overflow-hidden"><img :src="logoUrl" alt="Logo MB Gita Widya Agni" class="h-full w-full object-contain p-0.5" /></div>
                                <span class="text-[9px] sm:text-[10px] font-bold text-white uppercase tracking-widest">Ketentuan Pemegang Kartu</span>
                            </div>
                            <span class="text-[8px] sm:text-[9px] text-slate-200">MBGWA &bull; Internal</span>
                        </div>

                        <!-- Rules / Ketentuan -->
                        <div class="space-y-1 my-auto text-[9px] sm:text-[10px] text-slate-100 leading-relaxed pr-1">
                            <p class="flex items-start gap-1">
                                <span class="text-slate-300 font-bold shrink-0">1.</span>
                                <span>Kartu identitas resmi anggota aktif UKM Marching Band Gita Widya Agni.</span>
                            </p>
                            <p class="flex items-start gap-1">
                                <span class="text-slate-300 font-bold shrink-0">2.</span>
                                <span>Wajib ditunjukkan saat presensi agenda latihan, TC, dan peminjaman alat sarpras.</span>
                            </p>
                            <p class="flex items-start gap-1">
                                <span class="text-slate-300 font-bold shrink-0">3.</span>
                                <span>Segala bentuk peminjaman inventaris dicatat atas nama pemegang KTA ini.</span>
                            </p>
                            <p class="flex items-start gap-1">
                                <span class="text-slate-300 font-bold shrink-0">4.</span>
                                <span>Jika menemukan kartu ini, harap hubungi sekretariat MBGWA.</span>
                            </p>
                        </div>

                        <!-- Footer & Validation -->
                        <div class="flex items-center justify-between border-t border-white/20 pt-1.5 sm:pt-2 text-[8px] sm:text-[9px] text-slate-200">
                            <div>
                                <p class="text-white font-semibold">Pengurus Harian Gita Widya Agni</p>
                                <p class="text-[7px] sm:text-[8px] text-slate-200">Terverifikasi Sistem GWASync</p>
                            </div>
                            <div class="text-right font-mono">
                                <span class="text-white">RESMI &bull; SAH</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODE: Both Cards (Depan & Belakang Bersebelahan untuk Cetak Fisik) -->
                <div v-else class="flex flex-col md:flex-row items-center gap-3 sm:gap-4 w-full justify-center">
                    <!-- Sisi Depan -->
                    <div class="kta-card relative w-full max-w-[360px] sm:max-w-[420px] h-[230px] sm:h-[260px] rounded-2xl shadow-xl overflow-hidden bg-gradient-to-tr from-[#061a35] via-[#0a2f61] to-[#16477f] text-white p-4 sm:p-5 border border-white/20 flex flex-col justify-between">
                        <img :src="logoUrl" alt="" aria-hidden="true" class="absolute left-1/2 top-1/2 w-48 -translate-x-1/2 -translate-y-1/2 opacity-[0.16] pointer-events-none" />
                        <div class="flex items-center justify-between z-10 border-b border-white/20 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center overflow-hidden"><img :src="logoUrl" alt="Logo MB Gita Widya Agni" class="w-full h-full object-contain p-0.5" /></div>
                                <div>
                                    <h3 class="text-xs uppercase font-extrabold text-white">MB Gita Widya Agni</h3>
                                    <p class="text-[8px] text-slate-200">KARTU TANDA ANGGOTA</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2.5 my-auto z-10">
                            <div class="space-y-0.5 min-w-0 flex-1 pr-2">
                                <p class="text-[8px] uppercase tracking-[0.18em] text-slate-300">Identitas Anggota</p>
                                <h4 class="text-sm sm:text-base font-extrabold text-white line-clamp-1">{{ member.name }}</h4>
                                <p class="text-[10px] text-white font-mono">NPM: {{ member.nim || '-' }}</p>
                                <p class="text-[9px] text-slate-200 font-mono">NIA: {{ member.nia || '-' }}</p>
                                <div class="pt-1 flex gap-1 text-[9px]">
                                    <span class="bg-white/10 px-1.5 py-0.5 rounded border border-white/20 text-slate-100">{{ member.division || 'Umum' }}</span>
                                    <span class="bg-white/10 px-1.5 py-0.5 rounded border border-white/20 text-slate-100 capitalize">{{ member.role || 'anggota' }}</span>
                                </div>
                            </div>
                            <div class="bg-white p-1 rounded-xl shadow shrink-0 border border-slate-200">
                                <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR" class="w-14 h-14 object-contain" />
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[8px] text-slate-200 border-t border-white/20 pt-1.5">
                            <span>GWASync Digital Identity</span>
                            <span class="font-mono">ID: {{ member.id.toString().padStart(6, '0') }}</span>
                        </div>
                    </div>

                    <!-- Sisi Belakang -->
                    <div class="kta-card relative w-full max-w-[360px] sm:max-w-[420px] h-[230px] sm:h-[260px] rounded-2xl shadow-xl overflow-hidden bg-gradient-to-tr from-[#061a35] via-[#0a2f61] to-[#16477f] text-white p-4 sm:p-5 border border-white/20 flex flex-col justify-between">
                        <img :src="logoUrl" alt="" aria-hidden="true" class="absolute left-1/2 top-1/2 w-48 -translate-x-1/2 -translate-y-1/2 opacity-[0.16] pointer-events-none" />
                        <div class="flex items-center justify-between border-b border-white/20 pb-2">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded overflow-hidden"><img :src="logoUrl" alt="Logo MB Gita Widya Agni" class="h-full w-full object-contain p-0.5" /></div>
                                <span class="text-[10px] font-bold text-white uppercase tracking-widest">Ketentuan Pemegang Kartu</span>
                            </div>
                            <span class="text-[8px] text-slate-200">MBGWA &bull; Internal</span>
                        </div>
                        <div class="space-y-1 my-auto text-[9px] text-slate-100 leading-relaxed">
                            <p><span class="text-slate-300 font-bold">1.</span> Kartu resmi anggota UKM Marching Band Gita Widya Agni.</p>
                            <p><span class="text-slate-300 font-bold">2.</span> Wajib ditunjukkan saat presensi latihan dan peminjaman alat.</p>
                            <p><span class="text-slate-300 font-bold">3.</span> Peminjaman alat dipertanggungjawabkan atas nama pemegang KTA.</p>
                            <p><span class="text-slate-300 font-bold">4.</span> Jika menemukan kartu ini, harap hubungi sekretariat MBGWA.</p>
                        </div>
                        <div class="flex items-center justify-between border-t border-white/20 pt-1.5 text-[8px] text-slate-200">
                            <div>
                                <p class="text-white font-semibold">Pengurus Harian MBGWA</p>
                                <p class="text-[7px] text-slate-200">Terverifikasi Sistem GWASync</p>
                            </div>
                            <span class="text-white font-mono">RESMI &bull; SAH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Penggunaan KTA (Print Hidden) -->
            <div class="print:hidden bg-blue-50 border border-blue-100 p-5 rounded-xl text-xs text-blue-800 space-y-1">
                <p class="font-bold text-sm text-blue-900 mb-1">Informasi Cetak KTA:</p>
                <p>&bull; Klik tombol <strong>Cetak Kotak KTA</strong> untuk mencetak hanya kotak kartu KTA saja tanpa elemen halaman lain.</p>
                <p>&bull; Anda juga dapat memilih <strong>Cetak Depan & Belakang Sekaligus</strong> untuk mencetak kedua sisi bersebelahan yang siap digunting dan dilipat menjadi ID card fisik.</p>
                <p>&bull; Pada jendela print dialog browser, pastikan opsi <em>Background graphics</em> dicentang agar warna gradient kartu tercetak sempurna.</p>
            </div>
        </div>
    </InternalLayout>
</template>

<style>
/* CSS KHUSUS PRINT CETAK KTA */
@media print {
    /* 1. Hilangkan margin halaman default browser */
    @page {
        size: auto;
        margin: 0;
    }

    /* 2. Sembunyikan seluruh elemen di dokumen */
    body * {
        visibility: hidden !important;
    }

    /* 3. Tampilkan HANYA kontainer KTA dan anak elemennya */
    #printable-kta-container,
    #printable-kta-container * {
        visibility: visible !important;
    }

    /* 4. Posisikan kotak KTA tepat di tengah kertas */
    #printable-kta-container {
        position: fixed !important;
        left: 50% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
        width: auto !important;
        height: auto !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* 5. Pengaturan style kartu saat dicetak */
    .kta-card {
        box-shadow: none !important;
        border: 1px solid #1e293b !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }

    /* Sembunyikan navigasi, sidebar, header */
    aside, header, nav, .print\:hidden {
        display: none !important;
    }
}
</style>
