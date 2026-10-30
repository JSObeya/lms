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
    GraduationCap,
    FileDown,
    Download,
    Eye,
    FileText,
    BookMarked,
    Plus
} from 'lucide-vue-next';

defineProps({
    metrics: Object,
    trade_distribution: Array,
    recent_audits: Array,
    recent_documents: {
        type: Array,
        default: () => [],
    },
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
                        :href="route('teacher-documents.index')"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-850 hover:bg-slate-800 text-slate-200 hover:text-white font-semibold text-sm shadow-sm transition border border-slate-700"
                    >
                        <FileDown class="w-4 h-4 text-teal-400" />
                        <span>Teacher Documents ({{ metrics.total_documents || 0 }})</span>
                    </Link>

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

            <!-- Right 1 Col: Documents & Security Audit Log Feed -->
            <div class="space-y-6">
                <!-- Official Documents & PDF Downloads for Teachers & Admin -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <FileDown class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                            <span>Official Documents &amp; PDFs</span>
                        </h3>
                        <Link
                            :href="route('teacher-documents.index')"
                            class="text-xs text-teal-600 dark:text-teal-400 hover:underline font-semibold"
                        >
                            Manage All &rarr;
                        </Link>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Download official manuals and curriculum documents or upload new files for teachers.
                    </p>

                    <!-- Quick Guide Downloads -->
                    <div class="space-y-2 mb-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <a
                            :href="route('user.guide.download-word')"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800/60 hover:bg-teal-100 dark:hover:bg-teal-900/40 transition group text-xs"
                            title="Download Full LMS User & Admin Guide (Word .docx)"
                        >
                            <div class="flex items-center space-x-2 min-w-0">
                                <BookMarked class="w-4 h-4 text-teal-600 dark:text-teal-400 shrink-0" />
                                <span class="font-bold text-teal-950 dark:text-teal-200 truncate">Complete LMS User Guide (.docx)</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-teal-600 text-white font-semibold text-[11px] shrink-0 ml-2 shadow-xs group-hover:bg-teal-700">
                                <Download class="w-3 h-3 mr-1" />
                                Download
                            </span>
                        </a>

                        <a
                            :href="route('day8.guide.download-word')"
                            class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition group text-xs"
                            title="Download Day 8 AI-Assisted Lesson Guide (Word .docx)"
                        >
                            <div class="flex items-center space-x-2 min-w-0">
                                <FileText class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
                                <span class="font-bold text-amber-950 dark:text-amber-200 truncate">Day 8 Practical Guide (.docx)</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-amber-600 text-white font-semibold text-[11px] shrink-0 ml-2 shadow-xs group-hover:bg-amber-700">
                                <Download class="w-3 h-3 mr-1" />
                                Download
                            </span>
                        </a>
                    </div>

                    <!-- Teacher Uploaded Documents List -->
                    <div v-if="!recent_documents || recent_documents.length === 0" class="text-center py-4 text-xs text-slate-400">
                        No documents uploaded yet.
                    </div>

                    <div v-else class="space-y-2.5">
                        <div
                            v-for="doc in recent_documents"
                            :key="doc.id"
                            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-2"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate" :title="doc.title">
                                    {{ doc.title }}
                                </div>
                                <div class="flex items-center space-x-2 text-[10px] text-slate-400 mt-0.5">
                                    <span class="px-1.5 py-0.2 rounded font-bold uppercase text-[9px] bg-rose-500/15 text-rose-600 dark:text-rose-400">
                                        {{ doc.file_extension }}
                                    </span>
                                    <span>{{ doc.formatted_file_size }}</span>
                                    <span>&bull;</span>
                                    <span>{{ doc.category }}</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1 shrink-0">
                                <a
                                    :href="route('teacher-documents.preview', doc.id)"
                                    target="_blank"
                                    class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 transition"
                                    title="Preview in browser"
                                >
                                    <Eye class="w-3.5 h-3.5" />
                                </a>
                                <a
                                    :href="route('teacher-documents.download', doc.id)"
                                    class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-bold shadow-xs transition"
                                    title="Download PDF directly to system"
                                >
                                    <Download class="w-3 h-3" />
                                    <span>Download</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <Link
                            :href="route('teacher-documents.index')"
                            class="w-full inline-flex items-center justify-center space-x-2 py-2 px-3 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 transition"
                        >
                            <Plus class="w-3.5 h-3.5 text-teal-500" />
                            <span>Upload / Browse All Documents</span>
                        </Link>
                    </div>
                </div>

                <!-- Security Audit Log Feed -->
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
