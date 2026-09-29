<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    events: Array
});

const showModal = ref(false);

const form = useForm({
    title: '',
    description: '',
    type: 'Latihan',
    start_time: '',
    end_time: '',
    location: ''
});

const submit = () => {
    form.post('/events', {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        }
    });
};

const deleteEvent = (id) => {
    if (confirm('Apakah kamu yakin ingin menghapus agenda ini?')) {
        useForm().delete(`/events/${id}`);
    }
};

const getTypeBadge = (type) => {
    switch (type) {
        case 'Latihan': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'Kejuaraan': return 'bg-red-100 text-red-700 border-red-200';
        case 'Rapat': return 'bg-amber-100 text-amber-700 border-amber-200';
        default: return 'bg-gray-100 text-gray-700 border-gray-200';
    }
};
</script>

<template>
    <Head title="Kalender Event & Agenda" />

    <InternalLayout>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Agenda & Jadwal Kegiatan</h3>
                    <p class="text-sm text-gray-500 mt-1">Kelola jadwal latihan, TC, rapat pengurus, dan penampilan MBGWA.</p>
                </div>
                <button 
                    @click="showModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                    + Tambah Agenda
                </button>
            </div>

            <!-- List Agenda -->
            <div class="p-6">
                <div v-if="!events || events.length === 0" class="text-center py-12 text-gray-500 text-sm">
                    Belum ada agenda kegiatan yang dijadwalkan.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div 
                        v-for="item in events" 
                        :key="item.id"
                        class="p-5 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getTypeBadge(item.type)" class="px-2.5 py-1 rounded-full text-xs font-semibold border">
                                    {{ item.type }}
                                </span>
                                <button @click="deleteEvent(item.id)" class="text-gray-400 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            
                            <h4 class="font-bold text-gray-900 text-base mb-1">{{ item.title }}</h4>
                            <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ item.description || 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="space-y-1.5 pt-3 border-t border-gray-100 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>{{ new Date(item.start_time).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) }}</span>
                            </div>
                            <div class="flex items-center gap-2" v-if="item.location">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ item.location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Event -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Agenda Baru</h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="event_title" class="block text-sm font-medium text-gray-700 mb-1">Judul Kegiatan</label>
                        <input 
                            id="event_title" 
                            name="title" 
                            v-model="form.title" 
                            type="text" 
                            placeholder="Contoh: Latihan Rutin Brass & Battery" 
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                            required
                        >
                    </div>

                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kegiatan</label>
                        <select 
                            id="event_type" 
                            name="type" 
                            v-model="form.type" 
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="Latihan">Latihan</option>
                            <option value="Evaluasi TC">Evaluasi TC</option>
                            <option value="Rapat">Rapat Pengurus</option>
                            <option value="Kejuaraan">Kejuaraan / Perform</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="event_start_time" class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                            <input 
                                id="event_start_time" 
                                name="start_time" 
                                v-model="form.start_time" 
                                type="datetime-local" 
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                                required
                            >
                        </div>
                        <div>
                            <label for="event_end_time" class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
                            <input 
                                id="event_end_time" 
                                name="end_time" 
                                v-model="form.end_time" 
                                type="datetime-local" 
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label for="event_location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input 
                            id="event_location" 
                            name="location" 
                            v-model="form.location" 
                            type="text" 
                            placeholder="Contoh: Lapangan Utama / Sanggar MBGWA" 
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                    </div>

                    <div>
                        <label for="event_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea 
                            id="event_description" 
                            name="description" 
                            v-model="form.description" 
                            rows="3" 
                            placeholder="Catatan tambahan agenda..." 
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">Simpan Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </InternalLayout>
</template>