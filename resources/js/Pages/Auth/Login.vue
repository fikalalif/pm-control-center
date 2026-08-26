<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Mail, Lock, LayoutDashboard, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// State untuk toggle password
const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 p-4 sm:p-8 transition-colors duration-200">

        <!-- Main Bento Container -->
        <div class="max-w-5xl w-full bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row">

            <!-- Left Panel: Branding / Visual (Hidden on small screens) -->
            <div class="hidden md:flex md:w-5/12 bg-indigo-600 dark:bg-indigo-900 p-8 md:p-12 text-white flex-col justify-between relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-400 opacity-20 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-8 border border-white/20 shadow-sm">
                        <LayoutDashboard class="w-8 h-8 text-white" />
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight">PM Control<br>Center.</h1>
                    <p class="text-indigo-100 text-sm md:text-base leading-relaxed">
                        Orchestrate your projects, tasks, and teams from a single, unified dashboard. Log in to continue to your workspace.
                    </p>
                </div>

                <div class="relative z-10">
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-2xl border border-white/10 shadow-sm">
                        <p class="text-xs text-indigo-50 font-medium leading-relaxed italic">
                            "Streamlining operations and maximizing team productivity with modern bento-box precision."
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Login Form -->
            <div class="w-full md:w-7/12 p-8 sm:p-12 lg:p-16 flex flex-col justify-center bg-white dark:bg-gray-800">

                <!-- Mobile Logo -->
                <div class="md:hidden flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                        <LayoutDashboard class="w-5 h-5 text-white" />
                    </div>
                    <h1 class="text-xl font-extrabold text-gray-900 dark:text-white">PM Control Center</h1>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Welcome Back 👋</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Please enter your credentials to access your account.</p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- Email Input -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <Mail class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                            </div>
                            <input v-model="form.email" type="email" required autofocus autocomplete="username"
                                class="pl-11 w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow shadow-sm"
                                placeholder="admin@pmcontrol.com">
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Password Input dengan Show/Hide Toggle (Tanpa Forgot Password) -->
                    <div>
                        <div class="mb-1.5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Password</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <Lock class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                            </div>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password"
                                class="pl-11 pr-11 w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow shadow-sm"
                                placeholder="••••••••">

                            <!-- Toggle Button -->
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors focus:outline-none">
                                <Eye v-if="!showPassword" class="w-5 h-5" />
                                <EyeOff v-else class="w-5 h-5" />
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block pt-2">
                        <label class="flex items-center cursor-pointer group">
                            <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600" />
                            <span class="ms-2 text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">Remember me for 30 days</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button :class="{ 'opacity-70 cursor-not-allowed': form.processing }" :disabled="form.processing"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Authenticating...
                            </span>
                            <span v-else>Sign In to Workspace</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
