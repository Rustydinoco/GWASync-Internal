<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';



const props = defineProps({
    inventories: Array,
    filters: Object, // Tangkap nilai pencarian sebelumnya dari controller
});

// Jadikan nilai default sesuai yang dikirim dari backend
const search = ref(props.filters?.search || '');

// Pantau ketikan dan kirim ke backend tanpa reload halaman
watch(search, debounce((value) => {
    router.get('/inventories', { search: value }, {
        preserveState: true, // Biar tampilan tidak berkedip
        preserveScroll: true, // Biar posisi scroll tidak lompat ke atas
        replace: true, // Biar riwayat tombol 'back' di browser tidak penuh
    });
}, 300));

// DefineProps({
//     inventories: Array
// });

// State untuk mengatur modal
const showModal = ref(false);
const isEditing = ref(false); // Penanda apakah sedang mode edit
const editingId = ref(null);  // Menyimpan ID data yang sedang diedit

const form = useForm({
    item_code: '',
    name: '',
    category: 'Brass',
    condition: 'Baik',
    status: 'Tersedia',
});

// Fungsi membuka modal untuk Tambah Data
const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

// Fungsi membuka modal untuk Edit Data
const openEditModal = (item) => {
    isEditing.value = true;
    editingId.value = item.id;
    
    // Isi form dengan data yang dipilih
    form.item_code = item.item_code;
    form.name = item.name;
    form.category = item.category;
    form.condition = item.condition;
    form.status = item.status;
    
    form.clearErrors();
    showModal.value = true;
};

// Fungsi Submit (Menangani Tambah & Update)
const submit = () => {
    if (isEditing.value) {
        // Eksekusi Update (PUT)
        form.put(`/inventories/${editingId.value}`, {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        // Eksekusi Tambah (POST)
        form.post('/inventories', {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteItem = (id) => {
    if (confirm('Apakah kamu yakin ingin menghapus alat ini?')) {
        useForm().delete(`/inventories/${id}`);
    }
};
</script>

<template>
    <Head title="Manajemen Inventaris" />

    <InternalLayout>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Halaman -->
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Daftar Inventaris Alat</h3>
                    <p class="text-sm text-gray-500 mt-1">Kelola data alat musik, seragam, dan properti MBGWA.</p>
                </div>
                <!-- Panggil openAddModal saat tombol diklik -->
                <button 
                    @click="openAddModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                    + Tambah Alat
                </button>
            </div>

            <!-- Pencarian -->
             <div class="p-4 border-b bg-white">
                <div class="relative w-full md:w-1/3">
                    <input 
                        v-model="search" 
                        type="text" 
                        placeholder="Cari nama alat, kode, atau kategori..." 
                        class="w-full border rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                    <!-- Ikon Kaca Pembesar (Opsional) -->
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- Tabel Data -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-100">
                            <th class="py-3 px-6 font-semibold">Kode</th>
                            <th class="py-3 px-6 font-semibold">Nama Alat</th>
                            <th class="py-3 px-6 font-semibold">Kategori</th>
                            <th class="py-3 px-6 font-semibold">Kondisi</th>
                            <th class="py-3 px-6 font-semibold">Status</th>
                            <th class="py-3 px-6 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                      
                        <tr v-if="!inventories || inventories.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data inventaris.</td>
                        </tr>
                    
                        <tr v-else v-for="item in inventories" :key="item.id" class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="py-3 px-6 font-medium text-gray-900">{{ item.item_code }}</td>
                            <td class="py-3 px-6">{{ item.name }}</td>
                            <td class="py-3 px-6">{{ item.category }}</td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ item.condition }}</span>
                            </td>
                            <td class="py-3 px-6">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ item.status }}</span>
                            </td>
                            <td class="py-3 px-6 text-right">
                                <button @click="openEditModal(item)" class="text-blue-600 hover:text-blue-800 text-sm mr-3 font-medium">Edit</button>
                                <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL TAMBAH / EDIT DATA -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <!-- Judul Modal Dinamis -->
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    {{ isEditing ? 'Edit Inventaris' : 'Tambah Inventaris Baru' }}
                </h3>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Alat</label>
                        <input v-model="form.item_code" type="text" placeholder="Contoh: BRS-001" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <div v-if="form.errors.item_code" class="text-red-500 text-xs mt-1">{{ form.errors.item_code }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Alat</label>
                        <input v-model="form.name" type="text" placeholder="Contoh: Mellophone Yamaha" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select v-model="form.category" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Brass">Brass</option>
                            <option value="Battery">Battery</option>
                            <option value="Pit_Instrument">Pit Instrument</option>
                            <option value="Guard">Guard</option>
                            <option value="Properti">Properti</option>
                        </select>
                    </div>

                    <!-- Input tambahan untuk Kondisi & Status agar bisa di-edit -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                            <select v-model="form.condition" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">
                            {{ isEditing ? 'Simpan Perubahan' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </InternalLayout>
</template>