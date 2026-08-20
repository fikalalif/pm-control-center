<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import NotificationDropdown from '@/Components/UI/NotificationDropdown.vue';
import {
    LayoutDashboard, FolderKanban, CheckSquare, Flag,
    AlertTriangle, XCircle, FileEdit, Briefcase,
    Truck, Users, Calendar, Activity, Settings, Sun, Moon
} from 'lucide-vue-next';

// Navigasi Sidebar Global menggunakan routeName agar lebih aman
const navGroups = [
    {
        name: 'DASHBOARD',
        items: [
            { name: 'Dashboard', routeName: 'dashboard', url: '/dashboard', icon: LayoutDashboard },
        ]
    },
    {
        name: 'PROJECT MANAGEMENT',
        items: [
            { name: 'Projects', routeName: 'projects.index', url: '/projects', icon: FolderKanban },
            { name: 'Tasks', routeName: 'tasks.index', url: '/tasks', icon: CheckSquare },
            { name: 'Milestones', routeName: 'milestones.index', url: '/milestones', icon: Flag },
            { name: 'Risks', routeName: 'risks.index', url: '/risks', icon: AlertTriangle },
            { name: 'Issues', routeName: 'issues.index', url: '/issues', icon: XCircle },
            { name: 'Change Requests', routeName: 'change-requests.index', url: '/change-requests', icon: FileEdit },
        ]
    },
    {
        name: 'STAKEHOLDERS',
        items: [
            { name: 'Clients', routeName: 'clients.index', url: '/clients', icon: Briefcase },
            { name: 'Vendors', routeName: 'vendors.index', url: '/vendors', icon: Truck },
            { name: 'Team', routeName: 'users.index', url: '/users', icon: Users },
        ]
    },
    {
        name: 'ACTIVITY',
        items: [
            { name: 'Meetings', routeName: 'meetings.index', url: '/meetings', icon: Calendar },
            // Di dalam menu ACTIVITY
            { name: 'Activity Log', routeName: 'activity-log.index', url: '/activity-log', icon: Activity },


        ]
    },
    {
        name: 'SYSTEM',
        items: [
            // Di dalam menu SYSTEM
            { name: 'Settings', routeName: 'settings.index', url: '/settings', icon: Settings }
        ]
    }
];

// Dark Mode Logic
const isDark = ref(false);

const showDropdown = ref(false);

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div
        class="flex h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">

        <!-- Sidebar -->
        <aside
            class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-colors duration-200">
            <div
                class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-700 font-bold text-lg tracking-tight">
                PM Control Center
            </div>
            <nav class="flex-1 p-4 space-y-6 overflow-y-auto">

                <!-- Render Navigasi Berdasarkan Grup -->
                <div v-for="group in navGroups" :key="group.name">
                    <h3 class="px-3 text-xs font-bold text-gray-400 dark:text-gray-500 tracking-wider mb-2">
                        {{ group.name }}
                    </h3>
                    <div class="space-y-1">
                        <template v-for="item in group.items" :key="item.name">

                            <!-- Link Aktif (Menggunakan URL langsung agar tidak bergantung pada Ziggy has()) -->
                            <Link v-if="item.url" :href="item.url" :class="[
                                $page.url.startsWith(item.url)
                                    ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium',
                                'flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-sm'
                            ]">
                                <component :is="item.icon" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                {{ item.name }}
                            </Link>

                            <!-- Disabled Item untuk fitur yang belum dibuat -->
                            <div v-else
                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-400 dark:text-gray-600 cursor-not-allowed select-none">
                                <component :is="item.icon" class="w-5 h-5 opacity-40" />
                                {{ item.name }}
                            </div>

                        </template>
                    </div>
                </div>

            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header / Topbar -->
            <header
                class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-8 transition-colors duration-200">
                <h1 class="text-xl font-semibold">Dashboard</h1>

                <div class="flex items-center gap-4">
                    <!-- Notification Dropdown -->
                    <NotificationDropdown />
                    
                    <!-- Theme Toggle Button -->
                    <button @click="toggleTheme"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                        <Sun v-if="isDark" class="w-5 h-5" />
                        <Moon v-else class="w-5 h-5" />
                    </button>

                    <!-- User Avatar & Dropdown -->
                    <div class="relative">
                        <!-- Tombol Avatar -->
                        <button @click="showDropdown = !showDropdown"
                            class="flex items-center gap-2 focus:outline-none">
                            <div
                                class="w-8 h-8 bg-indigo-600 dark:bg-indigo-500 text-white rounded-full flex items-center justify-center font-bold text-sm shadow-sm hover:ring-2 hover:ring-indigo-300 transition-all">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </div>
                        </button>

                        <!-- Dropdown Menu -->
                        <div v-if="showDropdown" @click.away="showDropdown = false"
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg py-1 border border-gray-100 dark:border-gray-700 z-50">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-sm leading-5 font-medium text-gray-900 dark:text-white truncate">
                                    {{ $page.props.auth.user.name }}
                                </p>
                                <p class="text-xs leading-5 font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ $page.props.auth.user.email }}
                                </p>
                            </div>

                            <Link :href="route('profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                Profile Settings
                            </Link>

                            <Link :href="route('logout')" method="post" as="button"
                                class="w-full text-left block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                Log Out
                            </Link>
                        </div>

                        <!-- Overlay transparan untuk menutup dropdown saat klik di luar -->
                        <div v-if="showDropdown" @click="showDropdown = false" class="fixed inset-0 z-40"></div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
