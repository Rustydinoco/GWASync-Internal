<script setup>
import InternalLayout from '../../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    steps: Array,
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    flow_id: '',
    step_level: 1,
    step_name: '',
    approver_type: 'Role',
    role_id: '',
    staff_position_id: '',
    work_unit_id: '',
    user_id: '',
    allow_revision: 0,
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
        form.put(`/approval-flow-steps/${editingId.value}`, options);
    } else {
        form.post('/approval-flow-steps', options);
    }
};

const remove = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        router.delete(`/approval-flow-steps/${id}`);
    }
};
</script>

<template>
    <Head title="Approval Flow Steps" />
    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Approval Flow Steps</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola langkah-langkah persetujuan.</p>
                </div>
                <button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-xs">
                    + Tambah Step
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <tr>
                            <th class="py-3 px-6">Flow</th>
                            <th class="py-3 px-6 text-center">Level</th>
                            <th class="py-3 px-6">Step Name</th>
                            <th class="py-3 px-6">Approver Type</th>
                            <th class="py-3 px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!props.steps || !props.steps.length">
                            <td colspan="5" class="py-10 text-center text-gray-500 text-sm">Belum ada data approval flow step.</td>
                        </tr>
                        <tr v-for="s in props.steps" :key="s.id" class="hover:bg-gray-50">
                            <td class="py-3.5 px-6 font-semibold text-gray-900">{{ s.flow?.name || '-' }}</td>
                            <td class="py-3.5 px-6 text-center text-gray-600">{{ s.step_level }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ s.step_name }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ s.approver_type }}</td>
                            <td class="py-3.5 px-6 text-right">
                                <button @click="openEdit(s)" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ubah</button>
                                <button @click="remove(s.id)" class="ml-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingId ? 'Ubah Step' : 'Tambah Step' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Flow ID</label>
                            <input name="flow_id" v-model="form.flow_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                            <input name="step_level" v-model="form.step_level" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Step Name</label>
                        <input name="step_name" v-model="form.step_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approver Type</label>
                        <select name="approver_type" v-model="form.approver_type" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Role">Role</option>
                            <option value="Jabatan">Jabatan</option>
                            <option value="Pengguna">Pengguna</option>
                            <option value="Unit Kerja">Unit Kerja</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role ID</label>
                            <input name="role_id" v-model="form.role_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Staff Position ID</label>
                            <input name="staff_position_id" v-model="form.staff_position_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Work Unit ID</label>
                            <input name="work_unit_id" v-model="form.work_unit_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                            <input name="user_id" v-model="form.user_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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