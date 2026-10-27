<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Compass,
    Plus,
    Copy,
    Printer,
    Edit3,
    Wand2,
    Layers,
    CheckCircle2,
    Clock
} from 'lucide-vue-next';

const props = defineProps({
    lessons: Object,
    templates: Array,
});
</script>

<template>
    <Head title="Competency-Based Lesson Plans — Greg ICT Hub LMS" />

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
                        <span class="text-amber-600 dark:text-amber-400 font-bold">Competency Lessons</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Compass class="w-7 h-7 text-amber-500" />
                        <span>Competency-Based Practical Lessons</span>
                    </h1>
                </div>

                <Link
                    :href="route('pedagogy.lessons.builder')"
                    class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                >
                    <Plus class="w-4 h-4" />
                    <span>Create Competency Lesson</span>
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Lessons List -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div v-if="!lessons.data || lessons.data.length === 0" class="py-12 text-center text-slate-500">
                    <Compass class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" />
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">No competency lessons authored yet</h4>
                    <p class="text-xs text-slate-400 mt-1">Start by authoring your first practical lesson or use an approved template.</p>
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="mt-4 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold text-xs hover:bg-teal-700 transition"
                    >
                        <span>Launch Lesson Builder</span>
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="lesson in lessons.data"
                        :key="lesson.id"
                        class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:shadow-md transition flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    {{ lesson.trade_category ? lesson.trade_category.name : 'TVET Trade' }}
                                </span>
                                <span class="text-[10px] font-semibold text-slate-500">v{{ lesson.version }}</span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2">{{ lesson.title }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ lesson.occupational_task }}</p>

                            <div v-if="lesson.occupational_standard" class="mt-2 text-[11px] text-teal-600 dark:text-teal-400">
                                {{ lesson.occupational_standard.code }}
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <Link
                                :href="route('pedagogy.lessons.export', lesson.id)"
                                class="text-xs text-slate-600 hover:text-slate-900 dark:text-slate-400 flex items-center space-x-1"
                            >
                                <Printer class="w-3.5 h-3.5" />
                                <span>Export</span>
                            </Link>

                            <div class="flex items-center space-x-2">
                                <Link
                                    :href="route('pedagogy.inclusive-designer.edit', lesson.id)"
                                    class="text-xs text-rose-500 font-semibold hover:underline flex items-center space-x-1"
                                >
                                    <Wand2 class="w-3.5 h-3.5" />
                                    <span>Adapt</span>
                                </Link>
                                <Link
                                    :href="route('pedagogy.lessons.edit', lesson.id)"
                                    class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition"
                                >
                                    Edit
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
