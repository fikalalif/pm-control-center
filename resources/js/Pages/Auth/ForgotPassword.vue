<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, KeyRound, ArrowLeft } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 p-4 sm:p-8 transition-colors duration-200">

        <!-- Center Bento Card -->
        <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700 p-8 sm:p-10 relative">

            <!-- Soft Glowing Background Element -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-indigo-100 dark:bg-indigo-900/40 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">

                <!-- Back to Login Link -->
                <Link :href="route('login')" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors mb-8">
                    <ArrowLeft class="w-4 h-4" /> Back to Login
                </Link>

                <!-- Icon Banner -->
                <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center mb-6 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                    <KeyRound class="w-7 h-7 text-indigo-600 dark:text-indigo-400" />
                </div>

                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Forgot Password?</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">
                    No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                </p>

                <!-- Status Alert -->
                <div v-if="status" class="mb-6 font-medium text-sm text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800">
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
                            <input v-model="form.email" type="email" required autofocus
                                class="pl-11 w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow shadow-sm"
                                placeholder="admin@pmcontrol.com">
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button :class="{ 'opacity-70 cursor-not-allowed': form.processing }" :disabled="form.processing"
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            <span v-if="form.processing" class="flex items-center gap-2">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Sending Link...
                            </span>
                            <span v-else>Email Password Reset Link</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
