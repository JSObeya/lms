<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ShieldCheck,
    Search,
    Filter,
    Calendar,
    User,
    Clock,
    Activity,
    Layers,
    RotateCcw,
    Trash2,
    Eye,
    X,
    AlertTriangle,
    CheckCircle2,
    Database,
    ArrowUpDown,
    Download
} from 'lucide-vue-next';

const props = defineProps({
    logs: Object,
    availableActions: Array,
    stats: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedAction = ref(props.filters.action || '');
const selectedEntity = ref(props.filters.entity_type || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const selectedLogForDetails = ref(null);
const showPurgeModal = ref(false);
const purgeDays = ref(90);

const purgeForm = useForm({
    days: 90,
});

const applyFilters = () => {
    router.get(
        route('admin.audit-logs.index'),
        {
            search: search.value || undefined,
            action: selectedAction.value || undefined,
            entity_type: selectedEntity.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const resetFilters = () => {
    search.value = '';
    selectedAction.value = '';
    selectedEntity.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

const filterByActionCategory = (category) => {
    switch (category) {
        case 'user':
            selectedAction.value = 'user_updated';
            break;
        case 'auth':
            selectedAction.value = 'user_logged_in';
            break;
        case 'course':
            selectedAction.value = 'course_updated';
            break;
        case 'rubric':
            selectedAction.value = 'rubric_created';
            break;
        default:
            selectedAction.value = '';
    }
    applyFilters();
};

const executePurge = () => {
    purgeForm.days = purgeDays.value;
    purgeForm.post(route('admin.audit-logs.clear-old'), {
        onSuccess: () => {
            showPurgeModal.value = false;
        },
    });
};

const getActionBadgeClass = (action) => {
    if (action.includes('deleted') || action.includes('purged')) {
        return 'bg-rose-100 text-rose-800 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200 dark:border-rose-800';
    }
    if (action.includes('created') || action.includes('restored') || action.includes('imported')) {
        return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800';
    }
    if (action.includes('login') || action.includes('logged_in')) {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border border-blue-200 dark:border-blue-800';
    }
    if (action.includes('updated') || action.includes('changed')) {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800';
    }
    return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700';
};

const formatEntity = (type, id) => {
    if (!type) return 'System / Global';
    const shortName = type.split('\\').pop();
    return id ? `${shortName} #${id}` : shortName;
};
</script>

<template>
    <Head title="System Audit Trail &amp; Logs — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2.5">
                        <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                            <ShieldCheck class="w-5 h-5" />
                        </span>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                System Audit Trail &amp; Compliance Logs
                            </h1>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                NDPA 2023 compliant immutable activity logging for security, user mutations, courses, rubrics, and soft-delete restorations.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('admin.users.index')"
                        class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-semibold text-xs transition"
                    >
                        Back to User Management
                    </Link>

                    <button
                        @click="showPurgeModal = true"
                        class="px-3.5 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 hover:bg-rose-100 font-semibold text-xs transition flex items-center space-x-1.5"
                    >
                        <Trash2 class="w-3.5 h-3.5" />
                        <span>Purge Historical Logs</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Metric Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span>Total Audit Entries</span>
                        <Database class="w-4 h-4 text-brand-600 dark:text-brand-400" />
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">
                        {{ stats.total_logs.toLocaleString() }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Logged actions across LMS</div>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span>Today's Activity</span>
                        <Clock class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ stats.today_events.toLocaleString() }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Recorded since midnight (WAT)</div>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span>Security &amp; User Ops</span>
                        <ShieldCheck class="w-4 h-4 text-blue-500" />
                    </div>
                    <div class="text-2xl font-black text-blue-600 dark:text-blue-400">
                        {{ stats.security_actions.toLocaleString() }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Logins, edits, status &amp; deletes</div>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span>Course &amp; Assessment Ops</span>
                        <Layers class="w-4 h-4 text-amber-500" />
                    </div>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400">
                        {{ stats.course_rubric_actions.toLocaleString() }}
                    </div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Curriculum, rubrics &amp; grades</div>
                </div>
            </div>

            <!-- Filter Card -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <!-- Search Input -->
                    <div class="lg:col-span-2 relative">
                        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            v-model="search"
                            @keyup.enter="applyFilters"
                            placeholder="Search user, action, IP, or entity..."
                            class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                        />
                    </div>

                    <!-- Action Selector -->
                    <div>
                        <select
                            v-model="selectedAction"
                            @change="applyFilters"
                            class="w-full py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                        >
                            <option value="">All Action Types</option>
                            <option v-for="act in availableActions" :key="act" :value="act">
                                {{ act }}
                            </option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <input
                            type="date"
                            v-model="dateFrom"
                            @change="applyFilters"
                            class="w-full py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                            placeholder="Date From"
                        />
                    </div>

                    <!-- Date To & Reset Button -->
                    <div class="flex items-center space-x-2">
                        <input
                            type="date"
                            v-model="dateTo"
                            @change="applyFilters"
                            class="w-full py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-brand-500"
                            placeholder="Date To"
                        />
                        <button
                            @click="resetFilters"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-100 dark:bg-slate-800 transition"
                            title="Reset Filters"
                        >
                            <RotateCcw class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center space-x-2">
                        <Activity class="w-4 h-4 text-brand-600 dark:text-brand-400" />
                        <span>Audit Event History</span>
                    </h3>
                    <span class="text-xs text-slate-400">Showing page {{ logs.current_page }} of {{ logs.last_page }} ({{ logs.total }} total events)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-[11px] text-slate-500 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Timestamp (WAT)</th>
                                <th class="px-4 py-3">User</th>
                                <th class="px-4 py-3">Action</th>
                                <th class="px-4 py-3">Target Entity</th>
                                <th class="px-4 py-3">Network &amp; Client</th>
                                <th class="px-4 py-3 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-[11px]">
                            <tr v-if="logs.data.length === 0">
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-sans">
                                    No audit log entries match the selected filters.
                                </td>
                            </tr>
                            <tr
                                v-for="log in logs.data"
                                :key="log.id"
                                class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition"
                            >
                                <td class="px-4 py-3 whitespace-nowrap text-slate-500 dark:text-slate-400 font-sans">
                                    {{ new Date(log.created_at).toLocaleString() }}
                                </td>
                                <td class="px-4 py-3 font-sans">
                                    <div v-if="log.user" class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold flex items-center justify-center text-[10px]">
                                            {{ log.user.name.charAt(0) }}
                                        </div>
                                        <div class="truncate max-w-[140px]">
                                            <div class="font-bold text-slate-900 dark:text-white truncate">{{ log.user.name }}</div>
                                            <div class="text-[10px] text-slate-400 truncate">{{ log.user.email }}</div>
                                        </div>
                                    </div>
                                    <span v-else class="text-slate-400 italic">System / Guest</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="[
                                            'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider inline-block',
                                            getActionBadgeClass(log.action)
                                        ]"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-300">
                                    {{ formatEntity(log.entity_type, log.entity_id) }}
                                </td>
                                <td class="px-4 py-3 text-slate-400 truncate max-w-[160px]" :title="log.user_agent">
                                    <span>{{ log.ip_address || '127.0.0.1' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        v-if="log.details"
                                        @click="selectedLogForDetails = log"
                                        class="px-2.5 py-1 rounded-lg bg-brand-50 dark:bg-brand-950/60 text-brand-700 dark:text-brand-300 hover:bg-brand-100 font-semibold text-[11px] font-sans transition flex items-center space-x-1 ml-auto"
                                    >
                                        <Eye class="w-3.5 h-3.5" />
                                        <span>View Payload</span>
                                    </button>
                                    <span v-else class="text-slate-400 font-sans text-[11px]">&mdash;</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div v-if="logs.links.length > 3" class="p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <span class="text-xs text-slate-500">
                        Page {{ logs.current_page }} of {{ logs.last_page }}
                    </span>
                    <div class="flex items-center space-x-1">
                        <Link
                            v-for="(link, i) in logs.links"
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

        <!-- Log Details Modal -->
        <div v-if="selectedLogForDetails" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2">
                        <ShieldCheck class="w-5 h-5 text-brand-600 dark:text-brand-400" />
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Audit Event Payload
                        </h3>
                    </div>
                    <button @click="selectedLogForDetails = null" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Action:</span>
                        <span class="font-bold font-mono text-brand-600 dark:text-brand-400">{{ selectedLogForDetails.action }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Actor:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ selectedLogForDetails.user?.name || 'System' }} ({{ selectedLogForDetails.user?.email || 'N/A' }})</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Entity:</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ selectedLogForDetails.entity_type }} #{{ selectedLogForDetails.entity_id || 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">IP Address:</span>
                        <span class="font-mono text-slate-700 dark:text-slate-300">{{ selectedLogForDetails.ip_address }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400">Timestamp:</span>
                        <span class="text-slate-700 dark:text-slate-300">{{ new Date(selectedLogForDetails.created_at).toLocaleString() }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Structured Payload (JSON)
                    </label>
                    <pre class="bg-slate-900 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto max-h-60 border border-slate-800">{{ JSON.stringify(selectedLogForDetails.details, null, 2) }}</pre>
                </div>

                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 text-right">
                    <button
                        @click="selectedLogForDetails = null"
                        class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-semibold text-xs"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Purge Historical Logs Modal -->
        <div v-if="showPurgeModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <div class="p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/60">
                        <AlertTriangle class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white">
                            Purge Historical Audit Trail
                        </h3>
                        <p class="text-xs text-slate-500">
                            Super-Administrator compliance maintenance
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    This maintenance operation permanently deletes older audit records to optimize database size. A compliance audit record will be logged with the purge event.
                </p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Purge logs older than:
                    </label>
                    <select
                        v-model="purgeDays"
                        class="w-full py-2 px-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-100"
                    >
                        <option :value="30">30 Days</option>
                        <option :value="60">60 Days</option>
                        <option :value="90">90 Days (Recommended)</option>
                        <option :value="180">180 Days (6 Months)</option>
                        <option :value="365">365 Days (1 Year)</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button
                        type="button"
                        @click="showPurgeModal = false"
                        class="px-4 py-2 text-xs font-semibold text-slate-500"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="executePurge"
                        :disabled="purgeForm.processing"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow transition"
                    >
                        Confirm &amp; Purge Logs
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
