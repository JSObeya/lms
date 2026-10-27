<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Sparkles,
    Compass,
    Wand2,
    Wrench,
    Settings2,
    Briefcase,
    BookOpen,
    CheckCircle2,
    Clock,
    ArrowRight,
    Copy,
    Printer,
    Edit3,
    ShieldCheck,
    AlertCircle,
    Layers,
    ChevronRight,
    Flame,
    Zap,
    Hammer,
    Sprout,
    Utensils,
    Truck,
    Scissors
} from 'lucide-vue-next';

const props = defineProps({
    my_lessons: Array,
    templates: Array,
    courses: Array,
    day8_progress: Object,
    pedagogy_progress: Object,
    portfolio: Object,
});

const getTradeIcon = (slug) => {
    switch (slug) {
        case 'solar':
        case 'solar-pv-installation-and-maintenance':
            return Zap;
        case 'electrical':
        case 'electrical-works':
            return Zap;
        case 'welding':
        case 'welding-and-fabrication':
            return Flame;
        case 'agriculture':
            return Sprout;
        case 'catering':
        case 'catering-services':
            return Utensils;
        case 'carpentry':
        case 'carpentry-and-joinery':
            return Hammer;
        case 'automobile':
        case 'automobile-repairs':
            return Truck;
        case 'garment':
        case 'garment-making':
            return Scissors;
        default:
            return Wrench;
    }
};
</script>

<template>
    <Head title="TVET Pedagogy &amp; Instructional Design Studio — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-rose-500/10 text-rose-600 dark:text-rose-400 text-xs font-bold uppercase tracking-wider mb-2 border border-rose-500/20">
                        <Sparkles class="w-3.5 h-3.5 text-rose-500" />
                        <span>Days 9 &amp; 10 Instructional Design Suite</span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        TVET Pedagogy &amp; Instructional Design Studio
                    </h1>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-3xl">
                        Design industry-standard, competency-based practical technical lessons, 4-step demonstrations, universal design for learning (UDL) adaptations, and workshop equipment rotation schedules.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-sm transition"
                    >
                        <Compass class="w-4 h-4" />
                        <span>New Competency Lesson</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.day9-wizard')"
                        class="inline-flex items-center space-x-2 px-3.5 py-2.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-semibold text-sm border border-amber-500/30 transition"
                    >
                        <CheckCircle2 class="w-4 h-4 text-amber-500" />
                        <span>Day 9 Check</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.day10-wizard')"
                        class="inline-flex items-center space-x-2 px-3.5 py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-semibold text-sm border border-rose-500/30 transition"
                    >
                        <Wand2 class="w-4 h-4 text-rose-500" />
                        <span>Day 10 Check</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Integrated 5-Day Training Progression Bar (Days 8 to 12) -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-900 via-rose-950 to-slate-950 text-white border border-rose-500/20 shadow-xl relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
                    <Layers class="w-64 h-64 text-rose-400" />
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <span class="text-xs uppercase font-bold tracking-widest text-rose-300">IDEAS-TVET Integrated Progression</span>
                            <h2 class="text-lg lg:text-xl font-bold text-white mt-0.5">Continuous Professional Development Pathway</h2>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-400/30">
                            Single Lesson Evolution
                        </span>
                    </div>

                    <!-- 5 Steps Flow -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-4">
                        <!-- Day 8 -->
                        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-amber-400">Day 8</span>
                                <span v-if="day8_progress.is_completed" class="text-emerald-400 flex items-center space-x-1">
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    <span>Verified</span>
                                </span>
                                <span v-else class="text-amber-400">{{ day8_progress.percentage }}%</span>
                            </div>
                            <div class="text-sm font-bold text-white">Course &amp; Resources</div>
                            <p class="text-[11px] text-slate-300 mt-1">LMS digital syllabus, learning objectives &amp; sandbox.</p>
                            <Link :href="route('day8.wizard')" class="mt-3 inline-flex items-center text-xs text-amber-400 hover:text-amber-300 font-semibold">
                                <span>Launch Wizard</span>
                                <ChevronRight class="w-3 h-3 ml-0.5" />
                            </Link>
                        </div>

                        <!-- Day 9 -->
                        <div :class="[
                            'p-4 rounded-xl border backdrop-blur-sm',
                            pedagogy_progress.day9_completed
                                ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-100'
                                : 'bg-rose-900/30 border-rose-500/40 text-rose-100'
                        ]">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-rose-300">Day 9 (Core)</span>
                                <span v-if="pedagogy_progress.day9_completed" class="text-emerald-400 flex items-center space-x-1 font-bold">
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    <span>Ready</span>
                                </span>
                                <span v-else class="text-rose-300 font-semibold">In Progress</span>
                            </div>
                            <div class="text-sm font-bold text-white">Competency Lesson</div>
                            <p class="text-[11px] text-slate-300 mt-1">Occupational standards, 4-step demo, learner exercises &amp; criteria.</p>
                            <Link :href="route('pedagogy.lessons.builder')" class="mt-3 inline-flex items-center text-xs text-rose-300 hover:text-rose-200 font-semibold">
                                <span>Lesson Builder</span>
                                <ChevronRight class="w-3 h-3 ml-0.5" />
                            </Link>
                        </div>

                        <!-- Day 10 -->
                        <div :class="[
                            'p-4 rounded-xl border backdrop-blur-sm',
                            pedagogy_progress.day10_completed
                                ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-100'
                                : 'bg-teal-900/30 border-teal-500/40 text-teal-100'
                        ]">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-teal-300">Day 10 (Inclusive)</span>
                                <span v-if="pedagogy_progress.day10_completed" class="text-emerald-400 flex items-center space-x-1 font-bold">
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    <span>Adapted</span>
                                </span>
                                <span v-else class="text-teal-300 font-semibold">Ready to Adapt</span>
                            </div>
                            <div class="text-sm font-bold text-white">Inclusive Adaptation</div>
                            <p class="text-[11px] text-slate-300 mt-1">3-Tier activities, UDL principles, workshop safety &amp; rotation.</p>
                            <Link :href="route('pedagogy.inclusive-designer.index')" class="mt-3 inline-flex items-center text-xs text-teal-300 hover:text-teal-200 font-semibold">
                                <span>Inclusive Designer</span>
                                <ChevronRight class="w-3 h-3 ml-0.5" />
                            </Link>
                        </div>

                        <!-- Day 11 -->
                        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-purple-300">Day 11</span>
                                <span class="text-slate-400 text-[10px]">Connected</span>
                            </div>
                            <div class="text-sm font-bold text-white">Rubric &amp; Feedback</div>
                            <p class="text-[11px] text-slate-300 mt-1">Performance criteria rubrics, formative feedback &amp; grading.</p>
                            <Link :href="route('rubrics.index')" class="mt-3 inline-flex items-center text-xs text-purple-300 hover:text-purple-200 font-semibold">
                                <span>Manage Rubrics</span>
                                <ChevronRight class="w-3 h-3 ml-0.5" />
                            </Link>
                        </div>

                        <!-- Day 12 -->
                        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 backdrop-blur-sm">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="font-bold text-blue-300">Day 12</span>
                                <span class="text-slate-400 text-[10px]">{{ portfolio.completion_percentage }}%</span>
                            </div>
                            <div class="text-sm font-bold text-white">Digital Portfolio</div>
                            <p class="text-[11px] text-slate-300 mt-1">Microteaching evidence, lesson artifacts &amp; school plan.</p>
                            <Link :href="route('portfolio.index')" class="mt-3 inline-flex items-center text-xs text-blue-300 hover:text-blue-200 font-semibold">
                                <span>View Portfolio</span>
                                <ChevronRight class="w-3 h-3 ml-0.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Studio Navigation Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- 1. Competency Lesson Builder -->
                <Link
                    :href="route('pedagogy.lessons.builder')"
                    class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-amber-500/50 transition group flex flex-col justify-between"
                >
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                            <Compass class="w-6 h-6" />
                        </div>
                        <div class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400 mb-1">Module A · Day 9</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Competency-Based Lesson Builder</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Draft TVET practical lessons with approved NBTE occupational standards, 4-step demonstrations, guided practice, PBL tasks, and fault-diagnosis scenarios.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-amber-600 dark:text-amber-400">
                        <span>Launch Builder</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition" />
                    </div>
                </Link>

                <!-- 2. Inclusive Lesson Designer -->
                <Link
                    :href="route('pedagogy.inclusive-designer.index')"
                    class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-teal-500/50 transition group flex flex-col justify-between"
                >
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                            <Wand2 class="w-6 h-6" />
                        </div>
                        <div class="text-xs font-bold uppercase tracking-wider text-teal-600 dark:text-teal-400 mb-1">Module B · Day 10</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Inclusive Lesson Designer</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Extend practical lessons with 3-tier differentiated tasks (Beginner, Intermediate, Advanced), UDL accessibility, accommodations, and non-negotiable safety preservation.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-teal-600 dark:text-teal-400">
                        <span>Design Adaptations</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition" />
                    </div>
                </Link>

                <!-- 3. Demonstration & Project Planner -->
                <Link
                    :href="route('pedagogy.demo-project-planner')"
                    class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-blue-500/50 transition group flex flex-col justify-between"
                >
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                            <Wrench class="w-6 h-6" />
                        </div>
                        <div class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 mb-1">TVET Practicality</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Demonstration &amp; Project Planner</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Fine-tune 4-step modeling steps with safety tolerances, problem-based fault injection scenarios, and real-world commercial client projects.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-blue-600 dark:text-blue-400">
                        <span>Open Planner</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition" />
                    </div>
                </Link>

                <!-- 4. Workshop Management Planner -->
                <Link
                    :href="route('pedagogy.workshop-planner')"
                    class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-emerald-500/50 transition group flex flex-col justify-between"
                >
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 group-hover:scale-105 transition">
                            <Settings2 class="w-6 h-6" />
                        </div>
                        <div class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-1">Lab Coordination</div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Workshop Management Planner</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Coordinate equipment rotation schedules, trainee station capacities, PPE safety checkoffs, and master emergency power isolation protocols.
                        </p>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        <span>Configure Stations</span>
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition" />
                    </div>
                </Link>
            </div>

            <!-- Templates Section: 8 IDEAS-TVET Trades -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <span>8 Approved IDEAS–TVET Trade Templates</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-semibold">
                                Ready to Use
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Aligned with National Occupational Standards (NOS Nigeria) featuring comprehensive demonstrations, fault scenarios, and UDL adaptations.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div
                        v-for="tpl in templates"
                        :key="tpl.id"
                        class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-800 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                                    <component :is="getTradeIcon(tpl.template_trade)" class="w-5 h-5" />
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800">
                                    {{ tpl.trade_category ? tpl.trade_category.name : 'TVET Trade' }}
                                </span>
                            </div>

                            <h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2">
                                {{ tpl.title }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-3">
                                {{ tpl.occupational_task }}
                            </p>

                            <div v-if="tpl.occupational_standard" class="mt-3 text-[11px] text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/60 p-2 rounded-lg">
                                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ tpl.occupational_standard.code }}</span>
                                <div class="truncate text-[10px] text-slate-500">{{ tpl.occupational_standard.source_authority }}</div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                            <Link
                                :href="route('pedagogy.lessons.export', tpl.id)"
                                class="inline-flex items-center space-x-1 text-xs text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                                title="Preview &amp; Print"
                            >
                                <Printer class="w-3.5 h-3.5" />
                                <span>Preview</span>
                            </Link>

                            <Link
                                :href="route('pedagogy.lessons.builder', { template_id: tpl.id })"
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-xs transition"
                            >
                                <Copy class="w-3.5 h-3.5" />
                                <span>Use Template</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- My Progressive Lessons Table -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            My Competency-Based TVET Lessons
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Authoring, version history, inclusive adaptations, and facilitator review tracking.
                        </p>
                    </div>
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 dark:bg-slate-100 dark:hover:bg-white text-white dark:text-slate-900 text-xs font-semibold transition"
                    >
                        <Compass class="w-3.5 h-3.5" />
                        <span>Create New Lesson</span>
                    </Link>
                </div>

                <div v-if="my_lessons.length === 0" class="py-12 text-center">
                    <Compass class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-600 mb-3" />
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">No competency lessons authored yet</h4>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        Select one of the 8 approved trade templates above or click "Create New Lesson" to begin Day 9 instructional design.
                    </p>
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="mt-4 inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 text-white font-semibold text-xs hover:bg-teal-700 transition"
                    >
                        <span>Start Lesson Builder</span>
                    </Link>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/70 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-semibold border-y border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="p-3">Lesson Title &amp; Task</th>
                                <th class="p-3">Trade &amp; Standard</th>
                                <th class="p-3">Course Link</th>
                                <th class="p-3">Day 9 (Core)</th>
                                <th class="p-3">Day 10 (Inclusive)</th>
                                <th class="p-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="item in my_lessons" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ item.title }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 max-w-xs">{{ item.occupational_task }}</div>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            v{{ item.version }}
                                        </span>
                                        <span :class="[
                                            'text-[10px] font-semibold px-1.5 py-0.5 rounded capitalize',
                                            item.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'
                                        ]">
                                            {{ item.status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div class="font-medium text-slate-800 dark:text-slate-200">{{ item.trade_category ? item.trade_category.name : '—' }}</div>
                                    <div v-if="item.occupational_standard" class="text-[11px] text-teal-600 dark:text-teal-400">
                                        {{ item.occupational_standard.code }}
                                    </div>
                                    <div v-else class="text-[10px] text-slate-400">Instructor Standard</div>
                                </td>
                                <td class="p-3">
                                    <div v-if="item.course" class="font-medium text-slate-700 dark:text-slate-300">
                                        {{ item.course.title }}
                                    </div>
                                    <span v-else class="text-[11px] text-slate-400 italic">Not linked</span>
                                </td>
                                <td class="p-3">
                                    <span v-if="item.demonstration_plan && item.demonstration_plan.length" class="inline-flex items-center space-x-1 text-emerald-600 dark:text-emerald-400 font-semibold text-[11px]">
                                        <CheckCircle2 class="w-3.5 h-3.5" />
                                        <span>4-Step Ready</span>
                                    </span>
                                    <span v-else class="text-amber-500 text-[11px]">Incomplete</span>
                                </td>
                                <td class="p-3">
                                    <div v-if="item.inclusive_adaptation">
                                        <span :class="[
                                            'inline-flex items-center space-x-1 font-semibold text-[11px]',
                                            item.inclusive_adaptation.status === 'approved' ? 'text-emerald-500' : 'text-teal-500'
                                        ]">
                                            <Wand2 class="w-3.5 h-3.5" />
                                            <span class="capitalize">{{ item.inclusive_adaptation.status }}</span>
                                        </span>
                                    </div>
                                    <Link
                                        v-else
                                        :href="route('pedagogy.inclusive-designer.edit', item.id)"
                                        class="text-xs text-rose-500 hover:underline font-semibold"
                                    >
                                        + Adapt Lesson
                                    </Link>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="inline-flex items-center space-x-2">
                                        <Link
                                            :href="route('pedagogy.lessons.edit', item.id)"
                                            class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                                            title="Edit Lesson"
                                        >
                                            <Edit3 class="w-4 h-4" />
                                        </Link>
                                        <Link
                                            :href="route('pedagogy.inclusive-designer.edit', item.id)"
                                            class="p-1.5 rounded-lg text-teal-600 dark:text-teal-400 hover:bg-slate-100 dark:hover:bg-slate-800"
                                            title="Inclusive Adaptations"
                                        >
                                            <Wand2 class="w-4 h-4" />
                                        </Link>
                                        <Link
                                            :href="route('pedagogy.lessons.export', item.id)"
                                            class="p-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                                            title="Export / Print Lesson"
                                        >
                                            <Printer class="w-4 h-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
