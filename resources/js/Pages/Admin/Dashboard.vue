<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Users,
    BookOpen,
    ClipboardCheck,
    Award,
    TrendingUp,
    Shield,
    UploadCloud,
    Activity,
    Layers,
    ChevronRight,
    GraduationCap
} from 'lucide-vue-next';

defineProps({
    metrics: Object,
    trade_distribution: Array,
    recent_audits: Array,
});
</script>

<template>
    <Head title="System Administrator Dashboard — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <span>LMS Executive Oversight &amp; Audit Console</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 font-bold uppercase tracking-wider">
                            Admin
                        </span>
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Monitoring 200 IDEAS-TVET teachers, trade specializations, submissions, and audit trails.
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('admin.users.index')"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-sm transition"
                    >
                        <Users class="w-4 h-4" />
                        <span>Manage 200 Teachers</span>
                    </Link>
                </div>
            </div>
        </template>

        <!-- Metrics Overview Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Teachers Registered</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ metrics.total_teachers }} / 200</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1">{{ metrics.active_teachers }} Active accounts</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Total Courses</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ metrics.total_courses }}</div>
                <div class="text-[11px] text-blue-600 font-medium mt-1">{{ metrics.published_courses }} Published</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Practice Courses</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ metrics.practice_courses }}</div>
                <div class="text-[11px] text-amber-600 font-medium mt-1">Mode A Sandboxes</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Assignments</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ metrics.total_assignments }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Assessment tasks</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Submissions</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ metrics.total_submissions }}</div>
                <div class="text-[11px] text-emerald-600 font-medium mt-1">{{ metrics.graded_submissions }} Graded with rubric</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Day 8 Completion</div>
                <div class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-1">{{ metrics.day8_completion_rate }}%</div>
                <div class="text-[11px] text-teal-600 font-medium mt-1">Practical benchmark</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: TVET Trade Categories Distribution -->
            <div class="lg:col-span-2 space-y-6">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <Layers class="w-5 h-5 text-teal-600" />
                                <span>IDEAS–TVET Trade Specialization Distribution</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Active course production across the 8 priority trade categories</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="trade in trade_distribution"
                            :key="trade.id"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 flex items-center justify-between"
                        >
                            <div>
                                <h4 class="font-bold text-sm text-slate-800 dark:text-slate-200">{{ trade.name }}</h4>
                                <div class="text-xs text-slate-400 mt-0.5">{{ trade.courses_count }} courses registered</div>
                            </div>
                            <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-600 dark:text-teal-400">
                                {{ trade.courses_count }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 200 Teachers Management Action Card -->
                <div class="p-6 rounded-2xl bg-gradient-to-r from-teal-900 to-slate-900 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <h3 class="text-lg font-bold">200 IDEAS–TVET Teacher Onboarding</h3>
                        <p class="text-xs text-slate-300 mt-1 max-w-xl">
                            Upload your formatted teacher roster via CSV to generate accounts, assign institutional roles, initialize practice sandboxes, and detect duplicate emails automatically.
                        </p>
                    </div>
                    <Link
                        :href="route('admin.users.index')"
                        class="px-5 py-2.5 rounded-xl bg-teal-400 hover:bg-teal-300 text-slate-950 font-bold text-sm shadow transition whitespace-nowrap"
                    >
                        Bulk CSV Import &rarr;
                    </Link>
                </div>
            </div>

            <!-- Right 1 Col: Security Audit Log Feed -->
            <div class="space-y-6">
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2 mb-4">
                        <Shield class="w-4 h-4 text-teal-600" />
                        <span>Security &amp; Action Audit Log</span>
                    </h3>

                    <div v-if="recent_audits.length === 0" class="text-center py-6 text-xs text-slate-400">
                        No audit activities recorded.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="audit in recent_audits"
                            :key="audit.id"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs"
                        >
                            <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                                <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider text-[10px]">{{ audit.action }}</span>
                                <span>{{ new Date(audit.created_at).toLocaleTimeString() }}</span>
                            </div>
                            <div class="font-medium text-slate-800 dark:text-slate-200 truncate">
                                {{ audit.user?.name || 'System' }}
                            </div>
                            <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                IP: {{ audit.ip_address || '127.0.0.1' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
