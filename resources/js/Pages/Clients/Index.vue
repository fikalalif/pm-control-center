<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import FormModal from '@/Components/Forms/FormModal.vue';
import Pagination from '@/Components/Data/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Users, Plus, Edit, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    clients: any;
}>();

const showModal = ref(false);
const editingClient = ref<any>(null);

const form = useForm({
    client_code: '',
    name: '',
    contact_person: '',
    phone: '',
    email: '',
    industry: '',
    notes: '',
});

const openCreateModal = () => {
    editingClient.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEditModal = (client: any) => {
    editingClient.value = client;
    form.clearErrors();
    form.client_code = client.client_code || '';
    form.name = client.name || '';
    form.contact_person = client.contact_person || '';
    form.phone = client.phone || '';
    form.email = client.email || '';
    form.industry = client.industry || '';
    form.notes = client.notes || '';
    showModal.value = true;
};

const submitForm = () => {
    if (editingClient.value) {
        form.put(route('clients.update', editingClient.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('clients.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
};

const deleteClient = (id: number) => {
    if (confirm('Are you sure you want to delete this client?')) {
        router.delete(route('clients.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Clients" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <Users class="w-6 h-6 text-indigo-500" />
                        Clients Management
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage client data for Hetra Teknologi Indonesia.</p>
                </div>
                <button @click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <Plus class="w-4 h-4" />
                    New Client
                </button>
            </div>

            <!-- Main Data Table -->
            <BentoCard title="All Clients" noPadding>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                        <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Contact Person</th>
                                <th class="px-6 py-4">Phone / Email</th>
                                <th class="px-6 py-4">Industry</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ client.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ client.client_code }}</div>
                                </td>
                                <td class="px-6 py-4">{{ client.contact_person || '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">{{ client.phone || '-' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ client.email || '-' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ client.industry || '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="openEditModal(client)" class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button @click="deleteClient(client.id)" class="text-gray-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!clients.data || clients.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    No clients found. Create one to get started!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <Pagination v-if="clients.meta && clients.meta.links" :links="clients.meta.links" />
                <Pagination v-else-if="clients.links" :links="clients.links" />
            </BentoCard>
        </div>

        <!-- Form Modal -->
        <FormModal :show="showModal" :title="editingClient ? 'Edit Client' : 'New Client'" @close="showModal = false" @submit="submitForm" maxWidth="2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client Code <span class="text-rose-500">*</span></label>
                    <input v-model="form.client_code" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.client_code" class="mt-1" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client Name <span class="text-rose-500">*</span></label>
                    <input v-model="form.name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact Person</label>
                    <input v-model="form.contact_person" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.contact_person" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                    <input v-model="form.phone" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.phone" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.email" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Industry</label>
                    <input v-model="form.industry" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <InputError :message="form.errors.industry" class="mt-1" />
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                    <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                    <InputError :message="form.errors.notes" class="mt-1" />
                </div>
            </div>
            
            <template #actions>
                <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Client' }}
                </button>
            </template>
        </FormModal>

    </AppLayout>
</template>
