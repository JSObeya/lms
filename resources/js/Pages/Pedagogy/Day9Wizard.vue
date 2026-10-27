<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    CheckCircle2,
    AlertCircle,
    Compass,
    Wand2,
    Layers,
    ArrowRight,
    Edit3,
    Printer,
    FileText,
    Zap,
    Wrench,
    ShieldCheck
} from 'lucide-vue-next';

const props = defineProps({
    active_lesson: Object,
    progress: Object,
    my_lessons: Array,
});

const checks = [
    {
        title: 'Occupational Task & Standards Linkage',
        description: 'Lesson is mapped to an occupational trade and links to an official NBTE NOS standard or explicit instructor example.',
        status: props.active_lesson && props.active_lesson.occupational_task && props.active_lesson.occupational_task.length > 5,
        target_tab: 'standards',
    },
    {
        title: 'Measurable Learning Outcomes',
        description: 'Contains behavioral action verbs, conditions, and precision standards.',
        status: props.active_lesson && props.active_lesson.learning_outcomes && props.active_lesson.learning_outcomes.length > 0,
        target_tab: 'outcomes',
    },
    {
        title: 'Instructor Demonstration Plan (4-Steps)',
        description: 'Detailed modeling phases with safety PPE, key tolerances, and teaching media.',
        status: props.progress.day9_demonstration_ready,
        target_tab: 'demo',
    },
    {
        title: 'Learner Practice Exercises',
        description: 'Contains both guided learner practice with checkpoints and independent practice tasks.',
        status: props.progress.day9_exercise_ready,
        target_tab: 'practice',
    },
    {
        title: 'Measurable Assessment Criteria & Evidence',
        description: 'Objective criteria with evidence type, passing metrics, and critical safety non-negotiables.',
        status: props.progress.day9_criteria_ready,
        target_tab: 'assessment',
    },
];

const completedChecks = checks.filter(c => c.status).length;
const percent = Math.round((completedChecks / checks.length) * 100);
</script>

<template>
    <Head title="Day 9 Competency Lesson Assessment Wizard — Greg ICT Hub LMS" />

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
                        <span class="text-amber-600 dark:text-amber-400 font-bold">Day 9 Assessment Verification Wizard</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <CheckCircle2 class="w-7 h-7 text-amber-500" />
                        <span>Day 9: Competency-Based Lesson Audit &amp; Verification</span>
                    </h1>
                </div>

                <div v-if="active_lesson" class="flex items-center space-x-2">
                    <Link
                        :href="route('pedagogy.lessons.edit', active_lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Edit3 class="w-3.5 h-3.5" />
                        <span>Edit Active Lesson</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.inclusive-designer.edit', active_lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Wand2 class="w-3.5 h-3.5" />
                        <span>Proceed to Day 10</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Progress Card -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-amber-950 to-slate-950 text-white border border-amber-500/30 shadow-xl relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-300">Day 9 TVET Pedagogy Milestone</span>
                        <h2 class="text-xl font-bold text-white mt-1">Competency-Based Practical Lesson Verification</h2>
                        <p class="text-xs text-slate-300 mt-1 max-w-lg">
                            Verifies that you have authored a complete, industry-standard TVET lesson plan containing an instructor demonstration, learner exercises, and measurable assessment criteria.
                        </p>
                    </div>

                    <div class="text-center sm:text-right">
                        <div class="text-3xl font-black text-amber-400">{{ percent }}%</div>
                        <div class="text-xs text-slate-300">{{ completedChecks }} of {{ checks.length }} Requirements Met</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full h-2 bg-slate-800 rounded-full mt-5 overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-amber-400 to-emerald-400 transition-all duration-500"
                        :style="{ width: `${percent}%` }"
                    ></div>
                </div>
            </div>

            <!-- Active Lesson Details -->
            <div v-if="active_lesson" class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Audited Lesson</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ active_lesson.title }}</h3>
                    <div class="text-xs text-slate-500">{{ active_lesson.occupational_task }}</div>
                </div>
                <Link :href="route('pedagogy.lessons.export', active_lesson.id)" class="text-xs text-teal-600 font-semibold hover:underline flex items-center space-x-1">
                    <Printer class="w-3.5 h-3.5" />
                    <span>View Plan</span>
                </Link>
            </div>

            <!-- 5 Essential Criteria Checks -->
            <div class="space-y-3">
                <div
                    v-for="(check, idx) in checks"
                    :key="idx"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-start justify-between gap-4"
                >
                    <div class="flex items-start space-x-3">
                        <div :class="[
                            'w-7 h-7 rounded-full flex items-center justify-center shrink-0 mt-0.5',
                            check.status ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'
                        ]">
                            <CheckCircle2 v-if="check.status" class="w-5 h-5" />
                            <AlertCircle v-else class="w-5 h-5" />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ check.title }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">{{ check.description }}</p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <span v-if="check.status" class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                            Verified
                        </span>
                        <Link
                            v-else-if="active_lesson"
                            :href="route('pedagogy.lessons.edit', active_lesson.id)"
                            class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 hover:opacity-80"
                        >
                            Fix in Builder →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Conclusion Card -->
            <div
                v-if="percent === 100"
                class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 shadow-sm flex items-center justify-between"
            >
                <div>
                    <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-100">Day 9 Practical Competency Achieved!</h3>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">
                        Your lesson meets all IDEAS-TVET Day 9 technical requirements and has been logged to your Teaching Portfolio.
                    </p>
                </div>
                <Link
                    v-if="active_lesson"
                    :href="route('pedagogy.inclusive-designer.edit', active_lesson.id)"
                    class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                >
                    <span>Launch Day 10 Inclusive Designer</span>
                    <ArrowRight class="w-4 h-4 ml-1" />
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
