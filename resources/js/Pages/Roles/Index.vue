<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Shield, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    roles: any[];
    permissions: any[];
}>();

const page = usePage();
const can = (permissionName: string) => {
    const roles = (page.props.auth as any).roles || [];
    const permissions = (page.props.auth as any).permissions || [];
    if (roles.includes('Admin')) return true;
    return permissions.includes(permissionName);
};

const flashMessage = computed(() => (page.props as any).flash?.message);
const flashError = computed(() => (page.props as any).flash?.error);

const showModal = ref(false);
const editingRole = ref<any>(null);

const form = useForm({
    name: '',
    permissions: [] as string[],
});

const openCreateModal = () => {
    editingRole.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (role: any) => {
    editingRole.value = role;
    form.clearErrors();
    form.name = role.name;
    // Ambil daftar permission yang sudah dimiliki role ini
    form.permissions = role.permissions.map((p: any) => p.name);
    showModal.value = true;
};

const submitForm = () => {
    if (editingRole.value) {
        form.put(route('roles.update', editingRole.value.id), {
            preserveScroll: true,
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    } else {
        form.post(route('roles.store'), {
            preserveScroll: true,
            onSuccess: () => { showModal.value = false; form.reset(); }
        });
    }
};

const deleteRole = (id: number) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('roles.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>
    <!-- Alert Flash Message -->
    <div v-if="flashMessage" class="mb-6 font-medium text-sm text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 p-4 rounded-xl border border-emerald-200 dark:border-emerald-800">{{ flashMessage }}</div>
    <div v-if="flashError" class="mb-6 font-medium text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/30 p-4 rounded-xl border border-rose-200 dark:border-rose-800">{{ flashError }}</div>

    <Head title="Role Management" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Shield class="w-6 h-6 text-indigo-500" /> Roles & Permissions
                    </h2>
                </div>
                <button v-if="can('manage_roles')" @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2">
                    <Plus class="w-4 h-4" /> New Role
                </button>
            </div>

            <BentoCard title="Access Roles" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Role Name</th>
                                <th class="px-6 py-4">Permissions Attached</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ role.name }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-md">
                                        <span v-for="perm in role.permissions" :key="perm.id" class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 rounded text-[10px]">
                                            {{ perm.name }}
                                        </span>
                                        <span v-if="!role.permissions.length" class="text-gray-400 dark:text-gray-500 italic text-xs">No specific access</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button v-if="can('manage_roles')" @click="openEditModal(role)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 mx-2" title="Edit"><Edit class="w-4 h-4" /></button>
                                    <button v-if="can('manage_roles') && role.name !== 'Admin'" @click="deleteRole(role.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400" title="Delete"><Trash2 class="w-4 h-4" /></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </BentoCard>
        </div>

        <FormModal :show="showModal" :title="editingRole ? 'Edit Role Access' : 'Create Role'" @close="showModal = false" @submit="submitForm" maxWidth="lg">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-indigo-500">
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Page Permissions</label>
                    <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div v-for="perm in permissions" :key="perm.id" class="flex items-center gap-2">
                            <input type="checkbox" :id="perm.name" :value="perm.name" v-model="form.permissions" class="rounded border-gray-300 dark:border-gray-500 dark:bg-gray-800 text-indigo-600 focus:ring-indigo-500">
                            <label :for="perm.name" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">{{ perm.name }}</label>
                        </div>
                        <div v-if="!permissions.length" class="text-sm text-gray-500 dark:text-gray-400 col-span-2">Belum ada data permission di database.</div>
                    </div>
                </div>
            </div>
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg mr-2 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Save Role</button>
            </template>
        </FormModal>
    </AppLayout>
</template>
