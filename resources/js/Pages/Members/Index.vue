<script setup>
import InternalLayout from '../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    members: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get('/members', { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300));

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    email: '',
    nim: '',
    phone: '',
    division: '',
    status: 'Aktif',
});

const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (member) => {
    isEditing.value = true;
    editingId.value = member.id;
    
    form.name = member.name;
    form.email = member.email;
    form.nim = member.nim || '';
    form.phone = member.phone || '';
    form.division = member.division || '';
    form.status = member.status;
    
    form.clearErrors();
    showModal.value = true;
};

const submit = () => {
    if (isEditing.value) {
        form.put(`/members/${editingId.value}`, {
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    } else {
        form.post('/members', {
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    }
};

const deleteMember = (id) => {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        useForm().delete(`/members/${id}`);
    }
};
</script>

<template>
    <Head title="Master Data Anggota" />

    <InternalLayout>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Data Anggota</h3>
                    <p class="text-sm text-gray-500">Kelola daftar anggota dan pengurus.</p>
                </div>
                <button @click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    + Tambah Anggota
                </button>
            </div>
            
            <div class="p-4 border-b bg-white">
                <input 
                    v-model="search" 
                    type="text" 
                    placeholder="Cari nama, NIM, atau divisi..." 
                    class="border rounded-lg px-4 py-2 w-full md:w-1/3 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-sm border-b">
                        <tr>
                            <th class="py-3 px-6">Nama Lengkap</th>
                            <th class="py-3 px-6">NIA / Kontak</th>
                            <th class="py-3 px-6">Divisi</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        <tr v-if="!members || members.length === 0">
                            <td colspan="5" class="py-6 text-center text-gray-500">Belum ada data anggota.</td>
                        </tr>
                        <tr v-else v-for="member in members" :key="member.id" class="border-b hover:bg-gray-50">
                            <td class="py-3 px-6 font-medium">{{ member.name }}<br><span class="text-xs text-gray-400 font-normal">{{ member.email }}</span></td>
                            <td class="py-3 px-6">{{ member.nim || '-' }}<br><span class="text-xs text-gray-400">{{ member.phone || '-' }}</span></td>
                            <td class="py-3 px-6">{{ member.division || '-' }}</td>
                            <td class="py-3 px-6">
                                <span :class="{'bg-green-100 text-green-700': member.status === 'Aktif', 'bg-gray-100 text-gray-700': member.status !== 'Aktif'}" class="px-2 py-1 rounded-full text-xs font-medium">
                                    {{ member.status }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-right space-x-3">
                                <button @click="openEditModal(member)" class="text-blue-600 hover:underline">Edit</button>
                                <button @click="deleteMember(member.id)" class="text-red-600 hover:underline">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah/Edit -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
                <h3 class="text-lg font-bold mb-4">{{ isEditing ? 'Edit Anggota' : 'Tambah Anggota' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1">Nama Lengkap</label>
                        <input type="text" v-model="form.name" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">NIA</label>
                            <input type="text" v-model="form.nim" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <div v-if="form.errors.nim" class="text-red-500 text-xs mt-1">{{ form.errors.nim }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">No. HP/WA</label>
                            <input type="text" v-model="form.phone" class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select v-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="Aktif">Aktif</option>
                                <option value="Pasif">Pasif</option>
                                <option value="Alumni">Alumni</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Divisi (Opsional)</label>
                        <select v-model="form.division" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">-- Pilih Divisi --</option>
                            <option value="Humas">BPH</option>
                            <option value="Latbang">Latbang</option>
                            <option value="Sarpras">Sarpras</option>
                            <option value="Media">Media</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </InternalLayout>
</template>