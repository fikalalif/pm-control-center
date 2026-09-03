<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ArrowRightLeft, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    changeRequests: any;
    projects: any[];
    users: any[];
}>();

const page = usePage();
const can = (permissionName: string) => {
    const roles = (page.props.auth as any).roles || [];
    const permissions = (page.props.auth as any).permissions || [];
    if (roles.includes('Admin')) return true;
    return permissions.includes(permissionName);
};

const showModal = ref(false);
const editingCR = ref<any>(null);

const form = useForm({
    project_id: '',
    cr_code: (page.props as any).global_settings?.change_request_code_prefix || 'CRQ-',
    title: '',
    description: '',
    requester_id: '',
    status: 'Pending',
    impact_analysis: '',
});

const openCreateModal = () => {
    editingCR.value = null;
    form.reset();
    form.clearErrors();
    form.cr_code = (page.props as any).global_settings?.change_request_code_prefix || 'CRQ-';
    showModal.value = true;
};

const openEditModal = (cr: any) => {
    editingCR.value = cr;
    form.clearErrors();
    form.project_id = cr.project_id;
    form.cr_code = cr.cr_code;
    form.title = cr.title;
    form.description = cr.description || '';
    form.requester_id = cr.requester_id || '';
    form.status = cr.status;
    form.impact_analysis = cr.impact_analysis || '';
    showModal.value = true;
};

const submitForm = () => {
    if (editingCR.value) {
        form.put(route('change-requests.update', editingCR.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('change-requests.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteItem = (id: number) => {
    if (confirm('Are you sure you want to delete this Change Request?')) {
        router.delete(route('change-requests.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Change Requests" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <ArrowRightLeft class="w-6 h-6 text-indigo-500" />
                        Global Change Requests
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola usulan perubahan scope untuk semua project.</p>
                </div>
                <button v-if="can('create_change_requests')" @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New CR
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Change Requests" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Change Request</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Requester</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="cr in changeRequests.data" :key="cr.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ cr.title }}</div>
                                    <div class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">{{ cr.cr_code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('projects.show', cr.project_id)" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        {{ cr.project?.project_code || 'Unknown Project' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div v-if="cr.requester" class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-bold">
                                            {{ cr.requester.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div v-else class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 flex items-center justify-center text-xs font-bold">
                                            ?
                                        </div>
                                        <span>{{ cr.requester?.name || 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="cr.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button v-if="can('edit_change_requests')" @click="openEditModal(cr)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button v-if="can('delete_change_requests')" @click="deleteItem(cr.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="changeRequests.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No change requests found. Log one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="changeRequests.meta && changeRequests.meta.links" :links="changeRequests.meta.links" />
                <Pagination v-else-if="changeRequests.links" :links="changeRequests.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingCR ? 'Edit Change Request' : 'New Change Request'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CR Code <span class="text-rose-500">*</span></label>
                    <input v-model="form.cr_code" type="text" placeholder="CR-001" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.cr_code" class="mt-1" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project <span class="text-rose-500">*</span></label>
                    <select v-model="form.project_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="" disabled>Select Project...</option>
                        <option v-for="prj in projects" :key="prj.id" :value="prj.id">{{ prj.project_code }} - {{ prj.name }}</option>
                    </select>
                    <InputError :message="form.errors.project_id" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">CR Title <span class="text-rose-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.title" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Requester</label>
                    <select v-model="form.requester_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Unknown</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    <InputError :message="form.errors.requester_id" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description <span class="text-rose-500">*</span></label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Impact Analysis</label>
                    <textarea v-model="form.impact_analysis" rows="3" placeholder="Dampak ke waktu/biaya..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.impact_analysis" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save CR' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
