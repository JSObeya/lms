<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import {
    Users,
    UserPlus,
    UploadCloud,
    Search,
    Shield,
    CheckCircle2,
    XCircle,
    UserCheck,
    UserX,
    FileSpreadsheet,
    AlertCircle,
    Edit2,
    Trash2,
    RotateCcw,
    AlertTriangle,
    X,
    Briefcase,
    Building2,
    ShieldAlert,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    users: Object,
    roles: Array,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');
const selectedStatus = ref(props.filters.status || '');
const currentTab = ref(props.filters.trashed || 'active_only');

// Bulk Selection State
const selectedUsers = ref([]);

const isAllSelected = computed(() => {
    return props.users.data.length > 0 && props.users.data.every((u) => selectedUsers.value.includes(u.id));
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedUsers.value = [];
    } else {
        selectedUsers.value = props.users.data.map((u) => u.id);
    }
};

const deselectAll = () => {
    selectedUsers.value = [];
};

const bulkApply = (action, extraParams = {}) => {
    if (selectedUsers.value.length === 0) return;

    if (action === 'move_to_trash' && !confirm(`Are you sure you want to move ${selectedUsers.value.length} selected users to the trash?`)) {
        return;
    }

    router.post(
        route('admin.users.bulk-action'),
        {
            user_ids: selectedUsers.value,
            action: action,
            ...extraParams,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedUsers.value = [];
            },
        }
    );
};

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showImportModal = ref(false);
const showDeleteConfirmModal = ref(false);
const showForceDeleteModal = ref(false);

const targetUser = ref(null);

// Create User Form
const createForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    institution: 'Government Technical College, Benue',
    state: 'Benue',
    trade_specialization: 'Solar PV Installation',
    role: 'teacher',
    account_status: 'active',
    password: '',
});

// Edit User Form
const editForm = useForm({
    id: null,
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    institution: '',
    state: '',
    trade_specialization: '',
    role: 'teacher',
    account_status: 'active',
    password: '',
});

// Import Form
const importForm = useForm({
    csv_file: null,
});

const applyFilters = () => {
    router.get(
        route('admin.users.index'),
        {
            search: search.value || undefined,
            role: selectedRole.value || undefined,
            status: selectedStatus.value || undefined,
            trashed: currentTab.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const switchTab = (tab) => {
    currentTab.value = tab;
    applyFilters();
};

const openCreateModal = () => {
    createForm.reset();
    createForm.clearErrors();
    showCreateModal.value = true;
};

const submitCreate = () => {
    createForm.post(route('admin.users.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

const openEditModal = (user) => {
    targetUser.value = user;
    editForm.clearErrors();
    editForm.id = user.id;
    editForm.first_name = user.first_name || user.name.split(' ')[0] || '';
    editForm.last_name = user.last_name || user.name.split(' ').slice(1).join(' ') || '';
    editForm.email = user.email;
    editForm.phone = user.phone || '';
    editForm.institution = user.institution || '';
    editForm.state = user.state || 'Benue';
    editForm.trade_specialization = user.trade_specialization || '';
    editForm.role = user.roles[0]?.name || 'teacher';
    editForm.account_status = user.account_status || 'active';
    editForm.password = '';
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route('admin.users.update', editForm.id), {
        onSuccess: () => {
            showEditModal.value = false;
            editForm.reset();
        },
    });
};

const confirmSoftDelete = (user) => {
    targetUser.value = user;
    showDeleteConfirmModal.value = true;
};

const executeSoftDelete = () => {
    if (!targetUser.value) return;
    router.delete(route('admin.users.destroy', targetUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteConfirmModal.value = false;
            targetUser.value = null;
        },
    });
};

const restoreUser = (userId) => {
    router.post(route('admin.users.restore', userId), {}, {
        preserveScroll: true,
    });
};

const confirmForceDelete = (user) => {
    targetUser.value = user;
    showForceDeleteModal.value = true;
};

const executeForceDelete = () => {
    if (!targetUser.value) return;
    router.delete(route('admin.users.force-delete', targetUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showForceDeleteModal.value = false;
            targetUser.value = null;
        },
    });
};

const toggleStatus = (userId) => {
    router.post(route('admin.users.toggle-status', userId), {}, {
        preserveScroll: true,
    });
};

const submitImport = () => {
    importForm.post(route('admin.users.bulk-import'), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};
</script>

<template>
    <Head title="User Administration &amp; Component Oversight — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2.5">
                        <span class="p-2 rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-500/20">
                            <Users class="w-5 h-5" />
                        </span>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                User Administration &amp; 200 TVET Teachers
                            </h1>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Add, edit, manage, suspend, soft-delete, and restore user accounts with automatic audit trail logging.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2.5">
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-md transition"
                    >
                        <UserPlus class="w-4 h-4" />
                        <span>Add New User</span>
                    </button>

                    <button
                        @click="showImportModal = true"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs border border-slate-200 dark:border-slate-700 shadow-sm transition"
                    >
                        <UploadCloud class="w-4 h-4 text-brand-600 dark:text-brand-400" />
                        <span>Bulk 200 CSV Import</span>
                    </button>

                    <Link
                        :href="route('admin.audit-logs.index')"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 font-semibold text-xs transition"
                        title="View System Audit Trail"
                    >
                        <Shield class="w-3.5 h-3.5" />
                        <span class="hidden md:inline">System Audit</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- User Metric Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-[11px] text-slate-500 font-medium">Active Accounts</span>
                    <div class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ stats.total_active }}</div>
                    <span class="text-[10px] text-emerald-600 font-semibold">Ready for LMS</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-[11px] text-slate-500 font-medium">IDEAS-TVET Teachers</span>
                    <div class="text-xl font-black text-brand-700 dark:text-brand-400 mt-1">{{ stats.teachers_count }}</div>
                    <span class="text-[10px] text-slate-400">Target: 200 instructors</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-[11px] text-slate-500 font-medium">Trainees &amp; Students</span>
                    <div class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ stats.students_count }}</div>
                    <span class="text-[10px] text-slate-400">Enrolled learners</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-[11px] text-slate-500 font-medium">Facilitators</span>
                    <div class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ stats.facilitators_count }}</div>
                    <span class="text-[10px] text-slate-400">Station assessors</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-[11px] text-slate-500 font-medium">Suspended</span>
                    <div class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ stats.total_suspended }}</div>
                    <span class="text-[10px] text-rose-500">Access paused</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-[11px] text-slate-500 font-medium">Trashed / Soft-Deleted</span>
                    <div class="text-xl font-black text-slate-600 dark:text-slate-300 mt-1">{{ stats.total_trashed }}</div>
                    <span class="text-[10px] text-indigo-500 font-semibold">Recoverable</span>
                </div>
            </div>

            <!-- Tabs & Filters Row -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <!-- Status Tabs -->
                <div class="flex items-center space-x-1.5 border-b border-slate-200 dark:border-slate-800 pb-3 overflow-x-auto">
                    <button
                        @click="switchTab('active_only')"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2',
                            currentTab === 'active_only'
                                ? 'bg-brand-50 text-brand-800 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200 dark:border-brand-800 shadow-sm'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                        ]"
                    >
                        <span>Active Accounts</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-brand-100 dark:bg-brand-900/60 text-brand-800 dark:text-brand-300">{{ stats.total_active }}</span>
                    </button>

                    <button
                        @click="switchTab('only_trashed')"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2',
                            currentTab === 'only_trashed'
                                ? 'bg-rose-50 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 shadow-sm'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                        ]"
                    >
                        <Trash2 class="w-3.5 h-3.5" />
                        <span>Trash Bin (Soft-Deleted)</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-rose-100 dark:bg-rose-900/60 text-rose-800 dark:text-rose-300">{{ stats.total_trashed }}</span>
                    </button>

                    <button
                        @click="switchTab('with_trashed')"
                        :class="[
                            'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2',
                            currentTab === 'with_trashed'
                                ? 'bg-slate-200 text-slate-800 dark:bg-slate-800 dark:text-white border border-slate-300 dark:border-slate-700'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'
                        ]"
                    >
                        <span>All Accounts (Including Trash)</span>
                    </button>
                </div>

                <!-- Search & Dropdown Filters -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="lg:col-span-2 relative">
                        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            v-model="search"
                            @keyup.enter="applyFilters"
                            placeholder="Search by name, email, institution, trade, or phone..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                        />
                    </div>

                    <div>
                        <select
                            v-model="selectedRole"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="">All Roles</option>
                            <option v-for="r in roles" :key="r.id" :value="r.name">
                                {{ r.name.toUpperCase() }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <select
                            v-model="selectedStatus"
                            @change="applyFilters"
                            class="w-full py-2.5 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="">All Account Statuses</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Floating Bar -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 -translate-y-2 scale-95"
            >
                <div
                    v-if="selectedUsers.length > 0"
                    class="mb-4 p-4 rounded-2xl bg-gradient-to-r from-slate-900 via-brand-950 to-slate-900 border border-brand-500/50 text-white shadow-xl shadow-brand-950/20 flex flex-wrap items-center justify-between gap-3"
                >
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full bg-brand-600/30 border border-brand-500/40 text-rose-200 text-xs font-bold">
                            {{ selectedUsers.length }} user(s) selected
                        </span>
                        <span class="text-xs text-slate-300 hidden sm:inline">Bulk actions for selected accounts:</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Convert to Teacher -->
                        <button
                            type="button"
                            @click="bulkApply('change_role', { role: 'teacher' })"
                            class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-brand-700 to-rose-700 hover:from-brand-600 hover:to-rose-600 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-md"
                            title="Set role of all selected accounts to Teacher"
                        >
                            <Briefcase class="w-3.5 h-3.5 text-rose-300" />
                            <span>Make Teacher</span>
                        </button>

                        <!-- Activate -->
                        <button
                            type="button"
                            @click="bulkApply('set_status', { status: 'active' })"
                            class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-md"
                        >
                            <UserCheck class="w-3.5 h-3.5" />
                            <span>Activate</span>
                        </button>

                        <!-- Suspend -->
                        <button
                            type="button"
                            @click="bulkApply('set_status', { status: 'suspended' })"
                            class="px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-md"
                        >
                            <UserX class="w-3.5 h-3.5" />
                            <span>Suspend</span>
                        </button>

                        <!-- Restore or Move to Trash -->
                        <template v-if="currentTab === 'only_trashed'">
                            <button
                                type="button"
                                @click="bulkApply('restore')"
                                class="px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-md"
                            >
                                <RotateCcw class="w-3.5 h-3.5" />
                                <span>Restore Selected</span>
                            </button>
                        </template>
                        <template v-else>
                            <button
                                type="button"
                                @click="bulkApply('move_to_trash')"
                                class="px-3 py-1.5 rounded-xl bg-rose-800 hover:bg-rose-700 text-white text-xs font-bold transition flex items-center space-x-1.5 shadow-md"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                                <span>Move to Trash</span>
                            </button>
                        </template>

                        <button
                            type="button"
                            @click="deselectAll"
                            class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-medium transition"
                        >
                            Clear Selection
                        </button>
                    </div>
                </div>
            </Transition>

            <!-- Users Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center space-x-2">
                        <Users class="w-4 h-4 text-brand-600 dark:text-brand-400" />
                        <span>
                            {{ currentTab === 'only_trashed' ? 'Soft-Deleted Users (Trash Bin)' : 'System Registered Accounts' }}
                        </span>
                    </h3>
                    <div class="flex items-center space-x-3 text-xs text-slate-400">
                        <span v-if="selectedUsers.length > 0" class="font-semibold text-brand-600 dark:text-brand-400">
                            {{ selectedUsers.length }} selected
                        </span>
                        <span>Total: {{ users.total }} users</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-[11px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3 w-10 text-center">
                                    <input
                                        type="checkbox"
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="rounded border-slate-300 dark:border-slate-700 text-brand-700 focus:ring-brand-500 cursor-pointer w-4 h-4"
                                        title="Select all accounts on this page"
                                    />
                                </th>
                                <th class="px-5 py-3">User / Name (Click to Edit)</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Institution &amp; State</th>
                                <th class="px-5 py-3">Trade Specialization</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Actions &amp; Oversight</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-if="users.data.length === 0">
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 font-sans">
                                    {{ currentTab === 'only_trashed' ? 'The Trash Bin is empty. No soft-deleted accounts found.' : 'No users match your query.' }}
                                </td>
                            </tr>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                :class="[
                                    'transition',
                                    selectedUsers.includes(user.id)
                                        ? 'bg-brand-50/60 dark:bg-brand-950/40 ring-1 ring-inset ring-brand-500/30'
                                        : 'hover:bg-slate-50/70 dark:hover:bg-slate-800/40',
                                    user.deleted_at ? 'bg-rose-50/20 dark:bg-rose-950/20' : ''
                                ]"
                            >
                                <td class="px-4 py-3.5 text-center">
                                    <input
                                        type="checkbox"
                                        :value="user.id"
                                        v-model="selectedUsers"
                                        class="rounded border-slate-300 dark:border-slate-700 text-brand-700 focus:ring-brand-500 cursor-pointer w-4 h-4"
                                    />
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center space-x-2.5">
                                        <button
                                            type="button"
                                            @click="openEditModal(user)"
                                            class="w-8 h-8 rounded-full bg-brand-100 hover:bg-brand-200 dark:bg-brand-950/60 dark:hover:bg-brand-900 text-brand-800 dark:text-brand-300 font-bold flex items-center justify-center text-xs shrink-0 transition"
                                            title="Click to edit user profile"
                                        >
                                            {{ user.name.charAt(0) }}
                                        </button>
                                        <div>
                                            <button
                                                type="button"
                                                @click="openEditModal(user)"
                                                class="font-bold text-slate-900 dark:text-white hover:text-brand-700 dark:hover:text-brand-400 flex items-center space-x-1.5 transition text-left group"
                                                title="Click name to edit details"
                                            >
                                                <span class="group-hover:underline underline-offset-2">{{ user.name }}</span>
                                                <Edit2 class="w-3 h-3 text-brand-600 dark:text-brand-400 opacity-60 group-hover:opacity-100 transition shrink-0" />
                                                <span v-if="user.deleted_at" class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300 ml-1">
                                                    Trashed
                                                </span>
                                            </button>
                                            <div class="text-[11px] text-slate-400">
                                                {{ user.email }} · @{{ user.username || 'user' }}
                                                <span v-if="user.phone"> · {{ user.phone }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        {{ user.roles[0]?.name || 'Student' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-medium">
                                    <div>{{ user.institution || 'GTC Makurdi' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ user.state || 'Benue' }} State</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-brand-700 dark:text-brand-400 font-semibold">
                                        {{ user.trade_specialization || 'Technical Education' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                            user.account_status === 'active'
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                                : user.account_status === 'suspended'
                                                    ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'
                                                    : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                                        ]"
                                    >
                                        {{ user.account_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <!-- Trashed Actions -->
                                    <template v-if="user.deleted_at">
                                        <button
                                            @click="restoreUser(user.id)"
                                            class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition mr-2"
                                            title="Restore this account from trash"
                                        >
                                            <RotateCcw class="w-3.5 h-3.5" />
                                            <span>Restore</span>
                                        </button>

                                        <button
                                            @click="confirmForceDelete(user)"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 transition"
                                            title="Permanently remove account"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                            <span>Purge</span>
                                        </button>
                                    </template>

                                    <!-- Active User Actions -->
                                    <template v-else>
                                        <button
                                            @click="openEditModal(user)"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition mr-1.5"
                                            title="Edit user details"
                                        >
                                            <Edit2 class="w-3.5 h-3.5" />
                                            <span>Edit</span>
                                        </button>

                                        <button
                                            @click="toggleStatus(user.id)"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition mr-1.5"
                                            :class="user.account_status === 'active'
                                                ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800'
                                                : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800'"
                                            :title="user.account_status === 'active' ? 'Suspend account' : 'Activate account'"
                                        >
                                            {{ user.account_status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>

                                        <button
                                            @click="confirmSoftDelete(user)"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                                            title="Soft delete user (move to trash)"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div v-if="users.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Page {{ users.current_page }} of {{ users.last_page }}
                    </span>
                    <div class="flex items-center space-x-1">
                        <Link
                            v-for="(link, i) in users.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-semibold transition',
                                link.active
                                    ? 'bg-brand-700 text-white'
                                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800',
                                !link.url ? 'opacity-40 cursor-not-allowed' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 rounded-xl bg-brand-500/10 text-brand-600">
                            <UserPlus class="w-5 h-5" />
                        </div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Create New User Account
                        </h3>
                    </div>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="createForm.first_name"
                                required
                                placeholder="e.g. Emmanuel"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Last Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="createForm.last_name"
                                required
                                placeholder="e.g. Terver"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="email"
                                v-model="createForm.email"
                                required
                                placeholder="user@gregicthub.com"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Phone Number
                            </label>
                            <input
                                type="text"
                                v-model="createForm.phone"
                                placeholder="+234..."
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Role Assignment <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="createForm.role"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            >
                                <option v-for="r in roles" :key="r.id" :value="r.name">
                                    {{ r.name.toUpperCase() }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Account Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="createForm.account_status"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            >
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Technical Institution
                            </label>
                            <input
                                type="text"
                                v-model="createForm.institution"
                                placeholder="e.g. GTC Makurdi"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                State
                            </label>
                            <input
                                type="text"
                                v-model="createForm.state"
                                placeholder="e.g. Benue"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Trade Specialization
                        </label>
                        <input
                            type="text"
                            v-model="createForm.trade_specialization"
                            placeholder="e.g. Solar PV Installation, Electrical, Welding"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                        />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Password (Optional - Defaults to TVET_Teacher_2026!)
                        </label>
                        <PasswordInput
                            v-model="createForm.password"
                            placeholder="Enter initial password or leave blank for default"
                        />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="px-4 py-2 font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-md transition"
                        >
                            Save Account &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            <Edit2 class="w-5 h-5 text-brand-600 dark:text-brand-400" />
                        </div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Edit User: {{ targetUser?.name }}
                        </h3>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit" class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                First Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="editForm.first_name"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Last Name <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="editForm.last_name"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="email"
                                v-model="editForm.email"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Phone Number
                            </label>
                            <input
                                type="text"
                                v-model="editForm.phone"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Role Assignment <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="editForm.role"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            >
                                <option v-for="r in roles" :key="r.id" :value="r.name">
                                    {{ r.name.toUpperCase() }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Account Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="editForm.account_status"
                                required
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            >
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Technical Institution
                            </label>
                            <input
                                type="text"
                                v-model="editForm.institution"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                                State
                            </label>
                            <input
                                type="text"
                                v-model="editForm.state"
                                class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Trade Specialization
                        </label>
                        <input
                            type="text"
                            v-model="editForm.trade_specialization"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100"
                        />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Reset Password (Leave blank to keep current)
                        </label>
                        <PasswordInput
                            v-model="editForm.password"
                            placeholder="Type new password to override..."
                        />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="px-4 py-2 font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow-md transition"
                        >
                            Update Account &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Soft Delete Confirmation Modal -->
        <div v-if="showDeleteConfirmModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center space-x-3 text-amber-600">
                    <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/60">
                        <AlertTriangle class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Soft Delete User Account
                        </h3>
                        <p class="text-xs text-slate-500">
                            Move user to Trash Bin
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Are you sure you want to soft delete <strong>{{ targetUser?.name }}</strong>? The user's active session will be invalidated, but their historical courses, submissions, and grades will be preserved and can be restored at any time.
                </p>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button
                        type="button"
                        @click="showDeleteConfirmModal = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="executeSoftDelete"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow transition"
                    >
                        Confirm Soft Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Force Delete Confirmation Modal -->
        <div v-if="showForceDeleteModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/60">
                        <ShieldAlert class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Permanent Purge (Danger)
                        </h3>
                        <p class="text-xs text-slate-500">
                            Irreversible database deletion
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    Warning: Permanently purging <strong>{{ targetUser?.name }}</strong> cannot be undone. All linked records will be cascade removed or unlinked. An audit record of this deletion will be stored.
                </p>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button
                        type="button"
                        @click="showForceDeleteModal = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="executeForceDelete"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow transition"
                    >
                        Permanently Purge
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Teacher CSV Import Modal -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-brand-500/10 text-brand-600 dark:text-brand-400">
                        <FileSpreadsheet class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            Bulk Teacher CSV Import (200 Teachers)
                        </h3>
                        <p class="text-xs text-slate-500">
                            Upload a CSV spreadsheet to onboard IDEAS-TVET teachers automatically.
                        </p>
                    </div>
                </div>

                <!-- CSV Template Spec -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs mb-4">
                    <div class="font-bold text-slate-700 dark:text-slate-300 mb-1">Expected CSV Column Order:</div>
                    <code class="block bg-white dark:bg-slate-900 p-2 rounded text-[11px] font-mono text-brand-700 dark:text-brand-400 border border-slate-200 dark:border-slate-700">
                        First Name, Last Name, Email, Phone, Institution, State, Trade Specialization
                    </code>
                    <p class="text-[10px] text-slate-500 mt-1.5">
                        Duplicate emails are automatically skipped to avoid collision. Passwords are securely hashed.
                    </p>
                </div>

                <form @submit.prevent="submitImport" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Choose CSV File <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="file"
                            accept=".csv,text/csv"
                            @input="importForm.csv_file = $event.target.files[0]"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100"
                            required
                        />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showImportModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="importForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-bold text-xs shadow transition"
                        >
                            Execute Import &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
