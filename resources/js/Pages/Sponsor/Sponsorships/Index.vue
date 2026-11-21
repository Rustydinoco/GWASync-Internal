<script setup>
import InternalLayout from '../../../Layouts/InternalLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sponsorships: Array,
});

const showModal = ref(false);
const editingId = ref(null);
const form = useForm({
    partner_name: '',
    contact_person: '',
    phone: '',
    email: '',
    package_id: '',
    amount: 0,
    start_date: '',
    end_date: '',
    agreement_number: '',
    letter_id: '',
    signed_by_member_id: '',
    status: 'Prospek',
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
        form.put(`/sponsorships/${editingId.value}`, options);
    } else {
        form.post('/sponsorships', options);
    }
};

const remove = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
        router.delete(`/sponsorships/${id}`);
    }
};
</script>

<template>
    <Head title="Sponsorship" />
    <InternalLayout>
        <div class="space-y-4 sm:space-y-6 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 sm:p-6 rounded-xl shadow-xs border border-gray-100">
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-gray-900">Sponsorship</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Kelola data kerjasama sponsor.</p>
                </div>
                <button @click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-xs">
                    + Tambah Sponsorship
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <tr>
                            <th class="py-3 px-6">Partner</th>
                            <th class="py-3 px-6">PIC</th>
                            <th class="py-3 px-6">Package</th>
                            <th class="py-3 px-6 text-right">Amount</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="!props.sponsorships || !props.sponsorships.length">
                            <td colspan="6" class="py-10 text-center text-gray-500 text-sm">Belum ada data sponsorship.</td>
                        </tr>
                        <tr v-for="s in props.sponsorships" :key="s.id" class="hover:bg-gray-50">
                            <td class="py-3.5 px-6 font-semibold text-gray-900">{{ s.partner_name }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ s.contact_person || '-' }}</td>
                            <td class="py-3.5 px-6 text-gray-600">{{ s.package?.name || '-' }}</td>
                            <td class="py-3.5 px-6 text-right text-gray-600 font-mono">{{ s.amount }}</td>
                            <td class="py-3.5 px-6 text-center">
                                <span :class="s.status === 'Deal' ? 'bg-emerald-100 text-emerald-800' : s.status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800'" class="px-2 py-0.5 rounded text-[10px] font-semibold border">
                                    {{ s.status }}
                                </span>
                            </td>
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
                <h3 class="text-lg font-bold text-gray-900 mb-4">{{ editingId ? 'Ubah Sponsorship' : 'Tambah Sponsorship' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Partner</label>
                            <input name="partner_name" v-model="form.partner_name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                            <input name="contact_person" v-model="form.contact_person" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input name="phone" v-model="form.phone" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input name="email" v-model="form.email" type="email" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                            <input name="amount" v-model="form.amount" type="number" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" v-model="form.status" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="Prospek">Prospek</option>
                                <option value="Negosiasi">Negosiasi</option>
                                <option value="Deal">Deal</option>
                                <option value="Ditolak">Ditolak</option>
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