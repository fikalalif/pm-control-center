<script setup lang="ts">
import { ref } from 'vue';
import { Bell, Check } from 'lucide-vue-next';
import { router, usePage, Link } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';

const page = usePage();
const isOpen = ref(false);
const dropdownRef = ref(null);

onClickOutside(dropdownRef, () => {
    isOpen.value = false;
});

const markAsRead = (id: string) => {
    router.post(route('notifications.read', id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
};

const markAllAsRead = () => {
    router.post(route('notifications.read-all'), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => isOpen.value = false
    });
};
</script>

<template>
    <div class="relative" ref="dropdownRef">
        <!-- Bell Icon Button -->
        <button @click="isOpen = !isOpen" class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <Bell class="w-6 h-6" />
            <!-- Unread Badge -->
            <span v-if="$page.props.auth.unread_notifications_count > 0" class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                {{ $page.props.auth.unread_notifications_count > 9 ? '9+' : $page.props.auth.unread_notifications_count }}
            </span>
        </button>

        <!-- Dropdown Menu -->
        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <div v-if="isOpen" class="absolute right-0 z-50 mt-2 w-80 md:w-96 origin-top-right rounded-2xl bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-white/10 focus:outline-none border border-gray-100 dark:border-gray-700 overflow-hidden">
                
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Notifications</h3>
                    <button v-if="$page.props.auth.unread_notifications_count > 0" @click="markAllAsRead" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        Mark all as read
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <template v-if="$page.props.auth.notifications.length > 0">
                        <div v-for="notification in $page.props.auth.notifications" :key="notification.id" class="flex gap-4 p-4 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                    <span class="font-bold">{{ notification.data.title }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                    {{ notification.data.message }}
                                </p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1.5 font-medium">
                                    {{ new Date(notification.created_at).toLocaleString() }}
                                </p>
                            </div>
                            
                            <div class="flex-shrink-0">
                                <button @click="markAsRead(notification.id)" class="text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400" title="Mark as read">
                                    <Check class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </template>
                    <div v-else class="px-4 py-8 text-center">
                        <Bell class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-2 opacity-50" />
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">You're all caught up!</p>
                        <p class="text-xs text-gray-400 mt-1">No new notifications.</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 p-2 text-center">
                    <Link href="#" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">View All Notifications</Link>
                </div>
            </div>
        </transition>
    </div>
</template>
