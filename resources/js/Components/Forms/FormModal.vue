<script setup lang="ts">
import Modal from '@/Components/Modal.vue';

defineProps<{
    show: boolean;
    title: string;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
}>();

const emit = defineEmits(['close', 'submit']);
</script>

<template>
    <Modal :show="show" :max-width="maxWidth" @close="$emit('close')">
        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ title }}</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            
            <form @submit.prevent="$emit('submit')">
                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    <slot />
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <slot name="actions">
                        <button type="button" @click="$emit('close')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save
                        </button>
                    </slot>
                </div>
            </form>
        </div>
    </Modal>
</template>
