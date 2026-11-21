<script setup>
import InternalLayout from '../../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    responses: Array,
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    form_id: '',
    member_id: '',
    respondent_name: '',
    respondent_email: '',
    answers: {},
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
        form.put(`/form-responses/${editingId.value}`, options);
    } else {
        form.post('/form-responses', options);
    }
};

const remove = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        router.delete(`/form-responses/${id}`);
    }
};
</script>

<template>
    <Head title="Form Responses" />
    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Form Responses</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola respons formulir evaluasi.</p>
                </div>
                <button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-xs">
                    + Tambah Response
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <tr>
                            <th class="py-3 px-6">Form</th>
                            <th class="py-3 px-6">Member</th>
                            <th class="py-3 px-6">Respondent Name</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!props.responses || !props.responses.length">
                            <td colspan="4" class="py-10 text-center text-gray-500 text-sm">Belum ada data respons.</td>
                        </tr>
                        <tr v-for="r in props.responses" :key="r.id" class="hover:bg-gray-50">
                            <td class="py-3.5 px-6 font-semibold text-gray-900">{{ r.form?.title || '-' }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ r.member?.name || '-' }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ r.respondent_name || '-' }}</td>
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
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingId ? 'Ubah Response' : 'Tambah Response' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Form ID</label>
                            <input name="form_id" v-model="form.form_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Member ID</label>
                            <input name="member_id" v-model="form.member_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Respondent Name</label>
                            <input name="respondent_name" v-model="form.respondent_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input name="respondent_email" v-model="form.respondent_email" type="email" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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