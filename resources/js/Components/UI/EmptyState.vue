<script setup lang="ts">
import { defineComponent } from 'vue';

defineProps<{
    title: string;
    description: string;
    icon?: any;
}>();
</script>

<template>
    <div class="flex flex-col items-center justify-center p-12 text-center bg-gray-50/50 dark:bg-gray-800/20 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
        <div v-if="icon" class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full mb-4 text-gray-400 dark:text-gray-500">
            <component :is="icon" class="w-8 h-8" />
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ title }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-sm">{{ description }}</p>
        <div v-if="$slots.action" class="mt-6">
            <slot name="action" />
        </div>
    </div>
</template>
