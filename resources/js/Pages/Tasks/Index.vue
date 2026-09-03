<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { CheckSquare, Plus, Search, Filter, MoreVertical, Edit, Trash2 } from 'lucide-vue-next';
import { debounce } from 'lodash-es';

const props = defineProps<{
    tasks: any;
    projects: any[];
    users: any[];
    vendors: any[];
    filters?: {
        search?: string;
        status?: string;
    };
}>();

const page = usePage();
const can = (permissionName: string) => {
    const roles = (page.props.auth as any).roles || [];
    const permissions = (page.props.auth as any).permissions || [];
    if (roles.includes('Admin')) return true;
    return permissions.includes(permissionName);
};

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

// Sync state if props.filters changes from URL navigation
watch(() => props.filters, (newFilters) => {
    if (newFilters) {
        search.value = newFilters.search || '';
        status.value = newFilters.status || '';
    }
}, { deep: true });

watch([search, status], debounce(([newSearch, newStatus]) => {
    router.get(
        route('tasks.index'),
        { search: newSearch, status: newStatus },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}, 300));

const showModal = ref(false);
const editingTask = ref<any>(null);

const form = useForm({
    task_code: (page.props as any).global_settings?.task_code_prefix || 'TSK-',
    project_id: '',
    name: '',
    description: '',
    assigned_user_id: '',
    vendor_id: '',
    start_date: '',
    deadline: '',
    status: 'Pending',
    progress_percentage: 0,
});

const openCreateModal = () => {
    editingTask.value = null;
    form.reset();
    form.clearErrors();
    form.task_code = (page.props as any).global_settings?.task_code_prefix || 'TSK-';
    showModal.value = true;
};

const openEditModal = (task: any) => {
    editingTask.value = task;
    form.clearErrors();
    form.task_code = task.task_code;
    form.project_id = task.project_id;
    form.name = task.name;
    form.description = task.description || '';
    form.assigned_user_id = task.assigned_user_id || '';
    form.vendor_id = task.vendor_id || '';
    form.start_date = task.start_date || '';
    form.deadline = task.deadline || '';
    form.status = task.status;
    form.progress_percentage = task.progress_percentage || 0;
    showModal.value = true;
};

const submitForm = () => {
    if (editingTask.value) {
        form.put(route('tasks.update', editingTask.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('tasks.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteTask = (id: number) => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(route('tasks.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Tasks" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <CheckSquare class="w-6 h-6 text-indigo-500" />
                        Tasks Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and track all project tasks globally.</p>
                </div>
                <button v-if="can('create_tasks')" @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New Task
                </button>
            </div>

            <!-- Filters & Search -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <input v-model="search" type="text" placeholder="Search task name or code..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                </div>
                <select v-model="status"
                    class="block w-full md:w-48 pl-3 pr-10 py-2 text-base border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Tasks" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Task</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Assignee</th>
                                <th class="px-6 py-4">Deadline</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ task.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ task.task_code }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('projects.show', task.project_id)" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        {{ task.project?.project_code || 'Unknown Project' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div v-if="task.assigned_user" class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-bold">
                                            {{ task.assigned_user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div v-else class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 flex items-center justify-center text-xs font-bold">
                                            ?
                                        </div>
                                        <span>{{ task.assigned_user?.name || 'Unassigned' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ task.deadline || '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="task.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button v-if="can('edit_tasks')" @click="openEditModal(task)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button v-if="can('delete_tasks')" @click="deleteTask(task.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="tasks.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No tasks found. Create one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="tasks.meta && tasks.meta.links" :links="tasks.meta.links" />
                <Pagination v-else-if="tasks.links" :links="tasks.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingTask ? 'Edit Task' : 'New Task'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Task Code <span class="text-rose-500">*</span></label>
                    <input v-model="form.task_code" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.task_code" class="mt-1" />
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Task Name <span class="text-rose-500">*</span></label>
                    <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assignee</label>
                    <select v-model="form.assigned_user_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Unassigned</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    <InputError :message="form.errors.assigned_user_id" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                        <option value="Blocked">Blocked</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input v-model="form.start_date" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.start_date" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deadline</label>
                    <input v-model="form.deadline" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.deadline" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Task' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
