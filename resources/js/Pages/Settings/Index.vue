<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Settings as SettingsIcon, Save, Info } from 'lucide-vue-next';
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

const getDescription = (key: string) => {
    const descriptions: Record<string, string> = {
        'company_name': 'Nama resmi perusahaan untuk keperluan report PDF.',
        'timezone': 'Zona waktu standar untuk seluruh aktivitas project.',
        'default_theme': 'Pilih nuansa tampilan aplikasi bawaan.',
        'auto_collapse_sidebar': 'Sembunyikan sidebar otomatis untuk ruang kerja yang lebih luas.',
        'email_alerts': 'Kirim notifikasi aktivitas penting via email.',
        'slack_webhook_url': 'URL webhook untuk integrasi notifikasi ke channel Slack.',
        'project_id_prefix': 'Awalan kode unik untuk project baru (misal: PRJ-).',
    };
    return descriptions[key] || '';
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
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure global application parameters and
                        preferences.</p>
                </div>
                <button @click="submitSettings" :disabled="form.processing"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors disabled:opacity-50">
                    <Save class="w-4 h-4" />
                    {{ form.processing ? 'Saving...' : 'Save All Settings' }}
                </button>
            </div>

            <!-- Validation Errors -->
            <div v-if="form.hasErrors" class="bg-rose-50 border border-rose-200 text-rose-600 p-4 rounded-xl">
                Please check the form for errors before saving.
            </div>

            <!-- Settings Groups (Bento Boxes) -->
            <div class="grid grid-cols-1 gap-6">
                <BentoCard v-for="(groupSettings, groupName) in settings" :key="groupName" :title="groupName">
                    <div class="space-y-6">
                        <div v-for="(setting, index) in groupSettings" :key="setting.id"
                            class="flex flex-col md:flex-row md:items-start gap-2 md:gap-8 border-b border-gray-100 dark:border-gray-700/50 pb-5 last:border-0 last:pb-0">

                            <!-- Label & Description -->
                            <div class="md:w-5/12 pt-1">
                                <label :for="'setting_' + setting.id"
                                    class="block text-sm font-bold text-gray-700 dark:text-gray-300">
                                    {{ getTitle(setting.key) }}
                                </label>
                                <p v-if="getDescription(setting.key)"
                                    class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ getDescription(setting.key) }}
                                </p>
                            </div>

                            <!-- Inputs -->
                            <div class="md:w-7/12">
                                <template v-if="form.settings.find(s => s.id === setting.id)">

                                    <!-- Boolean Checkbox -->
                                    <div v-if="setting.type === 'boolean'" class="flex items-center pt-1">
                                        <input :id="'setting_' + setting.id" type="checkbox"
                                            v-model="form.settings[form.settings.findIndex(s => s.id === setting.id)].value"
                                            true-value="1" false-value="0"
                                            class="h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Enabled</span>
                                    </div>

                                    <!-- Timezone Dropdown -->
                                    <select v-else-if="setting.type === 'timezone'" :id="'setting_' + setting.id"
                                        v-model="form.settings[form.settings.findIndex(s => s.id === setting.id)].value"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm">
                                        <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                        <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                        <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                    </select>

                                    <!-- Theme Dropdown -->
                                    <select v-else-if="setting.type === 'theme'" :id="'setting_' + setting.id"
                                        v-model="form.settings[form.settings.findIndex(s => s.id === setting.id)].value"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm">
                                        <option value="system">System Default</option>
                                        <option value="light">Light Mode</option>
                                        <option value="dark">Dark Mode</option>
                                    </select>

                                    <!-- String/Number Input -->
                                    <input v-else :id="'setting_' + setting.id"
                                        :type="setting.type === 'integer' ? 'number' : 'text'"
                                        v-model="form.settings[form.settings.findIndex(s => s.id === setting.id)].value"
                                        :placeholder="'Enter ' + getTitle(setting.key)"
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm">
                                </template>
                            </div>
                        </div>
                    </div>
                </BentoCard>
            </div>

        </div>
    </AppLayout>
</template>
