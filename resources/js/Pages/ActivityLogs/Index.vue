<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import { Head } from '@inertiajs/vue3';
import { FolderKanban, CheckSquare, AlertTriangle, Calendar, Activity } from 'lucide-vue-next';

defineProps<{
    activities: any[];
}>();

const getIcon = (type: string) => {
    switch (type) {
        case 'project': return FolderKanban;
        case 'task': return CheckSquare;
        case 'risk': return AlertTriangle;
        case 'meeting': return Calendar;
        default: return Activity;
    }
};

const getColor = (type: string) => {
    switch (type) {
        case 'project': return 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400';
        case 'task': return 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400';
        case 'risk': return 'bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400';
        case 'meeting': return 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400';
        default: return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
    }
};
</script>

<template>
    <Head title="Activity Log" />

    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Activity class="w-6 h-6 text-indigo-500" />
                        System Activity Log
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor all system events and user actions across projects.</p>
                </div>
            </div>

            <!-- Timeline -->
            <BentoCard noPadding class="p-8">
                <div v-if="activities && activities.length > 0" class="relative border-l-2 border-gray-100 dark:border-gray-700 ml-4 space-y-8">
                    <div v-for="activity in activities" :key="activity.id" class="relative pl-8">
                        <div :class="['absolute -left-[17px] w-8 h-8 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-800', getColor(activity.type)]">
                            <component :is="getIcon(activity.type)" class="w-4 h-4" />
                        </div>
                        <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                <span class="font-bold text-gray-900 dark:text-white">{{ activity.user }}</span>
                                <span class="mx-1">{{ activity.action }}</span>
                                <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ activity.target }}</span>
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 font-medium">{{ activity.date }}</p>
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-center py-12">
                    <Activity class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No activity yet</h3>
                    <p class="text-sm text-gray-500 mt-1">Check back later when users start interacting with the system.</p>
                </div>

                <div v-if="activities && activities.length > 0" class="mt-8 text-center pt-6 border-t border-gray-100 dark:border-gray-700/50">
                    <button class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                        Load More Activities
                    </button>
                </div>
            </BentoCard>

        </div>
    </AppLayout>
</template>
