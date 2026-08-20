<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Menerima data master untuk dropdown
defineProps<{
    clients: any[];
    users: any[];
    types: any[];
    phases: any[];
}>();

const form = useForm({
    project_code: '',
    name: '',
    client_id: '',
    project_type_id: '',
    project_manager_id: '',
    current_phase_id: '',
    start_date: '',
    target_completion: '',
    priority: 'Medium',
    status: 'Pending',
    description: '',
});

const submit = () => {
    form.post(route('projects.store'));
};
</script>

<template>
    <Head title="Create Project" />

    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">

            <div class="flex items-center gap-4 mb-6">
                <Link :href="route('projects.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    &larr; Back to Projects
                </Link>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Create New Project</h2>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Project Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Code *</label>
                            <input v-model="form.project_code" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Ex: PRJ-001">
                            <div v-if="form.errors.project_code" class="text-red-500 text-sm mt-1">{{ form.errors.project_code }}</div>
                        </div>

                        <!-- Project Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Name *</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Ex: Website Revamp">
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <!-- Client Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client *</label>
                            <select v-model="form.client_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="" disabled>Select Client</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.name }}</option>
                            </select>
                            <div v-if="form.errors.client_id" class="text-red-500 text-sm mt-1">{{ form.errors.client_id }}</div>
                        </div>

                        <!-- PM Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Manager *</label>
                            <select v-model="form.project_manager_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="" disabled>Select PM</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>

                        <!-- Dates -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                            <input v-model="form.start_date" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Completion *</label>
                            <input v-model="form.target_completion" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <div v-if="form.errors.target_completion" class="text-red-500 text-sm mt-1">{{ form.errors.target_completion }}</div>
                        </div>

                        <!-- Type & Phase Dropdowns -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Project Type</label>
                            <select v-model="form.project_type_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Type</option>
                                <option v-for="ptype in types" :key="ptype.id" :value="ptype.id">{{ ptype.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Phase</label>
                            <select v-model="form.current_phase_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Phase</option>
                                <option v-for="phase in phases" :key="phase.id" :value="phase.id">{{ phase.name }}</option>
                            </select>
                        </div>

                        <!-- Priority & Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                            <select v-model="form.priority" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select v-model="form.status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="On Hold">On Hold</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea v-model="form.description" rows="4" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Detail scope project..."></textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50">
                            Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
