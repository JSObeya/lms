<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BookOpen,
    Eye,
    Wand2,
    Table,
    ArrowLeft,
    CheckCircle2,
    Calendar,
    Award
} from 'lucide-vue-next';

defineProps({
    course: Object,
    is_enrolled: Boolean,
    can_edit: Boolean,
});
</script>

<template>
    <Head :title="course.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('courses.index')"
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100"
                    >
                        <ArrowLeft class="w-4 h-4 text-slate-500" />
                    </Link>
                    <div>
                        <div class="text-xs text-teal-600 font-bold uppercase tracking-wider">
                            {{ course.category?.name }} · {{ course.code }}
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                            {{ course.title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <Link
                        v-if="can_edit"
                        :href="route('courses.builder', course.id)"
                        class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center space-x-1.5 shadow"
                    >
                        <Wand2 class="w-4 h-4" />
                        <span>Visual Builder</span>
                    </Link>

                    <Link
                        :href="route('courses.preview', course.id)"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-semibold text-xs flex items-center space-x-1.5"
                    >
                        <Eye class="w-4 h-4" />
                        <span>Student View</span>
                    </Link>

                    <Link
                        v-if="can_edit"
                        :href="route('gradebook.show', course.id)"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-semibold text-xs flex items-center space-x-1.5"
                    >
                        <Table class="w-4 h-4" />
                        <span>Gradebook</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Overview Card -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">About This Course</h3>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    {{ course.description || course.summary }}
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                    <span>Instructor: <strong>{{ course.user?.name }}</strong></span>
                    <span>Duration: {{ course.duration || '4 Weeks' }}</span>
                    <span>Difficulty: <strong class="capitalize">{{ course.difficulty_level }}</strong></span>
                </div>
            </div>

            <!-- Sections List -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Curriculum &amp; Sections</h3>
                <div
                    v-for="(section, sIndex) in course.sections"
                    :key="section.id"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm"
                >
                    <div class="font-bold text-sm text-slate-900 dark:text-white">
                        Topic {{ sIndex + 1 }}: {{ section.title }}
                    </div>
                    <p v-if="section.description" class="text-xs text-slate-500 mt-1">{{ section.description }}</p>

                    <div class="mt-3 flex items-center space-x-4 text-xs text-slate-400">
                        <span>{{ section.lessons?.length || 0 }} Lessons</span>
                        <span>{{ section.resources?.length || 0 }} Resources</span>
                        <span>{{ section.assignments?.length || 0 }} Assignments</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
