<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import { Head } from '@inertiajs/vue3';
import { Activity, Plus, Edit, Trash2, Calendar, Layers } from 'lucide-vue-next';

defineProps<{
    activities: {
        data: Array<{
            id: number;
            log_name: string;
            description: string;
            subject_type: string | null;
            event: string | null;
            subject_id: number | null;
            causer_type: string | null;
            causer_id: number | null;
            properties: any;
            created_at: string;
            causer?: {
                id: number;
                name: string;
                email: string;
            } | null;
            subject?: any;
        }>;
        links: any[];
    };
}>();

const formatRelativeTime = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now.getTime() - date.getTime()) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
    const diffInMonths = Math.floor(diffInDays / 30);
    if (diffInMonths < 12) return `${diffInMonths} month${diffInMonths > 1 ? 's' : ''} ago`;
    const diffInYears = Math.floor(diffInDays / 365);
    return `${diffInYears} year${diffInYears > 1 ? 's' : ''} ago`;
};

const getSubjectName = (subjectType: string | null) => {
    if (!subjectType) return 'System';
    const parts = subjectType.split('\\');
    return parts[parts.length - 1];
};

const getEventTheme = (event: string | null, description: string) => {
    const action = (event || description || '').toLowerCase();
    if (action.includes('create')) {
        return {
            icon: Plus,
            nodeColor: 'bg-emerald-600 text-white ring-emerald-100 dark:ring-emerald-950',
            badgeColor: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/80',
            label: 'Created'
        };
    }
    if (action.includes('update') || action.includes('edit')) {
        return {
            icon: Edit,
            nodeColor: 'bg-blue-600 text-white ring-blue-100 dark:ring-blue-950',
            badgeColor: 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border-blue-200 dark:border-blue-800/80',
            label: 'Updated'
        };
    }
    if (action.includes('delete') || action.includes('destroy')) {
        return {
            icon: Trash2,
            nodeColor: 'bg-rose-600 text-white ring-rose-100 dark:ring-rose-950',
            badgeColor: 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800/80',
            label: 'Deleted'
        };
    }
    return {
        icon: Activity,
        nodeColor: 'bg-indigo-600 text-white ring-indigo-100 dark:ring-indigo-950',
        badgeColor: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/80',
        label: description || 'Action'
    };
};

const getChangedAttributes = (activity: any) => {
    return activity.properties?.attributes || activity.attribute_changes?.attributes || null;
};

const getOldAttributes = (activity: any) => {
    return activity.properties?.old || activity.attribute_changes?.old || null;
};

const getSubjectTitle = (activity: any) => {
    if (activity.subject) {
        return activity.subject.name || activity.subject.title || activity.subject.task_code || activity.subject.project_code || activity.subject.risk_code || '';
    }
    const attrs = getChangedAttributes(activity);
    if (attrs?.name || attrs?.title || attrs?.project_code || attrs?.task_code) {
        return attrs.name || attrs.title || attrs.project_code || attrs.task_code;
    }
    const oldAttrs = getOldAttributes(activity);
    if (oldAttrs?.name || oldAttrs?.title || oldAttrs?.project_code || oldAttrs?.task_code) {
        return oldAttrs.name || oldAttrs.title || oldAttrs.project_code || oldAttrs.task_code;
    }
    return '';
};
</script>

<template>
    <Head title="Activity Log" />

    <AppLayout>
        <div class="max-w-5xl mx-auto space-y-6">

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Activity class="w-6 h-6 text-indigo-500" />
                        System Activity Log
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Audit trail and comprehensive timeline of data changes across all modules.
                    </p>
                </div>
            </div>

            <!-- Main Vertical Timeline Card -->
            <BentoCard noPadding>
                <div class="p-6 md:p-8">
                    <div v-if="activities.data && activities.data.length > 0" class="relative pl-6 md:pl-8 border-l-2 border-gray-200 dark:border-gray-700 space-y-8 my-2">
                        <div v-for="activity in activities.data" :key="activity.id" class="relative group">
                            <!-- Timeline Node Icon -->
                            <div :class="[
                                'absolute -left-[37px] md:-left-[45px] top-1.5 w-8 h-8 rounded-full flex items-center justify-center ring-4 ring-white dark:ring-gray-900 shadow-sm transition-transform group-hover:scale-110',
                                getEventTheme(activity.event, activity.description).nodeColor
                            ]">
                                <component :is="getEventTheme(activity.event, activity.description).icon" class="w-4 h-4" />
                            </div>

                            <!-- Timeline Content Card -->
                            <div class="bg-gray-50/70 dark:bg-gray-800/40 rounded-2xl p-5 border border-gray-100 dark:border-gray-700/60 shadow-xs hover:border-indigo-200 dark:hover:border-indigo-800/60 transition-all">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <!-- User / Causer -->
                                        <div class="flex items-center gap-1.5 font-bold text-gray-900 dark:text-white text-sm">
                                            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                                                {{ (activity.causer?.name || 'S').charAt(0).toUpperCase() }}
                                            </div>
                                            <span>{{ activity.causer?.name || 'System' }}</span>
                                        </div>

                                        <!-- Action Badge -->
                                        <span :class="[
                                            'px-2.5 py-0.5 rounded-full text-xs font-semibold border',
                                            getEventTheme(activity.event, activity.description).badgeColor
                                        ]">
                                            {{ getEventTheme(activity.event, activity.description).label }}
                                        </span>

                                        <!-- Subject Type & Title -->
                                        <div class="flex items-center gap-1 text-sm text-gray-700 dark:text-gray-300">
                                            <span class="font-medium text-gray-500 dark:text-gray-400">{{ getSubjectName(activity.subject_type) }}</span>
                                            <span v-if="getSubjectTitle(activity)" class="font-bold text-gray-900 dark:text-white">
                                                "{{ getSubjectTitle(activity) }}"
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Relative Timestamp -->
                                    <div class="text-xs text-gray-400 dark:text-gray-500 font-medium whitespace-nowrap flex items-center gap-1">
                                        <Calendar class="w-3.5 h-3.5" />
                                        <span>{{ formatRelativeTime(activity.created_at) }}</span>
                                    </div>
                                </div>

                                <!-- Changed Properties Details (If Available) -->
                                <div v-if="getChangedAttributes(activity)" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                                        <Layers class="w-3.5 h-3.5 text-indigo-500" />
                                        Changed Attributes:
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <div v-for="(val, key) in (getChangedAttributes(activity) || {})" :key="key" class="text-xs px-2.5 py-1 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                            <span class="font-medium text-gray-500 dark:text-gray-400">{{ key }}:</span>
                                            <span v-if="getOldAttributes(activity) && getOldAttributes(activity)[key] !== undefined" class="text-rose-500 line-through ml-1">{{ getOldAttributes(activity)[key] }}</span>
                                            <span v-if="getOldAttributes(activity) && getOldAttributes(activity)[key] !== undefined" class="mx-1 text-gray-400">→</span>
                                            <span class="font-semibold text-emerald-600 dark:text-emerald-400 ml-1">{{ val }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-16">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-500 flex items-center justify-center mx-auto mb-4">
                            <Activity class="w-8 h-8" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">No activity logged yet</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm mx-auto">
                            User actions like creating, updating, or deleting records will automatically appear here on this timeline.
                        </p>
                    </div>
                </div>

                <!-- Pagination Footer -->
                <Pagination v-if="activities.links" :links="activities.links" />
            </BentoCard>

        </div>
    </AppLayout>
</template>
