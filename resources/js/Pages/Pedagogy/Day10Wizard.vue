<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Wand2,
    CheckCircle2,
    AlertCircle,
    Layers,
    ArrowRight,
    Edit3,
    Send,
    ShieldCheck,
    ShieldAlert,
    Lock,
    Users,
    Sparkles
} from 'lucide-vue-next';

const props = defineProps({
    active_lesson: Object,
    adaptation: Object,
    progress: Object,
    my_lessons: Array,
});

const checks = [
    {
        title: '3-Tier Differentiated Activities',
        description: 'Custom activities designed for Beginner (scaffolded), Intermediate (standard), and Advanced (extension) learners.',
        status: props.progress.day10_differentiated_ready,
    },
    {
        title: 'Universal Design for Learning (UDL) Application',
        description: 'Instructional strategies applied across all 3 principles: Engagement, Representation, and Action/Expression.',
        status: props.progress.day10_udl_ready,
    },
    {
        title: 'Essential Occupational Safety Verified Uncompromised',
        description: 'Mandatory PPE, LOTO, and hazard barriers remain 100% active; accommodations do not bypass safety rules.',
        status: props.progress.day10_safety_verified,
    },
    {
        title: 'Workshop Group & Equipment Rotation Schedule',
        description: 'Station capacities, equipment mapping, and rotation intervals defined to ensure equal hands-on time.',
        status: props.adaptation && props.adaptation.workshop_rotation_schedule && props.adaptation.workshop_rotation_schedule.stations && props.adaptation.workshop_rotation_schedule.stations.length > 0,
    },
    {
        title: 'Accommodations & Gender-Responsive Strategies',
        description: 'Provisions for diverse learning needs and empowerment of female trainees in non-traditional trades.',
        status: props.adaptation && props.adaptation.learner_accommodations && props.adaptation.learner_accommodations.length > 0,
    },
];

const completedChecks = checks.filter(c => c.status).length;
const percent = Math.round((completedChecks / checks.length) * 100);

const submitForm = useForm({});
const submitForAssessment = () => {
    if (!props.active_lesson) return;
    if (confirm('Submit this adapted technical lesson for Facilitator review and rubric grading?')) {
        submitForm.post(route('pedagogy.inclusive-designer.submit', props.active_lesson.id));
    }
};
</script>

<template>
    <Head title="Day 10 Inclusive Lesson Designer Wizard — Greg ICT Hub LMS" />

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
                        <span class="text-teal-600 dark:text-teal-400 font-bold">Day 10 Practice Wizard</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Wand2 class="w-7 h-7 text-teal-500" />
                        <span>Day 10: Inclusive Technical Lesson Adaptation Wizard</span>
                    </h1>
                </div>

                <div v-if="active_lesson" class="flex items-center space-x-2">
                    <Link
                        :href="route('pedagogy.inclusive-designer.edit', active_lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Edit3 class="w-3.5 h-3.5" />
                        <span>Edit Inclusive Design</span>
                    </Link>
                    <button
                        v-if="percent === 100"
                        type="button"
                        @click="submitForAssessment"
                        :disabled="submitForm.processing"
                        class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Send class="w-3.5 h-3.5" />
                        <span>Submit for Assessment</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Progress Card -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-teal-950 to-slate-950 text-white border border-teal-500/30 shadow-xl relative overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-teal-300">Day 10 TVET Pedagogy Milestone</span>
                        <h2 class="text-xl font-bold text-white mt-1">Inclusive Technical Lesson Adaptation &amp; UDL Verification</h2>
                        <p class="text-xs text-slate-300 mt-1 max-w-lg">
                            Verifies that you have produced an adapted technical lesson for learners with different skill levels and learning needs without compromising safety.
                        </p>
                    </div>

                    <div class="text-center sm:text-right">
                        <div class="text-3xl font-black text-teal-400">{{ percent }}%</div>
                        <div class="text-xs text-slate-300">{{ completedChecks }} of {{ checks.length }} Requirements Met</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="w-full h-2 bg-slate-800 rounded-full mt-5 overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-teal-400 to-emerald-400 transition-all duration-500"
                        :style="{ width: `${percent}%` }"
                    ></div>
                </div>
            </div>

            <!-- Active Lesson Details -->
            <div v-if="active_lesson" class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Target Lesson</span>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ active_lesson.title }}</h3>
                    <div class="text-xs text-slate-500">{{ active_lesson.occupational_task }}</div>
                </div>
                <div class="text-right">
                    <span :class="[
                        'text-xs font-bold px-2.5 py-1 rounded-full capitalize',
                        adaptation && adaptation.status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-teal-100 text-teal-800'
                    ]">
                        {{ adaptation ? adaptation.status : 'Not Started' }}
                    </span>
                </div>
            </div>

            <!-- 5 Checks -->
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
                            :href="route('pedagogy.inclusive-designer.edit', active_lesson.id)"
                            class="text-xs font-semibold px-2.5 py-1 rounded-full bg-teal-100 text-teal-800 dark:bg-teal-950/60 dark:text-teal-300 hover:opacity-80"
                        >
                            Configure in Designer →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Submission Action Callout -->
            <div
                v-if="percent === 100"
                class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
            >
                <div>
                    <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-100">Day 10 Inclusive Adaptation Ready!</h3>
                    <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-1">
                        All inclusion and safety requirements verified. Submit your lesson to the Facilitator for rubric evaluation.
                    </p>
                </div>
                <button
                    type="button"
                    @click="submitForAssessment"
                    :disabled="submitForm.processing"
                    class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm transition shrink-0"
                >
                    <Send class="w-4 h-4" />
                    <span>Submit for Facilitator Assessment</span>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
