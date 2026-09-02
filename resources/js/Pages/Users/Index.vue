<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { User, Plus, Edit, Trash2 } from 'lucide-vue-next';


const props = defineProps<{
    users: any;
}>();

const page = usePage();

const flashMessage = computed(() => (page.props as any).flash?.message);
const flashError = computed(() => (page.props as any).flash?.error);

const currentUser = computed(() => page.props.auth.user);

const showModal = ref(false);
const editingUser = ref<any>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (user: any) => {
    editingUser.value = user;
    form.clearErrors();
    form.name = user.name || '';
    form.email = user.email || '';
    form.password = ''; // Don't fill password on edit
    showModal.value = true;
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('users.update', editingUser.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('users.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this team member?')) {
        router.delete(route('users.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>

    <!-- Alert Success -->
    <div v-if="flashMessage"
        class="mb-6 font-medium text-sm text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        {{ flashMessage }}
    </div>

    <!-- Alert Error (Dari Controller) -->
    <div v-if="flashError"
        class="mb-6 font-medium text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/30 p-4 rounded-xl border border-rose-200 dark:border-rose-800 flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
            </path>
        </svg>
        {{ flashError }}
    </div>

    <Head title="Users" />

    <AppLayout>


        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <User class="w-6 h-6 text-indigo-500" />
                        Team Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage internal team members and access.
                    </p>
                </div>
                <button @click="openCreateModal"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New Member
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="Internal Team" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead
                            class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Joined Date</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="user in users.data" :key="user.id"
                                class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-xs">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ user.name }}
                                            <span v-if="currentUser.id === user.id"
                                                class="ml-2 text-[10px] bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 rounded-full">You</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs">Member</span>
                                </td>
                                <td class="px-6 py-4">{{ new Date(user.created_at).toLocaleDateString('id-ID') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openEditModal(user)"
                                            class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button v-if="currentUser.id !== user.id" @click="deleteUser(user.id)"
                                            class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                        <span v-else class="text-gray-400 text-xs italic">N/A</span>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!users.data || users.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No team members found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination v-if="users.meta && users.meta.links" :links="users.meta.links" />
                <Pagination v-else-if="users.links" :links="users.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingUser ? 'Edit Member' : 'New Member'" @close="showModal = false"
            @submit="submitForm" maxWidth="lg">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name <span
                            class="text-rose-500">*</span></label>
                    <input v-model="form.name" type="text"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address <span
                            class="text-rose-500">*</span></label>
                    <input v-model="form.email" type="email"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.email" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Password <span v-if="!editingUser" class="text-rose-500">*</span>
                        <span v-else class="text-gray-400 text-xs font-normal ml-1">(Leave empty to keep current)</span>
                    </label>
                    <input v-model="form.password" type="password"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.password" class="mt-1" />
                </div>
            </div>

            <template #actions>
                <button type="button" @click="showModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Member' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
