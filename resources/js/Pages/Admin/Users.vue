<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Users,
    UploadCloud,
    Search,
    Shield,
    CheckCircle2,
    XCircle,
    UserCheck,
    UserX,
    FileSpreadsheet,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const showImportModal = ref(false);

const importForm = useForm({
    csv_file: null,
});

const submitImport = () => {
    importForm.post(route('admin.users.bulk-import'), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};

const toggleStatus = (userId) => {
    router.post(route('admin.users.toggle-status', userId), {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="200 Teachers Management — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        200 IDEAS–TVET Teacher User Administration
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Manage user accounts, bulk import instructor rosters via CSV, and oversee role permissions.
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <button
                        @click="showImportModal = true"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition"
                    >
                        <UploadCloud class="w-4 h-4" />
                        <span>Bulk Import 200 Teachers</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Users Table Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center space-x-2">
                        <Users class="w-5 h-5 text-teal-600" />
                        <span>System Registered Accounts</span>
                    </h3>
                    <span class="text-xs text-slate-400">Total: {{ users.total }} users</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-slate-500 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-5 py-3">User / Name</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Institution &amp; State</th>
                                <th class="px-5 py-3">Trade Specialization</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50/50">
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ user.name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ user.email }} · @{{ user.username || 'user' }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                        {{ user.roles[0]?.name || 'Student' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-medium">
                                    <div>{{ user.institution || 'GTC Makurdi' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ user.state || 'Benue' }} State</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="text-teal-600 dark:text-teal-400 font-semibold">
                                        {{ user.trade_specialization || 'General Technical' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                            user.account_status === 'active'
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                                : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'
                                        ]"
                                    >
                                        {{ user.account_status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <button
                                        @click="toggleStatus(user.id)"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition"
                                        :class="user.account_status === 'active'
                                            ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'
                                            : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'"
                                    >
                                        {{ user.account_status === 'active' ? 'Suspend' : 'Activate' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bulk Teacher CSV Import Modal -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-600">
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
                    <code class="block bg-white dark:bg-slate-900 p-2 rounded text-[11px] font-mono text-teal-600 border">
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
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"
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
                            class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition"
                        >
                            Execute Import &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
