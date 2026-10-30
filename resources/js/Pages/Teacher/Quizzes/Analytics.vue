<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BarChart3,
    Search,
    Filter,
    Printer,
    FileSpreadsheet,
    CheckCircle2,
    XCircle,
    Award,
    Building2,
    MapPin,
    Wrench,
    Clock,
    AlertTriangle,
    Eye,
    ChevronRight,
    Users,
    TrendingUp,
    FileText,
    Layers,
    RotateCcw,
    HelpCircle
} from 'lucide-vue-next';

const props = defineProps({
    attempts: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '', state: 'all', institution: 'all', status: 'all' }),
    },
    stats: {
        type: Object,
        required: true,
    },
    mostChallengingQuestions: {
        type: Array,
        default: () => [],
    },
    availableStates: {
        type: Array,
        default: () => [],
    },
    availableInstitutions: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedState = ref(props.filters.state || 'all');
const selectedInstitution = ref(props.filters.institution || 'all');
const selectedStatus = ref(props.filters.status || 'all');

// Checkbox selection set
const selectedAttemptIds = ref(new Set());

const isAllSelected = computed(() => {
    if (!props.attempts.data || props.attempts.data.length === 0) return false;
    return props.attempts.data.every(att => selectedAttemptIds.value.has(att.id));
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        props.attempts.data.forEach(att => selectedAttemptIds.value.delete(att.id));
    } else {
        props.attempts.data.forEach(att => selectedAttemptIds.value.add(att.id));
    }
};

const toggleSelect = (id) => {
    if (selectedAttemptIds.value.has(id)) {
        selectedAttemptIds.value.delete(id);
    } else {
        selectedAttemptIds.value.add(id);
    }
};

const applyFilters = () => {
    router.get(
        route('admin.quizzes.analytics'),
        {
            search: searchQuery.value || undefined,
            state: selectedState.value !== 'all' ? selectedState.value : undefined,
            institution: selectedInstitution.value !== 'all' ? selectedInstitution.value : undefined,
            status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedState.value = 'all';
    selectedInstitution.value = 'all';
    selectedStatus.value = 'all';
    router.get(route('admin.quizzes.analytics'), {}, { preserveState: true, replace: true });
};

// Collective Print (Master Roster)
const printCollective = () => {
    const ids = Array.from(selectedAttemptIds.value);
    const params = new URLSearchParams();
    if (ids.length > 0) {
        ids.forEach(id => params.append('selected_ids[]', id));
    } else {
        if (selectedState.value !== 'all') params.append('state', selectedState.value);
        if (selectedInstitution.value !== 'all') params.append('institution', selectedInstitution.value);
        if (selectedStatus.value !== 'all') params.append('status', selectedStatus.value);
    }
    window.open(`${route('admin.quizzes.collective-print')}?${params.toString()}`, '_blank');
};

// Batch Print Individual Slips for Selected
const printBatchIndividual = () => {
    const ids = Array.from(selectedAttemptIds.value);
    if (ids.length === 0) {
        alert('Please select at least one teacher using the checkboxes to batch print individual evaluation slips.');
        return;
    }
    const params = new URLSearchParams();
    ids.forEach(id => params.append('selected_ids[]', id));
    window.open(`${route('admin.quizzes.batch-print')}?${params.toString()}`, '_blank');
};

// Export CSV
const exportCsv = () => {
    const ids = Array.from(selectedAttemptIds.value);
    const params = new URLSearchParams();
    if (ids.length > 0) {
        params.append('selected_ids', ids.join(','));
    }
    window.location.href = `${route('admin.quizzes.export-csv')}?${params.toString()}`;
};
</script>

<template>
    <Head title="Teacher Assessment Results & Evaluation Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-purple-500/10 text-purple-600 dark:text-purple-400 rounded-2xl border border-purple-500/20">
                        <BarChart3 class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            Teacher MCQ Results &amp; Evaluation Center
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Analyze teacher competence, select records, and print collective or individual evaluation sheets
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('admin.quizzes.questions.index')"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <HelpCircle class="w-4 h-4" />
                        <span>Set Questions &amp; Bulk CSV</span>
                    </Link>

                    <button
                        @click="printCollective"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4 text-amber-400" />
                        <span>Print Collective Sheet {{ selectedAttemptIds.size > 0 ? `(${selectedAttemptIds.size})` : '(All)' }}</span>
                    </button>

                    <button
                        @click="printBatchIndividual"
                        :disabled="selectedAttemptIds.size === 0"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <FileText class="w-4 h-4" />
                        <span>Batch Print Slips ({{ selectedAttemptIds.size }})</span>
                    </button>

                    <button
                        @click="exportCsv"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <FileSpreadsheet class="w-4 h-4" />
                        <span>Export CSV</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
            <!-- ========================================================= -->
            <!-- Analytical Metric KPI Cards -->
            <!-- ========================================================= -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Total Submissions</span>
                        <Users class="w-4 h-4 text-purple-500" />
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                        {{ stats.total_submissions }}
                    </div>
                    <span class="text-[11px] text-slate-400">
                        {{ stats.unique_teachers }} distinct educators
                    </span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Class Average</span>
                        <TrendingUp class="w-4 h-4 text-teal-500" />
                    </div>
                    <div class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-2">
                        {{ stats.average_percentage }}%
                    </div>
                    <span class="text-[11px] text-slate-400">
                        Mean competency score
                    </span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Pass Rate</span>
                        <CheckCircle2 class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">
                        {{ stats.pass_rate }}%
                    </div>
                    <span class="text-[11px] text-slate-400">
                        {{ stats.passed_count }} passed / {{ stats.failed_count }} need support
                    </span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Highest Score</span>
                        <Award class="w-4 h-4 text-amber-500" />
                    </div>
                    <div class="text-2xl font-black text-amber-500 mt-2">
                        {{ stats.highest_score }}%
                    </div>
                    <span class="text-[11px] text-slate-400">
                        Top cohort performance
                    </span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                        <span>Lowest Score</span>
                        <AlertTriangle class="w-4 h-4 text-rose-500" />
                    </div>
                    <div class="text-2xl font-black text-rose-500 mt-2">
                        {{ stats.lowest_score }}%
                    </div>
                    <span class="text-[11px] text-slate-400">
                        Remediation threshold
                    </span>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Misconception / Gap Analysis (Top 5 Most Missed Questions) -->
            <!-- ========================================================= -->
            <div v-if="mostChallengingQuestions.length > 0" class="p-5 rounded-2xl bg-amber-500/5 border border-amber-500/20 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <AlertTriangle class="w-4 h-4 text-amber-500" />
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            Diagnostic Gap Analysis: Top Challenging Concepts Across Teachers
                        </h3>
                    </div>
                    <span class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold">
                        Prioritize these in upcoming pedagogical workshops
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div
                        v-for="q in mostChallengingQuestions"
                        :key="q.question_number"
                        class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 space-y-1.5 text-xs"
                    >
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900 dark:text-white">Question #{{ q.question_number }}</span>
                            <span class="px-2 py-0.5 rounded font-black bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 text-[10px]">
                                {{ q.error_rate }}% Error Rate
                            </span>
                        </div>
                        <span class="text-[11px] font-semibold text-teal-600 dark:text-teal-400 block">{{ q.category }}</span>
                        <p class="text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
                            {{ q.question_text }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Search & Filters Toolbar -->
            <!-- ========================================================= -->
            <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400 pointer-events-none" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search teacher, college, email..."
                            class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-slate-900 dark:text-white"
                            @keyup.enter="applyFilters"
                        />
                    </div>

                    <!-- State Filter -->
                    <div>
                        <select
                            v-model="selectedState"
                            class="w-full py-2 px-3 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-slate-900 dark:text-white"
                            @change="applyFilters"
                        >
                            <option value="all">All States</option>
                            <option v-for="st in availableStates" :key="st" :value="st">{{ st }} State</option>
                        </select>
                    </div>

                    <!-- Institution Filter -->
                    <div>
                        <select
                            v-model="selectedInstitution"
                            class="w-full py-2 px-3 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-slate-900 dark:text-white"
                            @change="applyFilters"
                        >
                            <option value="all">All Institutions</option>
                            <option v-for="inst in availableInstitutions" :key="inst" :value="inst">{{ inst }}</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select
                            v-model="selectedStatus"
                            class="w-full py-2 px-3 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-slate-900 dark:text-white"
                            @change="applyFilters"
                        >
                            <option value="all">All Statuses</option>
                            <option value="passed">Competent (Passed &gt;= 60%)</option>
                            <option value="failed">Needs Support (&lt; 60%)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 text-xs">
                    <span class="text-slate-500 dark:text-slate-400">
                        Showing {{ attempts.data?.length || 0 }} of {{ attempts.total || 0 }} teacher submission records
                    </span>
                    <button
                        @click="resetFilters"
                        class="text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 flex items-center space-x-1 font-medium"
                    >
                        <RotateCcw class="w-3.5 h-3.5" />
                        <span>Reset Filters</span>
                    </button>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Selection Action Bar (Appears when >= 1 teacher selected) -->
            <!-- ========================================================= -->
            <div
                v-if="selectedAttemptIds.size > 0"
                class="p-4 bg-purple-600 text-white rounded-2xl shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4 animate-in fade-in"
            >
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/20 rounded-xl font-bold text-sm">
                        {{ selectedAttemptIds.size }} Selected
                    </div>
                    <div>
                        <span class="font-bold text-sm block">Selected Teachers Cohort</span>
                        <span class="text-xs text-purple-200">Ready for collective evaluation sheet or batch individual printing</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('admin.quizzes.questions.index')"
                        class="inline-flex items-center space-x-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <HelpCircle class="w-4 h-4" />
                        <span>Set Questions &amp; Bulk CSV</span>
                    </Link>

                    <button
                        @click="printCollective"
                        class="px-4 py-2 bg-white text-purple-900 font-bold rounded-xl text-xs shadow-xs hover:bg-purple-50 transition flex items-center space-x-1.5"
                    >
                        <Printer class="w-4 h-4 text-purple-700" />
                        <span>Print Collective Sheet ({{ selectedAttemptIds.size }})</span>
                    </button>

                    <button
                        @click="printBatchIndividual"
                        class="px-4 py-2 bg-purple-800 hover:bg-purple-900 text-white font-bold rounded-xl text-xs transition flex items-center space-x-1.5"
                    >
                        <FileText class="w-4 h-4" />
                        <span>Batch Print Individual Slips</span>
                    </button>

                    <button
                        @click="selectedAttemptIds.clear()"
                        class="px-3 py-2 text-xs font-semibold text-purple-200 hover:text-white underline"
                    >
                        Clear Selection
                    </button>
                </div>
            </div>

            <!-- ========================================================= -->
            <!-- Submissions Table -->
            <!-- ========================================================= -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 uppercase font-bold text-[11px] border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="p-4 w-10 text-center">
                                    <input
                                        type="checkbox"
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 h-4 w-4"
                                    />
                                </th>
                                <th class="p-4">Teacher / Candidate</th>
                                <th class="p-4">Institution &amp; State</th>
                                <th class="p-4">Specialization</th>
                                <th class="p-4 text-center">Score / 50</th>
                                <th class="p-4 text-center">Percentage</th>
                                <th class="p-4 text-center">Competency Status</th>
                                <th class="p-4">Completed</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="att in attempts.data"
                                :key="att.id"
                                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition"
                                :class="selectedAttemptIds.has(att.id) ? 'bg-purple-50/50 dark:bg-purple-950/20' : ''"
                            >
                                <td class="p-4 text-center">
                                    <input
                                        type="checkbox"
                                        :checked="selectedAttemptIds.has(att.id)"
                                        @change="toggleSelect(att.id)"
                                        class="rounded border-slate-300 text-purple-600 focus:ring-purple-500 h-4 w-4"
                                    />
                                </td>
                                <td class="p-4">
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">
                                        {{ att.user?.name || 'Technical Teacher' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ att.user?.email }} • {{ att.user?.phone || 'No phone' }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                                        {{ att.user?.institution || 'Technical College' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ att.user?.state }} State
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 font-semibold text-teal-700 dark:text-teal-400">
                                        {{ att.user?.trade_specialization || 'TVET General' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="text-sm font-black text-slate-900 dark:text-white">
                                        {{ att.score }}
                                    </span>
                                    <span class="text-slate-400 text-xs">/{{ att.max_score }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="font-black text-sm"
                                        :class="att.percentage >= 60 ? 'text-teal-600 dark:text-teal-400' : 'text-rose-500'"
                                    >
                                        {{ att.percentage }}%
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider"
                                        :class="att.passed
                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300'
                                            : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300'"
                                    >
                                        <CheckCircle2 v-if="att.passed" class="w-3 h-3" />
                                        <XCircle v-else class="w-3 h-3" />
                                        <span>{{ att.passed ? 'COMPETENT' : 'NEEDS SUPPORT' }}</span>
                                    </span>
                                </td>
                                <td class="p-4 text-slate-500 text-[11px]">
                                    <div>{{ new Date(att.completed_at).toLocaleDateString() }}</div>
                                    <div class="text-[10px] text-slate-400">{{ att.formatted_duration }}</div>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link
                                            :href="route('quizzes.attempt', att.id)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-purple-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                            title="View Detailed Results"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </Link>
                                        <Link
                                            :href="route('quizzes.attempt.print', att.id)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-teal-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                            title="Print Individual Slip"
                                        >
                                            <Printer class="w-4 h-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!attempts.data || attempts.data.length === 0">
                                <td colspan="9" class="p-8 text-center text-slate-500">
                                    No teacher assessment submissions found matching the criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div v-if="attempts.links && attempts.links.length > 3" class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-center space-x-1">
                    <Link
                        v-for="(link, i) in attempts.links"
                        :key="i"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1.5 rounded-xl text-xs font-semibold transition"
                        :class="[
                            link.active
                                ? 'bg-purple-600 text-white'
                                : link.url
                                    ? 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200'
                                    : 'text-slate-400 cursor-not-allowed opacity-50'
                        ]"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

