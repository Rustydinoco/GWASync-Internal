<script setup>
import InternalLayout from '../../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    dispositions: Array,
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    letter_id: '',
    from_user_id: '',
    to_member_id: '',
    instruction: '',
    due_date: '',
    status: 'Belum',
    done_at: '',
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
        form.put(`/letter-dispositions/${editingId.value}`, options);
    } else {
        form.post('/letter-dispositions', options);
    }
};

const remove = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        router.delete(`/letter-dispositions/${id}`);
    }
};
</script>

<template>
    <Head title="Letter Dispositions" />
    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Letter Dispositions</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola disposisi surat.</p>
                </div>
                <button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-xs">
                    + Tambah Disposisi
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <tr>
                            <th class="py-3 px-6">Letter</th>
                            <th class="py-3 px-6">From</th>
                            <th class="py-3 px-6">To</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!props.dispositions || !props.dispositions.length">
                            <td colspan="5" class="py-10 text-center text-gray-500 text-sm">Belum ada data disposisi.</td>
                        </tr>
                        <tr v-for="d in props.dispositions" :key="d.id" class="hover:bg-gray-50">
                            <td class="py-3.5 px-6 font-semibold text-gray-900">{{ d.letter?.title || '-' }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ d.from_user?.name || '-' }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ d.to_member?.name || '-' }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <span :class="d.status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" class="px-2 py-0.5 rounded text-[10px] font-semibold border">
                                    {{ d.status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <button @click="openEdit(d)" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Ubah</button>
                                <button @click="remove(d.id)" class="ml-2 text-rose-600 hover:text-rose-700 text-sm font-medium">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-xs p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-5 sm:p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingId ? 'Ubah Disposisi' : 'Tambah Disposisi' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Letter ID</label>
                            <input name="letter_id" v-model="form.letter_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From User ID</label>
                            <input name="from_user_id" v-model="form.from_user_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">To Member ID</label>
                            <input name="to_member_id" v-model="form.to_member_id" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" v-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Belum">Belum</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instruction</label>
                        <input name="instruction" v-model="form.instruction" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
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