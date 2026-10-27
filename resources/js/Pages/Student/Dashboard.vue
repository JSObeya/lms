<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BookOpen,
    Clock,
    Award,
    CheckCircle2,
    Calendar,
    ChevronRight,
    ArrowUpRight
} from 'lucide-vue-next';

defineProps({
    enrolled_courses: Array,
    upcoming_assignments: Array,
    recent_grades: Array,
});
</script>

<template>
    <Head title="Student Portal — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    Learner Portal &amp; Practical Training
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Track your technical courses, assignment deadlines, practical rubrics, and released competency grades.
                </p>
            </div>
        </template>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: My Enrolled Courses -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <BookOpen class="w-5 h-5 text-teal-600" />
                        <span>Enrolled Technical Courses</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">
                            {{ enrolled_courses.length }}
                        </span>
                    </h3>
                </div>

                <div v-if="enrolled_courses.length === 0" class="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <BookOpen class="w-12 h-12 mx-auto text-slate-400 mb-3" />
                    <h4 class="text-base font-bold text-slate-800 dark:text-slate-200">No active course enrollments</h4>
                    <p class="text-sm text-slate-500 mt-1">
                        Explore the institutional course catalogue to enrol in technical trades.
                    </p>
                    <Link
                        :href="route('courses.index')"
                        class="mt-4 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold text-sm hover:bg-teal-700 transition"
                    >
                        <span>Browse Courses</span>
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div
                        v-for="course in enrolled_courses"
                        :key="course.id"
                        class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between text-xs text-teal-600 dark:text-teal-400 font-semibold mb-2">
                                <span>{{ course.category?.name }}</span>
                                <span class="text-slate-400">{{ course.code }}</span>
                            </div>

                            <h4 class="font-bold text-base text-slate-900 dark:text-white leading-snug line-clamp-2">
                                {{ course.title }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                                {{ course.summary }}
                            </p>

                            <!-- Progress -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                                <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                                    <span>Course Progress</span>
                                    <span class="font-bold text-teal-600">{{ course.pivot?.progress_percentage || 0 }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div
                                        class="bg-teal-500 h-full rounded-full"
                                        :style="{ width: `${course.pivot?.progress_percentage || 0}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Instructor: {{ course.user?.name }}</span>
                            <Link
                                :href="route('courses.show', course.id)"
                                class="inline-flex items-center space-x-1 text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline"
                            >
                                <span>Enter Course</span>
                                <ArrowUpRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Upcoming Deadlines & Released Grades -->
            <div class="space-y-6">
                <!-- Upcoming Assignments -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2 mb-4">
                        <Clock class="w-4 h-4 text-teal-600" />
                        <span>Upcoming Assignments</span>
                    </h3>

                    <div v-if="upcoming_assignments.length === 0" class="text-center py-6 text-xs text-slate-400">
                        No upcoming assignment deadlines.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="assignment in upcoming_assignments"
                            :key="assignment.id"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs"
                        >
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ assignment.title }}</div>
                            <div class="text-slate-400 text-[11px] mt-0.5">{{ assignment.course?.code }}</div>
                            <div class="mt-2 flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-700">
                                <span class="text-[10px] text-amber-600 font-semibold flex items-center space-x-1">
                                    <Calendar class="w-3 h-3" />
                                    <span>Due: {{ assignment.due_date ? new Date(assignment.due_date).toLocaleDateString() : 'No deadline' }}</span>
                                </span>
                                <Link
                                    :href="route('assignments.show', assignment.id)"
                                    class="text-teal-600 font-bold hover:underline"
                                >
                                    Submit &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Grades -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2 mb-4">
                        <Award class="w-4 h-4 text-teal-600" />
                        <span>Released Assessment Grades</span>
                    </h3>

                    <div v-if="recent_grades.length === 0" class="text-center py-6 text-xs text-slate-400">
                        No released grades yet.
                    </div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="grade in recent_grades"
                            :key="grade.id"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs flex items-center justify-between"
                        >
                            <div>
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ grade.assignment?.title }}</div>
                                <div class="text-[11px] text-slate-400">{{ grade.course?.title }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-black text-teal-600 dark:text-teal-400">
                                    {{ grade.obtained_score }} / {{ grade.max_score }}
                                </div>
                                <span class="text-[10px] font-bold text-emerald-600">{{ grade.percentage }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
