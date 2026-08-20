<script setup lang="ts">
defineProps<{
    title?: string;
    description?: string;
    noPadding?: boolean;
}>();
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col transition-all duration-200">
        <div v-if="title || $slots.header" class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <div>
                <h3 v-if="title" class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">{{ title }}</h3>
                <p v-if="description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ description }}</p>
            </div>
            <div v-if="$slots.header">
                <slot name="header" />
            </div>
        </div>
        <div :class="['flex-1', { 'p-6': !noPadding }]">
            <slot />
        </div>
        <div v-if="$slots.footer" class="px-6 py-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700">
            <slot name="footer" />
        </div>
    </div>
</template>
