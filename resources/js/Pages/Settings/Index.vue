<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Settings as SettingsIcon, Save } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    settings: Record<string, any[]>;
}>();

// Flat out settings for the form
const flatSettings = computed(() => {
    let arr: any[] = [];
    for (const group in props.settings) {
        arr = [...arr, ...props.settings[group]];
    }
    return arr;
});

const form = useForm({
    settings: flatSettings.value.map(s => ({ id: s.id, value: s.value, type: s.type }))
});

const submitSettings = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
    });
};

const getTitle = (key: string) => {
    return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<template>
    <Head title="System Settings" />

    <AppLayout>
        <div class="max-w-4xl mx-auto space-y-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <SettingsIcon class="w-6 h-6 text-indigo-500" />
                        System Settings
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure global application parameters and preferences.</p>
                </div>
                <button @click="submitSettings" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors disabled:opacity-50">
                    <Save class="w-4 h-4" />
                    {{ form.processing ? 'Saving...' : 'Save All Settings' }}
                </button>
            </div>

            <!-- Validation Errors -->
            <div v-if="form.hasErrors" class="bg-rose-50 border border-rose-200 text-rose-600 p-4 rounded-xl">
                Please check the form for errors before saving.
            </div>

            <!-- Settings Groups -->
            <BentoCard v-for="(groupSettings, groupName) in settings" :key="groupName" :title="groupName">
                <div class="space-y-6 max-w-2xl">
                    <div v-for="(setting, index) in groupSettings" :key="setting.id" class="flex flex-col md:flex-row md:items-center gap-2 md:gap-8 border-b border-gray-100 dark:border-gray-700/50 pb-4 last:border-0 last:pb-0">
                        <div class="md:w-1/3">
                            <label :for="'setting_' + setting.id" class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                {{ getTitle(setting.key) }}
                            </label>
                        </div>
                        <div class="md:w-2/3">
                            <!-- Dynamically find the index in the flat form array -->
                            <template v-if="form.settings.find(s => s.id === setting.id)">
                                <!-- Boolean Checkbox -->
                                <div v-if="setting.type === 'boolean'" class="flex items-center">
                                    <input :id="'setting_' + setting.id" type="checkbox"
                                        v-model="form.settings[form.settings.findIndex(s => s.id === setting.id)].value"
                                        true-value="1" false-value="0"
                                        class="h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Enabled</span>
                                </div>
                                
                                <!-- String/Number Input -->
                                <input v-else :id="'setting_' + setting.id" :type="setting.type === 'integer' ? 'number' : 'text'"
                                    v-model="form.settings[form.settings.findIndex(s => s.id === setting.id)].value"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </template>
                        </div>
                    </div>
                </div>
            </BentoCard>

        </div>
    </AppLayout>
</template>
