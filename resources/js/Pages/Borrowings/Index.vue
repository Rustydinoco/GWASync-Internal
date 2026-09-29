<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3'; // Tambahkan router di sini
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import Multiselect from '@vueform/multiselect';
import '@vueform/multiselect/themes/default.css';

const props = defineProps({
    borrowings: Array,
    users: Array,
    inventories: Array,
    filters: Object, // Tambahkan filters di sini
});

// Ubah nama variabel menjadi 'search' agar cocok dengan HTML
const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get('/borrowings', { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300));

const showModal = ref(false);

const form = useForm({
    user_id: '',
    inventory_id: '',
    start_date: '',
    end_date: '',
});

const submitForm = () => {
    form.post('/borrowings', {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        }
    });
};

const updateStatus = (id, newStatus) => {
    useForm({ status: newStatus }).patch(`/borrowings/${id}/status`);
};
</script>

<template>
    <Head title="Manajemen Peminjaman" />

    <InternalLayout>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Data Peminjaman Alat</h3>
                    <p class="text-sm text-gray-500">Persetujuan dan pencatatan alat keluar/masuk.</p>
                </div>
                <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    + Ajukan Peminjaman
                </button>
            </div>
        <div class="p-4 border-b bg-white">
            <div class="relative w-full md:w-1/3">
                <input 
                v-model="search" 
                type="text" 
                placeholder="Cari nama peminjam, nama alat, atau kode..." 
                class="w-full border rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
            >
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"></path></svg>
            </div>
        </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-sm border-b">
                        <tr>
                            <th class="py-3 px-6">Peminjam</th>
                            <th class="py-3 px-6">Nama Alat</th>
                            <th class="py-3 px-6">Tanggal Pinjam</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        <tr v-if="!borrowings || borrowings.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-500"">Belum ada transaksi peminjaman.</td>
                        </tr>
                        <tr v-else v-for="trx in borrowings" :key="trx.id" class="border-b hover:bg-gray-50">
                            <td class="py-3 px-6">{{ trx.user?.name }}</td>
                            <td class="py-3 px-6">{{ trx.inventory?.name }} ({{ trx.inventory?.item_code }})</td>
                            <td class="py-3 px-6">{{ trx.start_date }} s/d {{ trx.end_date }}</td>
                            <td class="py-3 px-6">
                                <span :class="{
                                    'bg-yellow-100 text-yellow-700': trx.status === 'Pending',
                                    'bg-blue-100 text-blue-700': trx.status === 'Disetujui',
                                    'bg-green-100 text-green-700': trx.status === 'Dikembalikan',
                                    'bg-red-100 text-red-700': trx.status === 'Ditolak'
                                }" class="px-2 py-1 rounded-full text-xs font-medium">
                                    {{ trx.status }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right space-x-2">
                                <button v-if="trx.status === 'Pending'" @click="updateStatus(trx.id, 'Disetujui')" class="text-blue-600 hover:underline">Setujui</button>
                                <button v-if="trx.status === 'Pending'" @click="updateStatus(trx.id, 'Ditolak')" class="text-red-600 hover:underline">Tolak</button>
                                <button v-if="trx.status === 'Disetujui'" @click="updateStatus(trx.id, 'Dikembalikan')" class="text-green-600 hover:underline">Kembalikan</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Pengajuan Peminjaman -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">Ajukan Peminjaman</h3>
                <form @submit.prevent="submitForm" class="space-y-4">
    <!-- Pilihan Anggota -->
                <div>
                    <label class="block text-sm mb-1">Pilih Anggota</label>
                    <Multiselect
                    v-model="form.user_id"
                    :options="users"
                    valueProp="id"
                    label="name"
                    :searchable="true"
                    placeholder="Ketik untuk mencari anggota MBGWA..."
                    class="border rounded-lg text-sm"
                />
                    <div v-if="form.errors.user_id" class="text-red-500 text-xs mt-1">{{ form.errors.user_id }}</div>
                </div>

                <!-- Pilihan Alat -->
                <div>
                    <label class="block text-sm mb-1">Pilih Alat (Tersedia)</label>
                    <Multiselect
                        v-model="form.inventory_id"
                        :options="inventories"
                        valueProp="id"
                        label="name"
                        :searchable="true"
                        placeholder="Ketik untuk mencari alat..."
                        class="border rounded-lg text-sm"
                        
                    />
                    <div v-if="form.errors.inventory_id" class="text-red-500 text-xs mt-1">{{ form.errors.inventory_id }}</div>
                </div>

                <!-- Tanggal Peminjaman -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1">Tanggal Pinjam</label>
                        <input type="date" v-model="form.start_date" class="w-full border rounded-lg px-3 py-2 text-sm" :class="{'border-red-500': form.errors.start_date}">
                        <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Tanggal Kembali</label>
                        <input type="date" v-model="form.end_date" class="w-full border rounded-lg px-3 py-2 text-sm" :class="{'border-red-500': form.errors.end_date}">
                        <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">
                        <span v-if="form.processing">Memproses...</span>
                        <span v-else>Ajukan</span>
                    </button>
                </div>
            </form>
            </div>
        </div>
    </InternalLayout>
</template>