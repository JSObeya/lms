<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Eye,
    ArrowLeft,
    BookOpen,
    Paperclip,
    Download,
    ExternalLink,
    ClipboardList,
    Award,
    CheckCircle2
} from 'lucide-vue-next';

defineProps({
    course: Object,
    is_preview_mode: Boolean,
});
</script>

<template>
    <Head :title="`Preview as Student: ${course.title}`" />

    <AuthenticatedLayout>
        <!-- Student Preview Floating Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-500 text-slate-950 shadow-md flex items-center justify-between gap-4 font-sans">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-xl bg-slate-950 text-amber-400">
                    <Eye class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-sm font-black">Student Preview Mode Active</h2>
                    <p class="text-xs text-slate-900 mt-0.5">
                        You are viewing this published course through the perspective of an enrolled TVET learner. All instructor editing controls are hidden.
                    </p>
                </div>
            </div>

            <Link
                :href="route('courses.builder', course.id)"
                class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs shrink-0 flex items-center space-x-1 transition"
            >
                <ArrowLeft class="w-3.5 h-3.5" />
                <span>Return to Builder</span>
            </Link>
        </div>

        <!-- Student Course View -->
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Header Card -->
            <div class="p-8 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center space-x-2 text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider mb-2">
                    <span>{{ course.category?.name }}</span>
                    <span>·</span>
                    <span>{{ course.code }}</span>
                </div>

                <h1 class="text-3xl font-black text-slate-900 dark:text-white leading-tight">
                    {{ course.title }}
                </h1>

                <p class="text-slate-600 dark:text-slate-300 text-sm mt-3 leading-relaxed">
                    {{ course.description || course.summary }}
                </p>

                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                    <span>Instructor: <strong>{{ course.user?.name }}</strong> ({{ course.user?.institution || 'TVET Master Trainer' }})</span>
                    <span>Duration: <strong>{{ course.duration || 'Self-paced' }}</strong></span>
                </div>
            </div>

            <!-- Published Sections Stream -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Course Syllabus &amp; Learning Units</h2>

                <div
                    v-for="(section, idx) in course.sections"
                    :key="section.id"
                    class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5"
                >
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                        <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase">Unit {{ idx + 1 }}</span>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">{{ section.title }}</h3>
                        <p v-if="section.description" class="text-xs text-slate-500 mt-1">{{ section.description }}</p>
                    </div>

                    <!-- Learning Objectives -->
                    <div v-if="section.objectives?.length > 0">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Target Competency Standard</h4>
                        <ul class="space-y-1.5 text-xs text-slate-700 dark:text-slate-300">
                            <li v-for="obj in section.objectives" :key="obj.id" class="flex items-start space-x-2">
                                <CheckCircle2 class="w-4 h-4 text-teal-600 mt-0.5 shrink-0" />
                                <span>{{ obj.complete_text }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Resources -->
                    <div v-if="section.resources?.length > 0">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Class Materials &amp; Downloads</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="res in section.resources"
                                :key="res.id"
                                class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs flex items-center justify-between"
                            >
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ res.title }}</span>
                                <a
                                    v-if="res.file_path"
                                    :href="route('resources.download', res.id)"
                                    class="p-1.5 rounded-lg bg-white dark:bg-slate-700 text-teal-600 font-semibold"
                                >
                                    <Download class="w-4 h-4" />
                                </a>
                                <a
                                    v-else-if="res.external_url"
                                    :href="res.external_url"
                                    target="_blank"
                                    class="p-1.5 rounded-lg bg-white dark:bg-slate-700 text-blue-600"
                                >
                                    <ExternalLink class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Assignments -->
                    <div v-if="section.assignments?.length > 0">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Assignments &amp; Practical Tests</h4>
                        <div class="space-y-3">
                            <div
                                v-for="assign in section.assignments"
                                :key="assign.id"
                                class="p-4 rounded-xl bg-teal-50/50 dark:bg-teal-950/20 border border-teal-200 dark:border-teal-900/40 text-xs flex items-center justify-between"
                            >
                                <div>
                                    <h5 class="font-bold text-sm text-slate-900 dark:text-white">{{ assign.title }}</h5>
                                    <p class="text-slate-500 mt-0.5">Due: {{ assign.due_date ? new Date(assign.due_date).toLocaleDateString() : 'Self-paced' }} · Max {{ assign.max_score }} marks</p>
                                </div>
                                <Link
                                    :href="route('assignments.show', assign.id)"
                                    class="px-3.5 py-1.5 rounded-lg bg-teal-600 text-white font-bold text-xs"
                                >
                                    View Task &amp; Rubric
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
