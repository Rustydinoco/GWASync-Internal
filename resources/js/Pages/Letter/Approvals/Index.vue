<script setup>
import InternalLayout from '../../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    approvals: Array,
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    letter_id: '',
    approver_user_id: '',
    approval_level: 1,
    flow_step_id: '',
    decision: 'Setuju',
    note: '',
    decided_at: '',
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
        form.put(`/letter-approvals/${editingId.value}`, options);
    } else {
        form.post('/letter-approvals', options);
    }
};

const remove = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        router.delete(`/letter-approvals/${id}`);
    }
};
</script>

<template>
    <Head title="Letter Approvals" />
    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Letter Approvals</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola persetujuan surat.</p>
                </div>
                <button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-xs">
                    + Tambah Approval
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <tr>
                            <th class="py-3 px-6">Letter</th>
                            <th class="py-3 px-6">Approver</th>
                            <th class="py-3 px-6 text-center">Level</th>
                            <th class="py-3 px-6 text-center">Decision</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!props.approvals || !props.approvals.length">
                            <td colspan="5" class="py-10 text-center text-gray-500 text-sm">Belum ada data approval.</td>
                        </tr>
                        <tr v-for="a in props.approvals" :key="a.id" class="hover:bg-gray-50">
                            <td class="py-3.5 px-6 font-semibold text-gray-900">{{ a.letter?.title || '-' }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ a.approver?.name || '-' }}</td>
                            <td class="py-3.5 px-6 text-center text-gray-600">{{ a.approval_level }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <span :class="a.decision === 'Setuju' ? 'bg-emerald-100 text-emerald-800' : a.decision === 'Tolak' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'" class="px-2 py-0.5 rounded text-[10px] font-semibold border">
                                    {{ a.decision }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <button @click="openEdit(a)" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ubah</button>
                                <button @click="remove(a.id)" class="ml-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingId ? 'Ubah Approval' : 'Tambah Approval' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Letter ID</label>
                            <input name="letter_id" v-model="form.letter_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Approver User ID</label>
                            <input name="approver_user_id" v-model="form.approver_user_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                            <input name="approval_level" v-model="form.approval_level" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Decision</label>
                            <select name="decision" v-model="form.decision" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Setuju">Setuju</option>
                                <option value="Tolak">Tolak</option>
                                <option value="Revisi">Revisi</option>
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