<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Settings as SettingsIcon, Save, Globe, FolderKanban, ShieldAlert, CheckCircle2, Hash, User, Bell, Palette, Sun, Moon, Monitor, ShieldCheck } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    settings: {
        app_name?: string;
        timezone?: string;
        project_code_prefix?: string;
        task_code_prefix?: string;
        milestone_code_prefix?: string;
        risk_code_prefix?: string;
        issue_code_prefix?: string;
        change_request_code_prefix?: string;
        client_code_prefix?: string;
        vendor_code_prefix?: string;
        maintenance_mode?: string | boolean;
        [key: string]: any;
    };
    user_preferences?: {
        theme?: string;
        notify_task_assigned?: boolean;
        notify_daily_digest?: boolean;
        [key: string]: any;
    };
}>();

const page = usePage();

// Pengecekan Role Admin untuk RBAC
const isAdmin = computed(() => {
    const roles = (page.props.auth as any)?.roles || [];
    return roles.includes('Admin');
});

// 1. Form untuk Preferensi Pribadi (Semua User)
const personalForm = useForm({
    theme: props.user_preferences?.theme || 'system',
    notify_task_assigned: props.user_preferences?.notify_task_assigned ?? true,
    notify_daily_digest: props.user_preferences?.notify_daily_digest ?? false,
});

const submitPersonalSettings = () => {
    personalForm.post(route('settings.update.personal'), {
        preserveScroll: true,
    });
};

// 2. Form untuk Pengaturan Global (Hanya Admin)
const globalForm = useForm({
    app_name: props.settings.app_name || 'PM Control Center',
    timezone: props.settings.timezone || 'Asia/Jakarta',
    project_code_prefix: props.settings.project_code_prefix || 'PRJ-',
    task_code_prefix: props.settings.task_code_prefix || 'TSK-',
    milestone_code_prefix: props.settings.milestone_code_prefix || 'MLS-',
    risk_code_prefix: props.settings.risk_code_prefix || 'RSK-',
    issue_code_prefix: props.settings.issue_code_prefix || 'ISS-',
    change_request_code_prefix: props.settings.change_request_code_prefix || 'CRQ-',
    client_code_prefix: props.settings.client_code_prefix || 'CLI-',
    vendor_code_prefix: props.settings.vendor_code_prefix || 'VND-',
    maintenance_mode: props.settings.maintenance_mode === 'true' || props.settings.maintenance_mode === true,
});

const submitGlobalSettings = () => {
    globalForm.post(route('settings.update.global'), {
        preserveScroll: true,
    });
};

const timezones = [
    { value: 'Asia/Jakarta', label: 'Asia/Jakarta (WIB, UTC+07:00)' },
    { value: 'Asia/Makassar', label: 'Asia/Makassar (WITA, UTC+08:00)' },
    { value: 'Asia/Jayapura', label: 'Asia/Jayapura (WIT, UTC+09:00)' },
    { value: 'Asia/Singapore', label: 'Asia/Singapore (SGT, UTC+08:00)' },
    { value: 'Asia/Tokyo', label: 'Asia/Tokyo (JST, UTC+09:00)' },
    { value: 'UTC', label: 'UTC (Coordinated Universal Time)' },
    { value: 'America/New_York', label: 'America/New_York (EST/EDT)' },
    { value: 'Europe/London', label: 'Europe/London (GMT/BST)' },
];

const prefixConfigs = [
    { key: 'project_code_prefix', label: 'Project Code Prefix', default: 'PRJ-', placeholder: 'PRJ-' },
    { key: 'task_code_prefix', label: 'Task Code Prefix', default: 'TSK-', placeholder: 'TSK-' },
    { key: 'milestone_code_prefix', label: 'Milestone Code Prefix', default: 'MLS-', placeholder: 'MLS-' },
    { key: 'risk_code_prefix', label: 'Risk Code Prefix', default: 'RSK-', placeholder: 'RSK-' },
    { key: 'issue_code_prefix', label: 'Issue Code Prefix', default: 'ISS-', placeholder: 'ISS-' },
    { key: 'change_request_code_prefix', label: 'Change Request Prefix', default: 'CRQ-', placeholder: 'CRQ-' },
    { key: 'client_code_prefix', label: 'Client Code Prefix', default: 'CLI-', placeholder: 'CLI-' },
    { key: 'vendor_code_prefix', label: 'Vendor Code Prefix', default: 'VND-', placeholder: 'VND-' },
] as const;
</script>

<template>
    <Head title="Settings & Preferences" />

    <AppLayout>
        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <SettingsIcon class="w-6 h-6 text-indigo-500" />
                        Settings & Preferences
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Customize your personal profile preferences and manage application configurations.
                    </p>
                </div>
            </div>

            <!-- Success Alert Banner -->
            <div 
                v-if="(page.props as any).flash?.message" 
                class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm font-medium"
            >
                <CheckCircle2 class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <span>{{ (page.props as any).flash?.message }}</span>
            </div>

            <!-- ========================================================================= -->
            <!-- 1. AREA PUBLIK: PERSONAL PREFERENCES (Dapat diakses oleh semua user)      -->
            <!-- ========================================================================= -->
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                            <User class="w-4 h-4" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Personal Preferences</h3>
                    </div>
                </div>

                <form @submit.prevent="submitPersonalSettings">
                    <BentoCard noPadding>
                        <div class="p-6 space-y-6">
                            <!-- Theme Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 flex items-center gap-2">
                                    <Palette class="w-4 h-4 text-indigo-500" />
                                    Appearance Theme
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Choose how the application interface looks to you.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-xl">
                                    <label :class="[
                                        'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                                        personalForm.theme === 'light' 
                                            ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 ring-2 ring-indigo-500/20' 
                                            : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300'
                                    ]">
                                        <input v-model="personalForm.theme" type="radio" value="light" class="sr-only" />
                                        <Sun class="w-4 h-4 text-amber-500" />
                                        <span class="text-sm font-semibold">Light Mode</span>
                                    </label>

                                    <label :class="[
                                        'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                                        personalForm.theme === 'dark' 
                                            ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 ring-2 ring-indigo-500/20' 
                                            : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300'
                                    ]">
                                        <input v-model="personalForm.theme" type="radio" value="dark" class="sr-only" />
                                        <Moon class="w-4 h-4 text-indigo-400" />
                                        <span class="text-sm font-semibold">Dark Mode</span>
                                    </label>

                                    <label :class="[
                                        'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                                        personalForm.theme === 'system' 
                                            ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/30 text-indigo-900 dark:text-indigo-200 ring-2 ring-indigo-500/20' 
                                            : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300'
                                    ]">
                                        <input v-model="personalForm.theme" type="radio" value="system" class="sr-only" />
                                        <Monitor class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm font-semibold">System Default</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Email Notifications -->
                            <div class="pt-5 border-t border-gray-100 dark:border-gray-700/60">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5 flex items-center gap-2">
                                    <Bell class="w-4 h-4 text-indigo-500" />
                                    Email Notifications
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Control what alerts are dispatched to your email address.</p>
                                
                                <div class="space-y-3 max-w-xl">
                                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700/60 hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors">
                                        <input 
                                            v-model="personalForm.notify_task_assigned" 
                                            type="checkbox" 
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700" 
                                        />
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Task Assignment Notifications</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive an email immediately when a project task or milestone is assigned to you.</p>
                                        </div>
                                    </label>

                                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 dark:border-gray-700/60 hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors">
                                        <input 
                                            v-model="personalForm.notify_daily_digest" 
                                            type="checkbox" 
                                            class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700" 
                                        />
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Daily Digest Summary</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive a daily summary of upcoming deadlines, open risks, and status updates.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Save Button -->
                        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-700/60 flex justify-end">
                            <button 
                                type="submit"
                                :disabled="personalForm.processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-all hover:scale-105 active:scale-95 disabled:opacity-50 disabled:pointer-events-none"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ personalForm.processing ? 'Saving...' : 'Save Personal Preferences' }}</span>
                            </button>
                        </div>
                    </BentoCard>
                </form>
            </section>

            <!-- ========================================================================= -->
            <!-- 2. AREA ADMIN: GLOBAL SETTINGS (Hanya terlihat & dieksekusi oleh Admin)   -->
            <!-- ========================================================================= -->
            <section v-if="isAdmin" class="space-y-6 pt-4 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <ShieldCheck class="w-4 h-4" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                Global System Settings
                                <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">Admin Only</span>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Parameters configured here affect all system users and automated project identifiers.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitGlobalSettings" class="space-y-6">

                    <!-- Section 1: General Settings -->
                    <BentoCard noPadding>
                        <div class="p-6">
                            <div class="flex items-center gap-3 pb-5 mb-5 border-b border-gray-100 dark:border-gray-700/60">
                                <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400">
                                    <Globe class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">General Settings</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Basic platform branding and timezone settings.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Application Name -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                        Application Name
                                    </label>
                                    <input 
                                        v-model="globalForm.app_name" 
                                        type="text" 
                                        placeholder="e.g. PM Control Center"
                                        class="block w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                                    />
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Displayed across sidebar header, navbar, and exported documents.</p>
                                </div>

                                <!-- Timezone -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                        System Timezone
                                    </label>
                                    <div class="relative">
                                        <select 
                                            v-model="globalForm.timezone" 
                                            class="block w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-colors"
                                        >
                                            <option v-for="tz in timezones" :key="tz.value" :value="tz.value">
                                                {{ tz.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Default timezone applied to all system activities and schedules.</p>
                                </div>
                            </div>
                        </div>
                    </BentoCard>

                    <!-- Section 2: Module Code Prefixes -->
                    <BentoCard noPadding>
                        <div class="p-6">
                            <div class="flex items-center gap-3 pb-5 mb-5 border-b border-gray-100 dark:border-gray-700/60">
                                <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                                    <FolderKanban class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">Module Code Prefixes</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Configure default prefixes auto-filled when creating new items in each module.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div v-for="cfg in prefixConfigs" :key="cfg.key">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ cfg.label }}
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <Hash class="w-4 h-4 text-gray-400" />
                                            </div>
                                            <input 
                                                v-model="(globalForm as any)[cfg.key]" 
                                                type="text" 
                                                :placeholder="cfg.placeholder"
                                                class="block w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-gray-700 rounded-xl leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono transition-colors uppercase"
                                            />
                                        </div>
                                        <div class="shrink-0 px-2.5 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 text-xs font-mono text-gray-600 dark:text-gray-300">
                                            <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ ((globalForm as any)[cfg.key] || cfg.default) + '001' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </BentoCard>

                    <!-- Section 3: System Control -->
                    <BentoCard noPadding>
                        <div class="p-6">
                            <div class="flex items-center gap-3 pb-5 mb-5 border-b border-gray-100 dark:border-gray-700/60">
                                <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400">
                                    <ShieldAlert class="w-5 h-5" />
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white">System Control</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">High-impact platform availability and emergency switches.</p>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-rose-100 dark:border-rose-900/40 bg-rose-50/50 dark:bg-rose-950/20">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h5 class="text-sm font-bold text-gray-900 dark:text-white">Maintenance Mode</h5>
                                        <span 
                                            v-if="globalForm.maintenance_mode" 
                                            class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-600 text-white animate-pulse"
                                        >
                                            ACTIVE
                                        </span>
                                        <span 
                                            v-else 
                                            class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300"
                                        >
                                            DISABLED
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        When enabled, non-administrator users will see a maintenance notice and will not be able to perform mutations.
                                    </p>
                                </div>

                                <!-- Toggle Switch -->
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input 
                                        v-model="globalForm.maintenance_mode" 
                                        type="checkbox" 
                                        class="sr-only peer"
                                    />
                                    <div class="w-12 h-6.5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-rose-500 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5.5 after:w-5.5 after:transition-all dark:border-gray-600 peer-checked:bg-rose-600"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Card Footer: Save Button -->
                        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/30 border-t border-gray-100 dark:border-gray-700/60 flex justify-end">
                            <button 
                                type="submit"
                                :disabled="globalForm.processing"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-all hover:scale-105 active:scale-95 disabled:opacity-50 disabled:pointer-events-none"
                            >
                                <Save class="w-4 h-4" />
                                <span>{{ globalForm.processing ? 'Saving...' : 'Save Global Settings' }}</span>
                            </button>
                        </div>
                    </BentoCard>

                </form>
            </section>

        </div>
    </AppLayout>
</template>
