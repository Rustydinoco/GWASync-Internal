<script setup>
import InternalLayout from '../../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    requests: Array,
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    title: '',
    reason: '',
    total_estimate: 0,
    total_actual: 0,
    needed_by_date: '',
    payment_method: 'Tunai',
    funding_request_id: '',
    status: 'Draft',
});

const openCreate = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (item) => {
    editingId.value = item.id;
    form.clearErrors();
    Object.keys(form.data()).forEach((key) => {
        if (item[key] !== undefined && item[key] !== null) {
            form[key] = item[key];
        } else {
            form.reset(key);
        }
    });
    showModal.value = true;
};

const submit = () => {
    const options = {
        onSuccess: () => {
            showModal.value = false;
            editingId.value = null;
            form.reset();
        },
    };
    if (editingId.value) {
        form.put(`/funding-requests/${editingId.value}`, options);
    } else {
        form.post('/funding-requests', options);
    }
};

const remove = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        router.delete(`/funding-requests/${id}`);
    }
};
</script>

<template>
    <Head title="Pengajuan Dana" />
    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Pengajuan Dana</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola pengajuan dana kegiatan.</p>
                </div>
                <button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-xs">
                    + Ajukan Dana
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <tr>
                            <th class="py-3 px-6">No. Pengajuan</th>
                            <th class="py-3 px-6">Judul</th>
                            <th class="py-3 px-6">Estimasi</th>
                            <th class="py-3 px-6">Actual</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!props.requests || !props.requests.length">
                            <td colspan="6" class="py-10 text-center text-gray-500 text-sm">Belum ada data pengajuan dana.</td>
                        </tr>
                        <tr v-for="r in props.requests" :key="r.id" class="hover:bg-gray-50">
                            <td class="py-3.5 px-6 font-semibold text-gray-900 font-mono">{{ r.request_number }}</td>
                            <td class="py-3.5 px-6 text-gray-900">{{ r.title }}</td>
                            <td class="py-3.5 px-6 text-right text-gray-600 font-mono">{{ r.total_estimate }}</td>
                            <td class="py-3.5 px-6 text-right text-gray-600 font-mono">{{ r.total_actual }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <span :class="r.status === 'Disetujui' ? 'bg-emerald-100 text-emerald-800' : r.status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'" class="px-2 py-0.5 rounded text-[10px] font-semibold border">
                                    {{ r.status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <button @click="openEdit(r)" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ubah</button>
                                <button @click="remove(r.id)" class="ml-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingId ? 'Ubah Pengajuan' : 'Ajukan Dana' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Pengajuan</label>
                            <input name="request_number" v-model="form.request_number" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                            <input name="title" v-model="form.title" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                        <textarea name="reason" v-model="form.reason" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimasi</label>
                            <input name="total_estimate" v-model="form.total_estimate" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Actual</label>
                            <input name="total_actual" v-model="form.total_actual" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode</label>
                            <select name="payment_method" v-model="form.payment_method" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Tunai">Tunai</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Pengajuan Dana">Pengajuan Dana</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" v-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Draft">Draft</option>
                                <option value="Diajuan">Diajuan</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 disabled:opacity-50">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </InternalLayout>
</template>