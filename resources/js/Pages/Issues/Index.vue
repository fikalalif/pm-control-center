<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Bug, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    issues: any;
    projects: any[];
    users: any[];
}>();

const showModal = ref(false);
const editingIssue = ref<any>(null);

const form = useForm({
    project_id: '',
    issue_code: '',
    title: '',
    impact: 'Medium',
    owner_id: '',
    status: 'Open',
    action: '',
    deadline: '',
});

const openCreateModal = () => {
    editingIssue.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (issue: any) => {
    editingIssue.value = issue;
    form.clearErrors();
    form.project_id = issue.project_id;
    form.issue_code = issue.issue_code;
    form.title = issue.title;
    form.impact = issue.impact;
    form.owner_id = issue.owner_id || '';
    form.status = issue.status;
    form.action = issue.action || '';
    form.deadline = issue.deadline || '';
    showModal.value = true;
};

const submitForm = () => {
    if (editingIssue.value) {
        form.put(route('issues.update', editingIssue.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('issues.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteItem = (id: number) => {
    if (confirm('Are you sure you want to delete this issue?')) {
        router.delete(route('issues.destroy', id), { preserveScroll: true });
    }
};

const getImpactColor = (level: string) => {
    switch(level) {
        case 'High': return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400';
        case 'Medium': return 'text-orange-600 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400';
        case 'Low': return 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400';
        default: return 'text-gray-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-400';
    }
};
</script>

<template>
    <Head title="Issues Management" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Bug class="w-6 h-6 text-indigo-500" />
                        Global Issues Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau masalah yang terjadi di seluruh project.</p>
                </div>
                <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    Log Issue
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Issues" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Issue</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Owner</th>
                                <th class="px-6 py-4">Impact</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="issue in issues.data" :key="issue.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ issue.title }}</div>
                                    <div class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">{{ issue.issue_code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('projects.show', issue.project_id)" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        {{ issue.project?.project_code || 'Unknown Project' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div v-if="issue.owner" class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-bold">
                                            {{ issue.owner.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div v-else class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 flex items-center justify-center text-xs font-bold">
                                            ?
                                        </div>
                                        <span>{{ issue.owner?.name || 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2.5 py-1 rounded-md text-xs font-bold', getImpactColor(issue.impact)]">
                                        {{ issue.impact }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="issue.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openEditModal(issue)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button @click="deleteItem(issue.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="issues.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No issues logged. Log one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="issues.meta && issues.meta.links" :links="issues.meta.links" />
                <Pagination v-else-if="issues.links" :links="issues.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingIssue ? 'Edit Issue' : 'Log Issue'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Code <span class="text-rose-500">*</span></label>
                    <input v-model="form.issue_code" type="text" placeholder="ISS-001" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.issue_code" class="mt-1" />
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Title <span class="text-rose-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.title" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Impact Level</label>
                    <select v-model="form.impact" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                    <InputError :message="form.errors.impact" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Owner / PIC</label>
                    <select v-model="form.owner_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Unassigned</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    <InputError :message="form.errors.owner_id" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-1" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deadline</label>
                    <input v-model="form.deadline" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.deadline" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Corrective Action</label>
                    <textarea v-model="form.action" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.action" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Issue' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
