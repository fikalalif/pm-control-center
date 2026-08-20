<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import BentoCard from '@/Components/Bento/BentoCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import FormModal from '@/Components/Forms/FormModal.vue';

const props = defineProps<{
    project: any;
    users: any[];
    vendors: any[];
}>();

const tabs = ['Overview', 'Tasks', 'Milestones', 'Risks', 'Issues', 'Change Requests', 'Vendors', 'Meetings', 'Activity'];
const activeTab = ref('Overview');

const daysRemaining = computed(() => {
    if (!props.project.target_completion) return 0;
    const today = new Date();
    const deadline = new Date(props.project.target_completion);
    const diffTime = deadline.getTime() - today.getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
});

const getHealthColor = (status: string) => {
    switch (status) {
        case 'Completed': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200';
        case 'At Risk': return 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200';
        case 'Delayed': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border-red-200';
        default: return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 border-blue-200';
    }
};

// --- TASK LOGIC ---
const showTaskModal = ref(false);
const editTaskMode = ref(false);
const editingTaskId = ref<number | null>(null);
const taskForm = useForm({
    task_code: '',
    project_id: props.project.id,
    name: '',
    assigned_user_id: '',
    vendor_id: '',
    start_date: '',
    deadline: '',
    status: 'Pending',
});

const openCreateTask = () => {
    editTaskMode.value = false;
    editingTaskId.value = null;
    taskForm.reset();
    taskForm.project_id = props.project.id;
    showTaskModal.value = true;
};

const openEditTask = (item: any) => {
    editTaskMode.value = true;
    editingTaskId.value = item.id;
    Object.keys(taskForm.data()).forEach(key => {
        if (item[key] !== undefined) {
            (taskForm as any)[key] = item[key];
        }
    });
    showTaskModal.value = true;
};

const submitTask = () => {
    if (editTaskMode.value && editingTaskId.value) {
        taskForm.put(route('tasks.update', editingTaskId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showTaskModal.value = false;
                taskForm.reset();
            }
        });
    } else {
        taskForm.post(route('tasks.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showTaskModal.value = false;
                taskForm.reset();
            }
        });
    }
};

const deleteTask = (id: number) => {
    if (confirm('Yakin ingin menghapus task ini?')) {
        router.delete(route('tasks.destroy', id), { preserveScroll: true });
    }
};

// --- MILESTONE LOGIC ---
const showMilestoneModal = ref(false);
const editMilestoneMode = ref(false);
const editingMilestoneId = ref<number | null>(null);
const milestoneForm = useForm({
    project_id: props.project.id,
    name: '',
    due_date: '',
    status: 'Pending',
    description: '',
});

const openCreateMilestone = () => {
    editMilestoneMode.value = false;
    editingMilestoneId.value = null;
    milestoneForm.reset();
    milestoneForm.project_id = props.project.id;
    showMilestoneModal.value = true;
};

const openEditMilestone = (item: any) => {
    editMilestoneMode.value = true;
    editingMilestoneId.value = item.id;
    Object.keys(milestoneForm.data()).forEach(key => {
        if (item[key] !== undefined) {
            (milestoneForm as any)[key] = item[key];
        }
    });
    showMilestoneModal.value = true;
};

const submitMilestone = () => {
    if (editMilestoneMode.value && editingMilestoneId.value) {
        milestoneForm.put(route('milestones.update', editingMilestoneId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showMilestoneModal.value = false;
                milestoneForm.reset();
            }
        });
    } else {
        milestoneForm.post(route('milestones.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showMilestoneModal.value = false;
                milestoneForm.reset();
            }
        });
    }
};

const deleteMilestone = (id: number) => {
    if (confirm('Yakin hapus milestone ini?')) {
        router.delete(route('milestones.destroy', id), { preserveScroll: true });
    }
};

// --- RISK LOGIC ---
const showRiskModal = ref(false);
const editRiskMode = ref(false);
const editingRiskId = ref<number | null>(null);
const riskForm = useForm({
    project_id: props.project.id,
    risk_code: '',
    title: '',
    risk_level: 'Low',
    owner_id: '',
    status: 'Open',
    mitigation: '',
});

const openCreateRisk = () => {
    editRiskMode.value = false;
    editingRiskId.value = null;
    riskForm.reset();
    riskForm.project_id = props.project.id;
    showRiskModal.value = true;
};

const openEditRisk = (item: any) => {
    editRiskMode.value = true;
    editingRiskId.value = item.id;
    Object.keys(riskForm.data()).forEach(key => {
        if (item[key] !== undefined) {
            (riskForm as any)[key] = item[key];
        }
    });
    showRiskModal.value = true;
};

const submitRisk = () => {
    if (editRiskMode.value && editingRiskId.value) {
        riskForm.put(route('risks.update', editingRiskId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showRiskModal.value = false;
                riskForm.reset();
            }
        });
    } else {
        riskForm.post(route('risks.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showRiskModal.value = false;
                riskForm.reset();
            }
        });
    }
};

const deleteRisk = (id: number) => {
    if (confirm('Yakin hapus risk ini?')) {
        router.delete(route('risks.destroy', id), { preserveScroll: true });
    }
};

const getRiskColor = (level: string) => {
    switch (level) {
        case 'Critical': return 'text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400';
        case 'High': return 'text-orange-600 bg-orange-100 dark:bg-orange-900/30 dark:text-orange-400';
        case 'Medium': return 'text-amber-600 bg-amber-100 dark:bg-amber-900/30 dark:text-amber-400';
        case 'Low': return 'text-emerald-600 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400';
        default: return 'text-gray-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-400';
    }
};

// --- ISSUE LOGIC ---
const showIssueModal = ref(false);
const editIssueMode = ref(false);
const editingIssueId = ref<number | null>(null);
const issueForm = useForm({
    project_id: props.project.id,
    issue_code: '',
    title: '',
    impact: 'Medium',
    owner_id: '',
    status: 'Open',
    action: '',
    deadline: '',
});

const openCreateIssue = () => {
    editIssueMode.value = false;
    editingIssueId.value = null;
    issueForm.reset();
    issueForm.project_id = props.project.id;
    showIssueModal.value = true;
};

const openEditIssue = (item: any) => {
    editIssueMode.value = true;
    editingIssueId.value = item.id;
    Object.keys(issueForm.data()).forEach(key => {
        if (item[key] !== undefined) {
            (issueForm as any)[key] = item[key];
        }
    });
    showIssueModal.value = true;
};

const submitIssue = () => {
    if (editIssueMode.value && editingIssueId.value) {
        issueForm.put(route('issues.update', editingIssueId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showIssueModal.value = false;
                issueForm.reset();
            }
        });
    } else {
        issueForm.post(route('issues.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showIssueModal.value = false;
                issueForm.reset();
            }
        });
    }
};

const deleteIssue = (id: number) => {
    if (confirm('Yakin hapus issue ini?')) {
        router.delete(route('issues.destroy', id), { preserveScroll: true });
    }
};

// --- CHANGE REQUEST LOGIC ---
const showCRModal = ref(false);
const editCRMode = ref(false);
const editingCRId = ref<number | null>(null);
const crForm = useForm({
    project_id: props.project.id,
    cr_code: '',
    title: '',
    description: '',
    requester_id: '',
    status: 'Pending',
    impact_analysis: '',
});

const openCreateCR = () => {
    editCRMode.value = false;
    editingCRId.value = null;
    crForm.reset();
    crForm.project_id = props.project.id;
    showCRModal.value = true;
};

const openEditCR = (item: any) => {
    editCRMode.value = true;
    editingCRId.value = item.id;
    Object.keys(crForm.data()).forEach(key => {
        if (item[key] !== undefined) {
            (crForm as any)[key] = item[key];
        }
    });
    showCRModal.value = true;
};

const submitCR = () => {
    if (editCRMode.value && editingCRId.value) {
        crForm.put(route('change-requests.update', editingCRId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showCRModal.value = false;
                crForm.reset();
            }
        });
    } else {
        crForm.post(route('change-requests.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showCRModal.value = false;
                crForm.reset();
            }
        });
    }
};

const deleteCR = (id: number) => {
    if (confirm('Yakin hapus Change Request ini?')) {
        router.delete(route('change-requests.destroy', id), { preserveScroll: true });
    }
};

// --- VENDORS LOGIC (Otomatis dari Tasks) ---
const projectVendors = computed(() => {
    if (!props.project.tasks) return [];

    const vendorsMap = new Map();

    props.project.tasks.forEach((task: any) => {
        if (task.vendor) {
            if (!vendorsMap.has(task.vendor.id)) {
                vendorsMap.set(task.vendor.id, {
                    ...task.vendor,
                    related_tasks: [task.task_code]
                });
            } else {
                const v = vendorsMap.get(task.vendor.id);
                // Biar nggak duplikat kode task-nya
                if (!v.related_tasks.includes(task.task_code)) {
                    v.related_tasks.push(task.task_code);
                }
            }
        }
    });

    return Array.from(vendorsMap.values());
});

// --- MEETINGS LOGIC ---
const showMeetingModal = ref(false);
const editMeetingMode = ref(false);
const editingMeetingId = ref<number | null>(null);
const meetingForm = useForm({
    project_id: props.project.id,
    title: '',
    meeting_date: '',
    platform: 'Google Meet',
    meeting_link: '',
    status: 'Scheduled',
    notes: '',
});

const openCreateMeeting = () => {
    editMeetingMode.value = false;
    editingMeetingId.value = null;
    meetingForm.reset();
    meetingForm.project_id = props.project.id;
    showMeetingModal.value = true;
};

const openEditMeeting = (item: any) => {
    editMeetingMode.value = true;
    editingMeetingId.value = item.id;
    Object.keys(meetingForm.data()).forEach(key => {
        if (item[key] !== undefined) {
            (meetingForm as any)[key] = item[key];
        }
    });
    showMeetingModal.value = true;
};

const submitMeeting = () => {
    if (editMeetingMode.value && editingMeetingId.value) {
        meetingForm.put(route('meetings.update', editingMeetingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                showMeetingModal.value = false;
                meetingForm.reset();
            }
        });
    } else {
        meetingForm.post(route('meetings.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showMeetingModal.value = false;
                meetingForm.reset();
            }
        });
    }
};

const deleteMeeting = (id: number) => {
    if (confirm('Yakin ingin menghapus jadwal meeting ini?')) {
        router.delete(route('meetings.destroy', id), { preserveScroll: true });
    }
};
</script>

<template>

    <Head :title="project.name" />

    <AppLayout>
        <div class="max-w-7xl mx-auto space-y-6">

            <div class="flex items-center gap-4">
                <Link :href="route('projects.index')"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm font-medium">
                    &larr; Back to Projects
                </Link>
            </div>

            <!-- PROJECT HEADER -->
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ project.name }}</h1>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                            {{ project.client?.name || 'Unknown Client' }} • PM: <span
                                class="font-medium text-gray-700 dark:text-gray-300">{{ project.project_manager?.name ||
                                    'Unassigned' }}</span>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div
                            class="px-3 py-1 rounded-lg border bg-gray-50 dark:bg-gray-700 dark:border-gray-600 text-xs font-semibold text-gray-900 dark:text-white">
                            Priority: {{ project.priority }}
                        </div>
                        <div
                            :class="['px-3 py-1 rounded-lg border text-xs font-semibold', getHealthColor(project.status)]">
                            Status: {{ project.status }}
                        </div>
                        <div
                            :class="['px-3 py-1 rounded-lg border text-xs font-semibold', daysRemaining < 0 ? 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-700 dark:text-gray-200']">
                            Days Left: {{ daysRemaining }}
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Project Progress</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ project.progress_percentage
                            }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-500"
                            :style="`width: ${project.progress_percentage}%`"></div>
                    </div>
                </div>
            </div>

            <!-- TABS NAVIGATION -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-200">
                <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
                    <nav class="flex space-x-1 px-4" aria-label="Tabs">
                        <button v-for="tab in tabs" :key="tab" @click="activeTab = tab" :class="[
                            activeTab === tab
                                ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300',
                            'whitespace-nowrap py-4 px-3 border-b-2 font-medium text-sm transition-colors'
                        ]">
                            {{ tab }}
                        </button>
                    </nav>
                </div>

                <!-- TAB CONTENT AREA -->
                <div class="p-6 min-h-[400px]">

                    <!-- TAB: OVERVIEW -->
                    <div v-if="activeTab === 'Overview'" class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Project Overview</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                            {{ project.description || 'Tidak ada deskripsi untuk project ini.' }}
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400">Project
                                    Type</span>
                                <span class="block mt-1 font-semibold text-gray-900 dark:text-white">{{
                                    project.project_type?.name || '-' }}</span>
                            </div>
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400">Current
                                    Phase</span>
                                <span class="block mt-1 font-semibold text-gray-900 dark:text-white">{{
                                    project.current_phase?.name || '-' }}</span>
                            </div>
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400">Start
                                    Date</span>
                                <span class="block mt-1 font-semibold text-gray-900 dark:text-white">{{
                                    project.start_date || '-' }}</span>
                            </div>
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
                                <span class="block text-xs font-medium text-gray-500 dark:text-gray-400">Deadline</span>
                                <span class="block mt-1 font-semibold text-gray-900 dark:text-white">{{
                                    project.target_completion }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: TASKS -->
                    <div v-if="activeTab === 'Tasks'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tasks Management</h3>
                            <button @click="openCreateTask()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                + Add Task
                            </button>
                        </div>

                        <!-- Form Inline Add Task -->
                        <FormModal :show="showTaskModal" @close="showTaskModal = false" @submit="submitTask" :title="editTaskMode ? 'Edit Task' : 'Add Task'" :processing="taskForm.processing">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Task
                                            Code *</label>
                                        <input v-model="taskForm.task_code" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Task
                                            Name *</label>
                                        <input v-model="taskForm.name" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Assign
                                            To (Internal)</label>
                                        <select v-model="taskForm.assigned_user_id"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="">Unassigned</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name
                                                }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Vendor
                                            (Optional)</label>
                                        <select v-model="taskForm.vendor_id"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="">No Vendor</option>
                                            <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">{{
                                                vendor.name }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Deadline</label>
                                        <input v-model="taskForm.deadline" type="date"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    </div>
                                </div>
                                </FormModal>

                        <!-- Table Tasks -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Code</th>
                                        <th class="px-4 py-3 w-full">Task Name</th>
                                        <th class="px-4 py-3">Assignee / Vendor</th>
                                        <th class="px-4 py-3">Deadline</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="task in project.tasks" :key="task.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-4 py-3 font-medium text-indigo-600 dark:text-indigo-400">{{
                                            task.task_code }}</td>
                                        <td class="px-4 py-3 text-gray-900 dark:text-white">{{ task.name }}</td>
                                        <td class="px-4 py-3">
                                            <div v-if="task.assigned_user" class="text-xs">👤 {{ task.assigned_user.name
                                                }}</div>
                                            <div v-if="task.vendor" class="text-xs text-amber-600 dark:text-amber-400">
                                                🏢 {{ task.vendor.name }}</div>
                                            <span v-if="!task.assigned_user && !task.vendor"
                                                class="text-gray-400">-</span>
                                        </td>
                                        <td class="px-4 py-3">{{ task.deadline || '-' }}</td>
                                        <td class="px-4 py-3">
                                            <StatusBadge :status="
                                                    task.status .trim()" />
                                        </td>
                                        <td class="px-4 py-3 text-right space-x-2">
                                            <button @click="openEditTask(task)" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs mr-2">Edit</button>
                                            <button @click="deleteTask(task.id)" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!project.tasks || project.tasks.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada task untuk project ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB: MILESTONES (Sekarang sudah di luar blok Tasks) -->
                    <div v-if="activeTab === 'Milestones'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Project Milestones</h3>
                            <button @click="openCreateMilestone()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                + Add Milestone
                            </button>
                        </div>

                        <!-- Form Inline Milestone -->
                        <FormModal :show="showMilestoneModal" @close="showMilestoneModal = false" @submit="submitMilestone" :title="editMilestoneMode ? 'Edit Milestone' : 'Add Milestone'" :processing="milestoneForm.processing">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Milestone
                                            Name *</label>
                                        <input v-model="milestoneForm.name" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Due
                                            Date *</label>
                                        <input v-model="milestoneForm.due_date" type="date"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                </div>
                                </FormModal>

                        <!-- Table Milestones -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Milestone Name</th>
                                        <th class="px-4 py-3">Due Date</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="ms in project.milestones" :key="ms.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ ms.name }}</td>
                                        <td class="px-4 py-3">{{ ms.due_date }}</td>
                                        <td class="px-4 py-3"><StatusBadge :status="
                                                    ms.status .trim()" /></td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="openEditMilestone(ms)" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs mr-2">Edit</button>
                                            <button @click="deleteMilestone(ms.id)" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!project.milestones || project.milestones.length === 0">
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada milestone.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB: RISKS -->
                    <div v-if="activeTab === 'Risks'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Risk Management</h3>
                            <button @click="openCreateRisk()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                + Log Risk
                            </button>
                        </div>

                        <!-- Form Inline Risk -->
                        <FormModal :show="showRiskModal" @close="showRiskModal = false" @submit="submitRisk" :title="editRiskMode ? 'Edit Risk' : 'Add Risk'" :processing="riskForm.processing">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Risk
                                            Code *</label>
                                        <input v-model="riskForm.risk_code" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required placeholder="RSK-001">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Risk
                                            Title *</label>
                                        <input v-model="riskForm.title" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Risk
                                            Level</label>
                                        <select v-model="riskForm.risk_level"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Critical">Critical</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Owner
                                            / PIC</label>
                                        <select v-model="riskForm.owner_id"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="">Unassigned</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name
                                                }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                        <select v-model="riskForm.status"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="Open">Open</option>
                                            <option value="Mitigated">Mitigated</option>
                                            <option value="Closed">Closed</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Mitigation
                                        Plan</label>
                                    <textarea v-model="riskForm.mitigation" rows="2"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"></textarea>
                                </div>
                                </FormModal>

                        <!-- Table Risks -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Code</th>
                                        <th class="px-4 py-3 w-full">Risk Title</th>
                                        <th class="px-4 py-3">Owner</th>
                                        <th class="px-4 py-3">Level</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="risk in project.risks" :key="risk.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 font-medium text-indigo-600 dark:text-indigo-400">{{
                                            risk.risk_code }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ risk.title }}
                                        </td>
                                        <td class="px-4 py-3">{{ risk.owner?.name || '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                :class="['px-2 py-1 rounded text-xs font-bold', getRiskColor(risk.risk_level)]">
                                                {{ risk.risk_level }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3"><StatusBadge :status="
                                                    risk.status .trim()" /></td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="openEditRisk(risk)" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs mr-2">Edit</button>
                                            <button @click="deleteRisk(risk.id)" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!project.risks || project.risks.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada risk log.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB: ISSUES -->
                    <div v-if="activeTab === 'Issues'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Issue Management</h3>
                            <button @click="openCreateIssue()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                + Log Issue
                            </button>
                        </div>

                        <!-- Form Inline Issue -->
                        <FormModal :show="showIssueModal" @close="showIssueModal = false" @submit="submitIssue" :title="editIssueMode ? 'Edit Issue' : 'Add Issue'" :processing="issueForm.processing">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Issue
                                            Code *</label>
                                        <input v-model="issueForm.issue_code" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required placeholder="ISS-001">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Issue
                                            Title *</label>
                                        <input v-model="issueForm.title" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Impact
                                            Level</label>
                                        <select v-model="issueForm.impact"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Owner
                                            / PIC</label>
                                        <select v-model="issueForm.owner_id"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="">Unassigned</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name
                                                }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Deadline</label>
                                        <input v-model="issueForm.deadline" type="date"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Corrective
                                        Action</label>
                                    <textarea v-model="issueForm.action" rows="2"
                                        class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"></textarea>
                                </div>
                                </FormModal>

                        <!-- Table Issues -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Code</th>
                                        <th class="px-4 py-3 w-full">Issue Title</th>
                                        <th class="px-4 py-3">Owner</th>
                                        <th class="px-4 py-3">Impact</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="issue in project.issues" :key="issue.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 font-medium text-indigo-600 dark:text-indigo-400">{{
                                            issue.issue_code }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ issue.title }}
                                        </td>
                                        <td class="px-4 py-3">{{ issue.owner?.name || '-' }}</td>
                                        <td class="px-4 py-3">{{ issue.impact }}</td>
                                        <td class="px-4 py-3"><StatusBadge :status="
                                                    issue.status .trim()" /></td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="openEditIssue(issue)" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs mr-2">Edit</button>
                                            <button @click="deleteIssue(issue.id)" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!project.issues || project.issues.length === 0">
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada issue terdaftar.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB: CHANGE REQUESTS -->
                    <div v-if="activeTab === 'Change Requests'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Change Requests</h3>
                            <button @click="openCreateCR()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                + New CR
                            </button>
                        </div>

                        <!-- Form Inline CR -->
                        <FormModal :show="showCRModal" @close="showCRModal = false" @submit="submitCR" :title="editCRMode ? 'Edit CR' : 'Add CR'" :processing="crForm.processing">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">CR
                                            Code *</label>
                                        <input v-model="crForm.cr_code" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required placeholder="CR-001">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">CR
                                            Title *</label>
                                        <input v-model="crForm.title" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                        <select v-model="crForm.status"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="Pending">Pending</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Requester</label>
                                        <select v-model="crForm.requester_id"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="">Unknown</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name
                                                }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Description
                                            *</label>
                                        <textarea v-model="crForm.description" rows="2"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required></textarea>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Impact
                                            Analysis</label>
                                        <textarea v-model="crForm.impact_analysis" rows="2"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            placeholder="Dampak waktu/biaya..."></textarea>
                                    </div>
                                </div>
                                </FormModal>

                        <!-- Table CR -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Code</th>
                                        <th class="px-4 py-3 w-full">CR Title</th>
                                        <th class="px-4 py-3">Requester</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="cr in project.change_requests" :key="cr.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 font-medium text-indigo-600 dark:text-indigo-400">{{
                                            cr.cr_code }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ cr.title }}
                                        </td>
                                        <td class="px-4 py-3">{{ cr.requester?.name || '-' }}</td>
                                        <td class="px-4 py-3">
                                            <StatusBadge :status="cr.status" />
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="openEditCR(cr)" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs mr-2">Edit</button>
                                            <button @click="deleteCR(cr.id)" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!project.change_requests || project.change_requests.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada usulan perubahan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB: VENDORS -->
                    <div v-if="activeTab === 'Vendors'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Involved Vendors</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Daftar vendor diambil otomatis dari
                                penugasan Tasks.</p>
                        </div>

                        <!-- Table Vendors -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Vendor Code</th>
                                        <th class="px-4 py-3">Vendor Name</th>
                                        <th class="px-4 py-3">Contact Person</th>
                                        <th class="px-4 py-3">Related Tasks</th>
                                        <th class="px-4 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="vendor in projectVendors" :key="vendor.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 font-medium text-indigo-600 dark:text-indigo-400">{{
                                            vendor.vendor_code }}</td>
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ vendor.name }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ vendor.contact_person || '-' }}
                                            <div class="text-xs text-gray-500 mt-0.5">{{ vendor.phone || '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                <span v-for="tcode in vendor.related_tasks" :key="tcode"
                                                    class="px-2 py-0.5 bg-gray-100 dark:bg-gray-600 rounded text-xs font-medium">
                                                    {{ tcode }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <StatusBadge :status="vendor.status" />
                                        </td>
                                    </tr>
                                    <tr v-if="projectVendors.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada vendor yang di-assign ke task pada project ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB: MEETINGS -->
                    <div v-if="activeTab === 'Meetings'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Project Meetings</h3>
                            <button @click="openCreateMeeting()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                + Schedule Meeting
                            </button>
                        </div>

                        <!-- Form Inline Meeting -->
                        <FormModal :show="showMeetingModal" @close="showMeetingModal = false" @submit="submitMeeting" :title="editMeetingMode ? 'Edit Meeting' : 'Add Meeting'" :processing="meetingForm.processing">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Meeting
                                            Title *</label>
                                        <input v-model="meetingForm.title" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date
                                            & Time *</label>
                                        <input v-model="meetingForm.meeting_date" type="datetime-local"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                        <select v-model="meetingForm.status"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                            <option value="Scheduled">Scheduled</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Platform
                                            / Location</label>
                                        <input v-model="meetingForm.platform" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm"
                                            placeholder="Zoom, GMeet, Offline">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Meeting
                                            Link / Address</label>
                                        <input v-model="meetingForm.meeting_link" type="text"
                                            class="w-full text-sm rounded-lg border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm">
                                    </div>
                                </div>
                                </FormModal>

                        <!-- Table Meetings -->
                        <BentoCard noPadding>
                            <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 font-semibold border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Meeting Title</th>
                                        <th class="px-4 py-3">Date & Time</th>
                                        <th class="px-4 py-3">Platform</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="meeting in project.meetings" :key="meeting.id"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ meeting.title
                                        }}</td>
                                        <td class="px-4 py-3">{{ meeting.meeting_date }}</td>
                                        <td class="px-4 py-3">
                                            {{ meeting.platform }}
                                            <a v-if="meeting.meeting_link" :href="meeting.meeting_link" target="_blank"
                                                class="block text-xs text-indigo-500 hover:underline">Join Link
                                                &nearr;</a>
                                        </td>
                                        <td class="px-4 py-3">
                                            <StatusBadge :status="meeting.status" />
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button @click="openEditMeeting(meeting)" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs mr-2">Edit</button>
                                            <button @click="deleteMeeting(meeting.id)" class="text-red-600 hover:text-red-700 font-medium text-xs">Delete</button>
                                        </td>
                                    </tr>
                                    <tr v-if="!project.meetings || project.meetings.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada jadwal meeting.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </BentoCard>
                    </div>

                    <!-- TAB LAINNYA (Placeholder) -->
                    <div v-if="['Activity'].includes(activeTab)">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ activeTab }}</h3>
                        <div
                            class="flex items-center justify-center h-48 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl text-gray-400 dark:text-gray-500 text-sm">
                            [ Modul {{ activeTab }} sedang dalam tahap pengembangan ]
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>
