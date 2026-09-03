<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Briefcase, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    vendors: any;
}>();

const page = usePage();
const can = (permissionName: string) => {
    const roles = (page.props.auth as any).roles || [];
    const permissions = (page.props.auth as any).permissions || [];
    if (roles.includes('Admin')) return true;
    return permissions.includes(permissionName);
};

const showModal = ref(false);
const editingVendor = ref<any>(null);

const form = useForm({
    vendor_code: (page.props as any).global_settings?.vendor_code_prefix || 'VND-',
    name: '',
    contact_person: '',
    email: '',
    phone: '',
    company: '',
    notes: '',
    status: 'Active',
});

const openCreateModal = () => {
    editingVendor.value = null;
    form.reset();
    form.clearErrors();
    form.vendor_code = (page.props as any).global_settings?.vendor_code_prefix || 'VND-';
    showModal.value = true;
};

const openEditModal = (vendor: any) => {
    editingVendor.value = vendor;
    form.clearErrors();
    form.vendor_code = vendor.vendor_code || '';
    form.name = vendor.name || '';
    form.contact_person = vendor.contact_person || '';
    form.email = vendor.email || '';
    form.phone = vendor.phone || '';
    form.company = vendor.company || '';
    form.notes = vendor.notes || '';
    form.status = vendor.status || 'Active';
    showModal.value = true;
};

const submitForm = () => {
    if (editingVendor.value) {
        form.put(route('vendors.update', editingVendor.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('vendors.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteVendor = (id: number) => {
    if (confirm('Are you sure you want to delete this vendor?')) {
        router.delete(route('vendors.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Vendors" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Briefcase class="w-6 h-6 text-indigo-500" />
                        Vendors Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all third-party vendors and partners.</p>
                </div>
                <button v-if="can('create_vendors')" @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New Vendor
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Vendors" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Vendor</th>
                                <th class="px-6 py-4">Contact Person</th>
                                <th class="px-6 py-4">Email / Phone</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="vendor in vendors.data" :key="vendor.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ vendor.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ vendor.vendor_code }}</div>
                                </td>
                                <td class="px-6 py-4">{{ vendor.contact_person || '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">{{ vendor.email || '-' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ vendor.phone || '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="vendor.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button v-if="can('edit_vendors')" @click="openEditModal(vendor)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button v-if="can('delete_vendors')" @click="deleteVendor(vendor.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!vendors.data || vendors.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No vendors found. Create one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="vendors.meta && vendors.meta.links" :links="vendors.meta.links" />
                <Pagination v-else-if="vendors.links" :links="vendors.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingVendor ? 'Edit Vendor' : 'New Vendor'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor Code <span class="text-rose-500">*</span></label>
                    <input v-model="form.vendor_code" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.vendor_code" class="mt-1" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor Name <span class="text-rose-500">*</span></label>
                    <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company</label>
                    <input v-model="form.company" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.company" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Person</label>
                    <input v-model="form.contact_person" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.contact_person" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.email" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                    <input v-model="form.phone" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.phone" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.notes" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Vendor' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
