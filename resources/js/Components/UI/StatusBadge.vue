<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: string;
}>();

const colorClass = computed(() => {
    const s = props.status.toLowerCase();
    if (['completed', 'active', 'approved', 'resolved', 'open'].includes(s)) {
        return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50';
    }
    if (['pending', 'in progress', 'under review', 'scheduled'].includes(s)) {
        return 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400 border-amber-200 dark:border-amber-800/50';
    }
    if (['cancelled', 'rejected', 'blocked', 'closed', 'high', 'critical'].includes(s)) {
        return 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400 border-rose-200 dark:border-rose-800/50';
    }
    if (['low', 'medium'].includes(s)) {
        return 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400 border-blue-200 dark:border-blue-800/50';
    }
    return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600';
});
</script>

<template>
    <span :class="[colorClass, 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border']">
        {{ status }}
    </span>
</template>
