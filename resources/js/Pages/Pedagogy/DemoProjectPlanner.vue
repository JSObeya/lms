<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Wrench,
    Sparkles,
    Zap,
    Flame,
    Compass,
    ArrowRight,
    Edit3,
    CheckCircle2,
    ShieldAlert,
    BookOpen,
    Layers,
    FileText
} from 'lucide-vue-next';

const props = defineProps({
    lesson: Object,
    my_lessons: Array,
    can_update: Boolean,
});

const switchLesson = (event) => {
    const id = event.target.value;
    if (id) {
        router.get(route('pedagogy.demo-project-planner', id));
    }
};
</script>

<template>
    <Head title="Demonstration and Project Planner — Greg ICT Hub LMS" />

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
                        <span class="text-blue-600 dark:text-blue-400 font-bold">Demonstration &amp; Project Planner</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Wrench class="w-7 h-7 text-blue-500" />
                        <span>Demonstration, Fault Diagnosis &amp; PBL Studio</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2 text-xs">
                        <label class="text-slate-500 font-bold">Active Lesson:</label>
                        <select
                            :value="lesson ? lesson.id : ''"
                            @change="switchLesson"
                            class="text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200"
                        >
                            <option value="">-- Choose Lesson --</option>
                            <option v-for="l in my_lessons" :key="l.id" :value="l.id">
                                {{ l.title }}
                            </option>
                        </select>
                    </div>

                    <Link
                        v-if="lesson"
                        :href="route('pedagogy.lessons.edit', lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Edit3 class="w-3.5 h-3.5" />
                        <span>Edit Full Lesson</span>
                    </Link>
                </div>
            </div>
        </template>

        <div v-if="!lesson" class="py-16 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <Compass class="w-12 h-12 mx-auto text-slate-400 mb-3" />
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No Lesson Selected</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Please author a competency lesson or select an existing one to plan demonstrations and project briefs.
            </p>
            <Link
                :href="route('pedagogy.lessons.builder')"
                class="mt-4 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold text-xs hover:bg-teal-700 transition"
            >
                <span>Launch Lesson Builder</span>
            </Link>
        </div>

        <div v-else class="space-y-8">
            <!-- Header Lesson Info -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                        {{ lesson.trade_category ? lesson.trade_category.name : 'TVET Technical Trade' }}
                    </span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">{{ lesson.title }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-3xl">
                        <strong>Task:</strong> {{ lesson.occupational_task }}
                    </p>
                </div>

                <div v-if="lesson.occupational_standard" class="text-right text-xs bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-200 dark:border-slate-700">
                    <div class="font-bold text-teal-600 dark:text-teal-400">{{ lesson.occupational_standard.code }}</div>
                    <div class="text-[11px] text-slate-500">{{ lesson.occupational_standard.title }}</div>
                </div>
            </div>

            <!-- 1. 4-Step Demonstration Breakdown -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <Zap class="w-5 h-5 text-amber-500" />
                            <span>Instructor 4-Step Demonstration Sequence</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Visual breakdown of the instructor modeling steps with safety tolerances and media aids.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="step in lesson.demonstration_plan"
                        :key="step.step"
                        class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 font-bold text-xs flex items-center justify-center">
                                    {{ step.step }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300">{{ step.name }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                                {{ step.instructor_action }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-[11px]">
                            <div v-if="step.safety_ppe" class="text-rose-600 dark:text-rose-400 font-semibold flex items-start space-x-1">
                                <ShieldAlert class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                <span>{{ step.safety_ppe }}</span>
                            </div>
                            <div v-if="step.key_tolerance" class="text-teal-600 dark:text-teal-400">
                                <strong>Tolerance:</strong> {{ step.key_tolerance }}
                            </div>
                            <div v-if="step.media_resource" class="text-slate-500 italic">
                                <strong>Resource:</strong> {{ step.media_resource }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Problem-Based Learning: Fault Diagnosis Scenarios -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <Flame class="w-5 h-5 text-rose-500" />
                        <span>Problem-Based Learning &amp; Fault Diagnosis Scenarios</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Authentic diagnostic challenges where learners identify symptoms, isolate root causes, and rectify defects safely.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div
                        v-for="(fault, idx) in lesson.fault_diagnosis_scenarios"
                        :key="idx"
                        class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                                Fault Scenario #{{ idx + 1 }}
                            </span>
                        </div>

                        <div class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ fault.fault_symptom }}
                        </div>

                        <div class="text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/60 p-3 rounded-xl border border-slate-200 dark:border-slate-700 space-y-1.5">
                            <div><strong>Root Cause:</strong> {{ fault.root_cause }}</div>
                            <div><strong>Diagnostic Method:</strong> {{ fault.diagnostic_procedure }}</div>
                            <div><strong>Rectification:</strong> {{ fault.rectification }}</div>
                        </div>

                        <div v-if="fault.safety_rule" class="p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-[11px] text-rose-800 dark:text-rose-300 font-semibold flex items-center space-x-2">
                            <ShieldAlert class="w-4 h-4 shrink-0 text-rose-600" />
                            <span>Safety Rule: {{ fault.safety_rule }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Project-Based Learning (PBL) Client Briefs -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <Sparkles class="w-5 h-5 text-purple-500" />
                        <span>Project-Based Learning (PBL) Industry Briefs</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Authentic commercial deliverables addressing local enterprise requirements.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div
                        v-for="(pbl, idx) in lesson.project_based_tasks"
                        :key="idx"
                        class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3"
                    >
                        <div class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            PBL Challenge #{{ idx + 1 }}
                        </div>
                        <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ pbl.project_name }}</h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ pbl.client_brief }}</p>

                        <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-xs text-purple-900 dark:text-purple-200 space-y-1">
                            <div><strong>Deliverable:</strong> {{ pbl.deliverable }}</div>
                            <div v-if="pbl.materials"><strong>Materials:</strong> {{ pbl.materials }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
