<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Table,
    Download,
    ArrowLeft,
    CheckCircle2,
    Clock,
    Lock,
    Unlock,
    Award,
    Users
} from 'lucide-vue-next';

const props = defineProps({
    course: Object,
    assignments: Array,
    students: Array,
    grades: Array,
    submissions: Array,
    can_grade: Boolean,
});

const toggleRelease = (gradeId) => {
    router.post(route('grades.toggle-release', gradeId), {}, {
        preserveScroll: true,
    });
};

const classAverage = computed(() => {
    if (props.grades.length === 0) return 0;
    const sum = props.grades.reduce((acc, g) => acc + parseFloat(g.percentage || 0), 0);
    return Math.round(sum / props.grades.length);
});

const highestScore = computed(() => {
    if (props.grades.length === 0) return 0;
    return Math.max(...props.grades.map(g => parseFloat(g.obtained_score || 0)));
});
</script>

<template>
    <Head :title="`Gradebook: ${course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('courses.show', course.id)"
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100"
                    >
                        <ArrowLeft class="w-4 h-4 text-slate-500" />
                    </Link>
                    <div>
                        <div class="text-xs text-teal-600 font-bold uppercase tracking-wider">
                            Course Assessment Registry · {{ course.code }}
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            Gradebook: {{ course.title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a
                        :href="route('gradebook.export-csv', course.id)"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition"
                    >
                        <Download class="w-4 h-4" />
                        <span>Export CSV Report</span>
                    </a>
                </div>
            </div>
        </template>

        <!-- Gradebook Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Enrolled Students</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ students.length }}</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Recorded Grades</div>
                <div class="text-2xl font-black text-teal-600 mt-1">{{ grades.length }}</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Class Average</div>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ classAverage }}%</div>
            </div>

            <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="text-xs text-slate-500 font-medium">Highest Obtained Score</div>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ highestScore }} pts</div>
            </div>
        </div>

        <!-- Gradebook Master Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center space-x-2">
                    <Table class="w-5 h-5 text-teal-600" />
                    <span>Master Assessment Scoresheet</span>
                </h3>
                <span class="text-xs text-slate-400">Total Entries: {{ grades.length }}</span>
            </div>

            <div v-if="grades.length === 0" class="p-12 text-center text-xs text-slate-400">
                No grades entered or evaluated yet for this course. Open an assignment to evaluate submissions using rubrics.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 font-bold uppercase text-slate-500 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3">Student Name</th>
                            <th class="px-5 py-3">Assignment</th>
                            <th class="px-5 py-3">Score / Max</th>
                            <th class="px-5 py-3">Percentage</th>
                            <th class="px-5 py-3">Release Status</th>
                            <th class="px-5 py-3">Assessor Feedback</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="grade in grades" :key="grade.id" class="hover:bg-slate-50/50">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 dark:text-white">{{ grade.student?.name }}</div>
                                <div class="text-[10px] text-slate-400">{{ grade.student?.email }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-medium">
                                {{ grade.assignment?.title }}
                            </td>
                            <td class="px-5 py-3.5 font-black text-sm text-slate-900 dark:text-white">
                                {{ grade.obtained_score }} / {{ grade.max_score }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-teal-600">
                                {{ grade.percentage }}%
                            </td>
                            <td class="px-5 py-3.5">
                                <span
                                    :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                        grade.is_released
                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                            : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                                    ]"
                                >
                                    {{ grade.is_released ? 'Released to Student' : 'Draft / Withheld' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 max-w-xs truncate">
                                {{ grade.feedback || '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <button
                                    v-if="can_grade"
                                    @click="toggleRelease(grade.id)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition inline-flex items-center space-x-1"
                                    :class="grade.is_released
                                        ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'
                                        : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'"
                                >
                                    <Lock v-if="grade.is_released" class="w-3 h-3" />
                                    <Unlock v-else class="w-3 h-3" />
                                    <span>{{ grade.is_released ? 'Withhold' : 'Release' }}</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
