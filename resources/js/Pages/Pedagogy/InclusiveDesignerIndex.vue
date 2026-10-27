<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Wand2,
    Compass,
    Layers,
    ArrowRight,
    Copy,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
    my_lessons: Array,
    templates: Array,
});
</script>

<template>
    <Head title="Inclusive Technical Lesson Designer — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                        <Link :href="route('pedagogy.studio')" class="hover:underline flex items-center space-x-1">
                            <Layers class="w-3.5 h-3.5 text-rose-500" />
                            <span>TVET Pedagogy Studio</span>
                        </Link>
                        <span>/</span>
                        <span class="text-teal-600 dark:text-teal-400 font-bold">Inclusive Lesson Designer</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Wand2 class="w-7 h-7 text-teal-500" />
                        <span>Select Lesson to Adapt (Day 10)</span>
                    </h1>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- My Lessons to Adapt -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    My Competency Lessons Available for Inclusive Adaptation
                </h2>

                <div v-if="!my_lessons || my_lessons.length === 0" class="py-8 text-center text-slate-500">
                    <p class="text-xs">No lessons found. Please author a Day 9 lesson first.</p>
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="mt-3 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 text-white text-xs font-semibold hover:bg-teal-700 transition"
                    >
                        <span>Create Day 9 Lesson</span>
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="lesson in my_lessons"
                        :key="lesson.id"
                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col justify-between"
                    >
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ lesson.title }}</h3>
                            <p class="text-xs text-slate-500 line-clamp-2 mt-1">{{ lesson.occupational_task }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Duration: {{ lesson.duration_minutes }}m</span>
                            <Link
                                :href="route('pedagogy.inclusive-designer.edit', lesson.id)"
                                class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition"
                            >
                                <span>Adapt Lesson</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
