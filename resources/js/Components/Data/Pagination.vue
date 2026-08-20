<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: any[];
}>();
</script>

<template>
    <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 sm:px-6">
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm bg-white dark:bg-gray-800" aria-label="Pagination">
                    <template v-for="(link, k) in links" :key="k">
                        <div v-if="link.url === null" 
                            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-400 dark:text-gray-500 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:z-20 focus:outline-offset-0 cursor-not-allowed" 
                            v-html="link.label">
                        </div>
                        <Link v-else 
                            :href="link.url"
                            :class="[
                                link.active ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:z-20 focus:outline-offset-0',
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
                            ]"
                            v-html="link.label" />
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>
