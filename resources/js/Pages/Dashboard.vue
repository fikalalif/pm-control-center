<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    FolderKanban, CheckSquare, AlertTriangle,
    Flag, ArrowRight, Activity, Calendar,
    TrendingUp, ShieldAlert, CheckCircle2
} from 'lucide-vue-next';

defineProps<{
    stats: {
        total_projects: number;
        active_projects: number;
        total_tasks: number;
        pending_tasks: number;
        critical_risks: number;
        upcoming_milestones: number;
    };
    recent_projects: any[];
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Welcome Banner -->
            <div class="bg-indigo-600 dark:bg-indigo-900 rounded-2xl p-6 md:p-8 text-white shadow-sm flex flex-col md:flex-row justify-between items-center gap-4 relative overflow-hidden">
                <!-- Decorative background pattern -->
                <div class="absolute inset-0 opacity-10 pattern-dots pattern-indigo-500 pattern-bg-white pattern-size-4 dark:pattern-bg-black"></div>

                <div class="relative z-10">
                    <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Welcome back, {{ $page.props.auth.user.name }}! 👋</h2>
                    <p class="text-indigo-100 mt-2 text-sm md:text-base max-w-2xl">Here is your daily operational briefing. Monitor project health, upcoming deadlines, and critical items requiring your attention.</p>
                </div>
                <div class="relative z-10">
                    <Link :href="route('projects.create')" class="bg-white text-indigo-600 hover:bg-indigo-50 px-5 py-2.5 rounded-xl font-bold text-sm transition-colors whitespace-nowrap shadow-sm flex items-center gap-2">
                        <span>+</span> New Project
                    </Link>
                </div>
            </div>

            <!-- Bento Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                <BentoCard class="group cursor-default hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Active Projects</p>
                            <div class="flex items-baseline gap-2 mt-2">
                                <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.active_projects }}</h3>
                                <span class="text-xs font-medium text-gray-400">/ {{ stats.total_projects }} total</span>
                            </div>
                        </div>
                        <div class="p-3 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl group-hover:scale-110 transition-transform">
                            <FolderKanban class="w-6 h-6" />
                        </div>
                    </div>
                </BentoCard>

                <BentoCard class="group cursor-default hover:border-amber-200 dark:hover:border-amber-800 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Pending Tasks</p>
                            <div class="flex items-baseline gap-2 mt-2">
                                <h3 class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.pending_tasks }}</h3>
                                <span class="text-xs font-medium text-gray-400">/ {{ stats.total_tasks }} total</span>
                            </div>
                        </div>
                        <div class="p-3 bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 rounded-xl group-hover:scale-110 transition-transform">
                            <CheckSquare class="w-6 h-6" />
                        </div>
                    </div>
                </BentoCard>

                <BentoCard class="group cursor-default hover:border-emerald-200 dark:hover:border-emerald-800 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Upcoming Milestones</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ stats.upcoming_milestones }}</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl group-hover:scale-110 transition-transform">
                            <Flag class="w-6 h-6" />
                        </div>
                    </div>
                </BentoCard>

                <BentoCard class="group cursor-default hover:border-rose-200 dark:hover:border-rose-800 transition-colors">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Critical Risks</p>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-2">{{ stats.critical_risks }}</h3>
                        </div>
                        <div class="p-3 bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-xl group-hover:scale-110 transition-transform">
                            <ShieldAlert class="w-6 h-6" />
                        </div>
                    </div>
                </BentoCard>

            </div>

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Recent Projects Overview -->
                <BentoCard title="Recent Projects" noPadding class="lg:col-span-2">
                    <template #header>
                        <Link :href="route('projects.index')" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                            View all
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </template>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                            <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                                <tr>
                                    <th class="px-6 py-4">Project</th>
                                    <th class="px-6 py-4">Client</th>
                                    <th class="px-6 py-4">Progress</th>
                                    <th class="px-6 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <tr v-for="project in recent_projects" :key="project.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">
                                            <Link :href="route('projects.show', project.id)" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                {{ project.name }}
                                            </Link>
                                        </div>
                                        <div class="text-xs text-gray-500 font-medium mt-0.5">{{ project.project_code }}</div>
                                    </td>
                                    <td class="px-6 py-4">{{ project.client?.name || '-' }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full bg-gray-100 dark:bg-gray-600 rounded-full h-1.5 max-w-[80px]">
                                                <div class="bg-indigo-500 h-1.5 rounded-full" :style="`width: ${project.progress_percentage}%`"></div>
                                            </div>
                                            <span class="text-xs font-medium">{{ project.progress_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <StatusBadge :status="project.status" />
                                    </td>
                                </tr>
                                <tr v-if="!recent_projects || recent_projects.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No projects started yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </BentoCard>

                <!-- Quick Actions / Action Center -->
                <BentoCard title="Action Center" description="Jump quickly to key modules">
                    <div class="space-y-3">
                        <Link :href="route('tasks.index')" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-lg group-hover:scale-110 transition-transform">
                                <CheckCircle2 class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Manage Tasks</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Update assignments & progress</p>
                            </div>
                        </Link>

                        <Link :href="route('meetings.index')" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-lg group-hover:scale-110 transition-transform">
                                <Calendar class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Schedule Meetings</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Plan project syncing</p>
                            </div>
                        </Link>

                        <Link :href="route('activity-log.index')" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <div class="p-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg group-hover:scale-110 transition-transform">
                                <Activity class="w-5 h-5" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Activity Log</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">View recent system history</p>
                            </div>
                        </Link>
                    </div>
                </BentoCard>

            </div>

        </div>
    </AppLayout>
</template>
