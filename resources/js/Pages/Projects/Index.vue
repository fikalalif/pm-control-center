<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import Pagination from '@/Components/Data/Pagination.vue';
// Tambahkan usePage di import ini
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { FolderKanban, Plus, Search, Edit, Trash2, ArrowRight } from 'lucide-vue-next';
import { debounce } from 'lodash-es';

const props = defineProps<{
    projects: any;
    filters?: {
        search?: string;
        status?: string;
    };
}>();

// --- SETUP PERMISSIONS LOGIC ---
const page = usePage();
const can = (permissionName: string) => {
    const roles = (page.props.auth as any).roles || [];
    const permissions = (page.props.auth as any).permissions || [];

    if (roles.includes('Admin')) return true; // Bypass untuk Admin
    return permissions.includes(permissionName);
};
// -------------------------------

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');

// Sync state if props.filters changes from URL navigation
watch(() => props.filters, (newFilters) => {
    if (newFilters) {
        search.value = newFilters.search || '';
        status.value = newFilters.status || '';
    }
}, { deep: true });

// Automatically filter when search/status changes
watch([search, status], debounce(([newSearch, newStatus]) => {
    router.get(
        route('projects.index'),
        { search: newSearch, status: newStatus },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}, 300));

const deleteProject = (id: number) => {
    if (confirm('Are you sure you want to delete this project? All associated tasks and records will be deleted.')) {
        router.delete(route('projects.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>

    <Head title="Projects" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <FolderKanban class="w-6 h-6 text-indigo-500" />
                        Project Control Center
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor and manage all active operations.
                    </p>
                </div>

                <!-- Gembok Tombol Create -->
                <Link v-if="can('create_projects')" :href="route('projects.create')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New Project
                </Link>
            </div>

            <!-- Filters & Search -->
            <div class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <input v-model="search" type="text" placeholder="Search by name or code..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors">
                </div>
                <select v-model="status"
                    class="block w-full md:w-48 pl-3 pr-10 py-2 text-base border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="On Hold">On Hold</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Table Section -->
            <BentoCard noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead
                            class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Project Name</th>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">PM</th>
                                <th class="px-6 py-4">Deadline</th>
                                <th class="px-6 py-4">Progress</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="project in projects.data" :key="project.id"
                                class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">
                                        <Link :href="route('projects.show', project.id)"
                                            class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                            {{ project.name }}
                                        </Link>
                                    </div>
                                    <div class="text-xs text-indigo-500 font-medium mt-0.5">{{ project.project_code }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ project.client?.name || '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2" v-if="project.project_manager">
                                        <div
                                            class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 flex items-center justify-center text-xs font-bold">
                                            {{ project.project_manager.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span>{{ project.project_manager.name }}</span>
                                    </div>
                                    <span v-else>-</span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ project.target_completion ||
                                    '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 max-w-[80px]">
                                            <div class="bg-indigo-500 h-1.5 rounded-full"
                                                :style="`width: ${project.progress_percentage}%`"></div>
                                        </div>
                                        <span class="text-xs font-medium w-8">{{ project.progress_percentage }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <StatusBadge :status="project.status" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a :href="route('projects.export.pdf', project.id)" target="_blank"
                                            class="text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors"
                                            title="Export to PDF">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                        </a>

                                        <Link :href="route('projects.show', project.id)"
                                            class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                            title="View Detail">
                                            <ArrowRight class="w-4 h-4" />
                                        </Link>

                                        <!-- Gembok Tombol Edit -->
                                        <Link v-if="can('edit_projects')" :href="route('projects.edit', project.id)"
                                            class="text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 transition-colors"
                                            title="Edit">
                                            <Edit class="w-4 h-4" />
                                        </Link>

                                        <!-- Gembok Tombol Delete -->
                                        <button v-if="can('delete_projects')" @click="deleteProject(project.id)"
                                            class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors"
                                            title="Delete">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="projects.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <FolderKanban class="w-8 h-8 mb-3 opacity-20" />
                                        <p>No projects found matching your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="projects.links" />
            </BentoCard>
        </div>
    </AppLayout>
</template>
