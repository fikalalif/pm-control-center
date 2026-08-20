<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { AlertTriangle, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    risks: any;
    projects: any[];
    users: any[];
}>();

const showModal = ref(false);
const editingRisk = ref<any>(null);

const form = useForm({
    project_id: '',
    risk_code: '',
    title: '',
    risk_level: 'Low',
    owner_id: '',
    status: 'Open',
    mitigation: '',
});

const openCreateModal = () => {
    editingRisk.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (risk: any) => {
    editingRisk.value = risk;
    form.clearErrors();
    form.project_id = risk.project_id;
    form.risk_code = risk.risk_code;
    form.title = risk.title;
    form.risk_level = risk.risk_level;
    form.owner_id = risk.owner_id || '';
    form.status = risk.status;
    form.mitigation = risk.mitigation || '';
    showModal.value = true;
};

const submitForm = () => {
    if (editingRisk.value) {
        form.put(route('risks.update', editingRisk.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('risks.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteItem = (id: number) => {
    if (confirm('Are you sure you want to delete this risk?')) {
        router.delete(route('risks.destroy', id), { preserveScroll: true });
    }
};

const getRiskColor = (level: string) => {
    switch(level) {
        case 'Critical': return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400';
        case 'High': return 'text-orange-600 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400';
        case 'Medium': return 'text-amber-600 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400';
        case 'Low': return 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400';
        default: return 'text-gray-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-400';
    }
};
</script>

<template>
    <Head title="Risks Management" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <AlertTriangle class="w-6 h-6 text-indigo-500" />
                        Global Risks Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau dan mitigasi risiko seluruh project.</p>
                </div>
                <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    Log Risk
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Risks" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Risk</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Owner</th>
                                <th class="px-6 py-4">Level</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="risk in risks.data" :key="risk.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ risk.title }}</div>
                                    <div class="text-xs text-indigo-600 dark:text-indigo-400 mt-0.5">{{ risk.risk_code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('projects.show', risk.project_id)" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        {{ risk.project?.project_code || 'Unknown Project' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div v-if="risk.owner" class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-bold">
                                            {{ risk.owner.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div v-else class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 flex items-center justify-center text-xs font-bold">
                                            ?
                                        </div>
                                        <span>{{ risk.owner?.name || 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['px-2.5 py-1 rounded-md text-xs font-bold', getRiskColor(risk.risk_level)]">
                                        {{ risk.risk_level }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="risk.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openEditModal(risk)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button @click="deleteItem(risk.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="risks.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No risks logged. Log one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="risks.meta && risks.meta.links" :links="risks.meta.links" />
                <Pagination v-else-if="risks.links" :links="risks.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingRisk ? 'Edit Risk' : 'Log Risk'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Risk Code <span class="text-rose-500">*</span></label>
                    <input v-model="form.risk_code" type="text" placeholder="RSK-001" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.risk_code" class="mt-1" />
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Risk Title <span class="text-rose-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.title" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Risk Level</label>
                    <select v-model="form.risk_level" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                    <InputError :message="form.errors.risk_level" class="mt-1" />
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

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mitigation Plan</label>
                    <textarea v-model="form.mitigation" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.mitigation" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Risk' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
