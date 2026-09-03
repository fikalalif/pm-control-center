<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Calendar, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    meetings: any;
    projects: any[];
}>();

const page = usePage();
const can = (permissionName: string) => {
    const roles = (page.props.auth as any).roles || [];
    const permissions = (page.props.auth as any).permissions || [];
    if (roles.includes('Admin')) return true;
    return permissions.includes(permissionName);
};

const showModal = ref(false);
const editingMeeting = ref<any>(null);

const form = useForm({
    project_id: '',
    type: 'External',
    title: '',
    description: '',
    meeting_date: '',
    due_date: '',
    status: 'Scheduled',
});

const openCreateModal = () => {
    editingMeeting.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (meeting: any) => {
    editingMeeting.value = meeting;
    form.clearErrors();
    form.project_id = meeting.project_id || '';
    form.type = meeting.type || 'External';
    form.title = meeting.title || '';
    form.description = meeting.description || '';
    form.meeting_date = meeting.meeting_date ? meeting.meeting_date.slice(0,16) : '';
    form.due_date = meeting.due_date ? meeting.due_date.slice(0,16) : '';
    form.status = meeting.status || 'Scheduled';
    showModal.value = true;
};

const submitForm = () => {
    if (editingMeeting.value) {
        form.put(route('meetings.update', editingMeeting.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('meetings.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteMeeting = (id: number) => {
    if (confirm('Are you sure you want to delete this meeting?')) {
        router.delete(route('meetings.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Meetings" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Calendar class="w-6 h-6 text-indigo-500" />
                        Meetings Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and track all global meetings.</p>
                </div>
                <button v-if="can('create_meetings')" @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New Meeting
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Meetings" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Project</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Date & Time</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="meeting in meetings.data" :key="meeting.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ meeting.title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <Link :href="route('projects.show', meeting.project_id)" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                        {{ meeting.project?.project_code || 'Unknown Project' }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    {{ meeting.type }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                    {{ meeting.meeting_date || '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="meeting.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button v-if="can('edit_meetings')" @click="openEditModal(meeting)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Edit">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button v-if="can('delete_meetings')" @click="deleteMeeting(meeting.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!meetings.data || meetings.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No meetings found. Create one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="meetings.meta && meetings.meta.links" :links="meetings.meta.links" />
                <Pagination v-else-if="meetings.links" :links="meetings.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingMeeting ? 'Edit Meeting' : 'New Meeting'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project <span class="text-rose-500">*</span></label>
                    <select v-model="form.project_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="" disabled>Select Project...</option>
                        <option v-for="prj in projects" :key="prj.id" :value="prj.id">{{ prj.project_code }} - {{ prj.name }}</option>
                    </select>
                    <InputError :message="form.errors.project_id" class="mt-1" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type <span class="text-rose-500">*</span></label>
                    <select v-model="form.type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Internal">Internal</option>
                        <option value="External">External</option>
                        <option value="Client">Client</option>
                    </select>
                    <InputError :message="form.errors.type" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meeting Title <span class="text-rose-500">*</span></label>
                    <input v-model="form.title" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.title" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.description" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Meeting Date</label>
                    <input v-model="form.meeting_date" type="datetime-local" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.meeting_date" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
                    <input v-model="form.due_date" type="datetime-local" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.due_date" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <InputError :message="form.errors.status" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Meeting' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
