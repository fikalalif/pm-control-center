<script setup lang="ts">
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

// Menerima data client dari Controller (fungsi edit)
const props = defineProps<{
    client: {
        id: number;
        client_code: string;
        name: string;
        contact_person: string | null;
        email: string | null;
        phone: string | null;
        industry: string | null;
        notes: string | null;
    };
}>();

// Inisialisasi form dengan data client yang ada
const form = useForm({
    client_code: props.client.client_code,
    name: props.client.name,
    contact_person: props.client.contact_person || "",
    email: props.client.email || "",
    phone: props.client.phone || "",
    industry: props.client.industry || "",
    notes: props.client.notes || "",
});

const submit = () => {
    // Gunakan method PUT untuk update
    form.put(route("clients.update", props.client.id));
};
</script>

<template>
    <Head title="Edit Client" />

    <AppLayout>
        <div class="max-w-3xl mx-auto space-y-6">
            <div class="flex items-center gap-4 mb-6">
                <Link
                    :href="route('clients.index')"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    &larr; Back to Clients
                </Link>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Edit Client: {{ props.client.name }}
                </h2>
            </div>

            <div
                class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200"
            >
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Grid diubah biar nampung 6 input -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 1. Client Code -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Client Code *</label
                            >
                            <input
                                v-model="form.client_code"
                                type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                placeholder="Ex: CLI-001"
                            />
                            <div
                                v-if="form.errors.client_code"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.client_code }}
                            </div>
                        </div>

                        <!-- 2. Company Name -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Company Name *</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                placeholder="Ex: PT Hetra Teknologi"
                            />
                            <div
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- 3. Contact Person Name -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Contact Person Name</label
                            >
                            <input
                                v-model="form.contact_person"
                                type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex: Fikal Alif"
                            />
                        </div>

                        <!-- 4. Phone Number (Tambahan Baru) -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Phone Number</label
                            >
                            <input
                                v-model="form.phone"
                                type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex: 0891831398391"
                            />
                        </div>

                        <!-- 5. Email -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Email</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex: testing@gmail.com"
                            />
                        </div>

                        <!-- 6. Industry (Tambahan Baru) -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                >Industry</label
                            >
                            <input
                                v-model="form.industry"
                                type="text"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex: Technology"
                            />
                        </div>
                    </div>

                    <!-- 7. Notes (Full Width) -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                            >Notes</label
                        >
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Tambahkan catatan khusus..."
                        ></textarea>
                    </div>

                    <div
                        class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700"
                    >
                        <!-- Tulisan button di Create: Save Client. Di Edit: Update Client -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                        >
                            Save Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
