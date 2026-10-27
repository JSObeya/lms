<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Wand2,
    Save,
    CheckCircle2,
    AlertCircle,
    ShieldAlert,
    ShieldCheck,
    Plus,
    Trash2,
    Users,
    Clock,
    Layers,
    ArrowLeft,
    Send,
    Compass,
    Sparkles,
    Settings2,
    Eye,
    Zap,
    HeartHandshake,
    Lock
} from 'lucide-vue-next';

const props = defineProps({
    lesson: Object,
    adaptation: Object,
    can_update: {
        type: Boolean,
        default: true,
    },
});

const activeTab = ref('timing'); // 'timing', 'differentiated', 'udl', 'accommodations', 'rotation_safety'

// Form state
const form = useForm({
    structured_plan: Array.isArray(props.adaptation.structured_plan) && props.adaptation.structured_plan.length
        ? JSON.parse(JSON.stringify(props.adaptation.structured_plan))
        : [
            { phase: 'Safety Briefing & Motivation', duration_min: 15, teacher_activity: '', learner_activity: '' },
            { phase: 'Instructor 4-Step Demonstration', duration_min: 25, teacher_activity: '', learner_activity: '' },
            { phase: 'Guided Hands-On Practice', duration_min: 40, teacher_activity: '', learner_activity: '' },
            { phase: 'Differentiated Practical Lab', duration_min: 75, teacher_activity: '', learner_activity: '' },
            { phase: 'Tool Return, Clean-up & Debrief', duration_min: 25, teacher_activity: '', learner_activity: '' },
        ],
    differentiated_activities: props.adaptation.differentiated_activities && typeof props.adaptation.differentiated_activities === 'object'
        ? JSON.parse(JSON.stringify(props.adaptation.differentiated_activities))
        : {
            beginner: [{ task: '', support: '' }],
            intermediate: [{ task: '' }],
            advanced: [{ task: '', challenge: '' }],
        },
    udl_engagement: Array.isArray(props.adaptation.udl_engagement) && props.adaptation.udl_engagement.length
        ? JSON.parse(JSON.stringify(props.adaptation.udl_engagement))
        : ['Highlight direct community and economic relevance to local livelihood.', 'Provide choice between alternative project aesthetics or mounting configurations.'],
    udl_representation: Array.isArray(props.adaptation.udl_representation) && props.adaptation.udl_representation.length
        ? JSON.parse(JSON.stringify(props.adaptation.udl_representation))
        : ['High-contrast pictorial job cards and tactile sample boards.', 'Video micro-demos with bilingual terminology (English and local vernacular).'],
    udl_action_expression: Array.isArray(props.adaptation.udl_action_expression) && props.adaptation.udl_action_expression.length
        ? JSON.parse(JSON.stringify(props.adaptation.udl_action_expression))
        : ['Physical hands-on demonstration of the technical task.', 'Option for annotated photographic logbook or oral step walk-through.'],
    learner_accommodations: Array.isArray(props.adaptation.learner_accommodations) && props.adaptation.learner_accommodations.length
        ? JSON.parse(JSON.stringify(props.adaptation.learner_accommodations))
        : [
            { need: 'Trainee with mild motor tremor / reduced grip', adaptation: 'Extended-handle ergonomic ratcheting tool and bench clamping jig', safety_rule_maintained: 'Full PPE, electrical isolation, and torque specification remain 100% non-negotiable' },
            { need: 'Trainee with hearing impairment', adaptation: 'Visual LED strobe alarm and tactile vibration indicators', safety_rule_maintained: 'Evacuation drills and clear line of sight to instructor maintained' },
        ],
    safety_standards_maintained: props.adaptation.safety_standards_maintained ?? true,
    gender_responsive_strategies: Array.isArray(props.adaptation.gender_responsive_strategies) && props.adaptation.gender_responsive_strategies.length
        ? JSON.parse(JSON.stringify(props.adaptation.gender_responsive_strategies))
        : [
            'Assign female trainees as Lead Commissioning Engineers and Quality Inspectors during group rotations.',
            'Provide ergonomic hand tools tailored to diverse physical body sizes.',
            'Maintain zero-tolerance enforcement of anti-harassment workshop conduct.',
        ],
    workshop_rotation_schedule: props.adaptation.workshop_rotation_schedule && typeof props.adaptation.workshop_rotation_schedule === 'object'
        ? JSON.parse(JSON.stringify(props.adaptation.workshop_rotation_schedule))
        : {
            intervals_minutes: 30,
            stations: [
                { station_id: 1, name: 'Preparation & Stripping Station', equipment: 'Cutters, strippers, ferrule kits', group_capacity: 4 },
                { station_id: 2, name: 'Assembly & Mounting Station', equipment: 'DIN rails, enclosures, screwdrivers', group_capacity: 4 },
                { station_id: 3, name: 'Termination & Calibrated Torque Station', equipment: 'Insulated tools, torque driver, fuse holders', group_capacity: 4 },
                { station_id: 4, name: 'Testing & QA Inspection Station', equipment: 'Multimeters, insulation testers, checklist boards', group_capacity: 4 },
            ],
        },
    workshop_safety_arrangements: Array.isArray(props.adaptation.workshop_safety_arrangements) && props.adaptation.workshop_safety_arrangements.length
        ? JSON.parse(JSON.stringify(props.adaptation.workshop_safety_arrangements))
        : [
            'Master emergency electric/power shut-off button clearly labeled and unobstructed.',
            'Mandatory PPE verification check before entering practical workshop zone.',
            'First aid station and certified burn/eye-wash kits fully stocked.',
        ],
    status: props.adaptation.status || 'draft',
});

// Helper for total planned minutes
const totalPlannedMinutes = computed(() => {
    return form.structured_plan.reduce((sum, phase) => sum + (Number(phase.duration_min) || 0), 0);
});

// Differentiated Tier Adders
const addBeginner = () => form.differentiated_activities.beginner.push({ task: '', support: '' });
const removeBeginner = (idx) => form.differentiated_activities.beginner.splice(idx, 1);

const addIntermediate = () => form.differentiated_activities.intermediate.push({ task: '' });
const removeIntermediate = (idx) => form.differentiated_activities.intermediate.splice(idx, 1);

const addAdvanced = () => form.differentiated_activities.advanced.push({ task: '', challenge: '' });
const removeAdvanced = (idx) => form.differentiated_activities.advanced.splice(idx, 1);

// UDL String List Helpers
const addUdlItem = (listName) => form[listName].push('');
const removeUdlItem = (listName, idx) => form[listName].splice(idx, 1);

// Accommodation Adder
const addAccommodation = () => {
    form.learner_accommodations.push({ need: '', adaptation: '', safety_rule_maintained: 'Essential occupational safety standards remain fully enforced.' });
};
const removeAccommodation = (idx) => form.learner_accommodations.splice(idx, 1);

// Station Adder
const addStation = () => {
    const nextId = (form.workshop_rotation_schedule.stations.length || 0) + 1;
    form.workshop_rotation_schedule.stations.push({
        station_id: nextId,
        name: `Station ${nextId}`,
        equipment: '',
        group_capacity: 4,
    });
};
const removeStation = (idx) => {
    form.workshop_rotation_schedule.stations.splice(idx, 1);
    form.workshop_rotation_schedule.stations.forEach((s, i) => { s.station_id = i + 1; });
};

// Day 10 Readiness
const day10Status = computed(() => {
    const has3Tier = form.differentiated_activities.beginner.length > 0 &&
                     form.differentiated_activities.intermediate.length > 0 &&
                     form.differentiated_activities.advanced.length > 0 &&
                     form.differentiated_activities.beginner.some(b => b.task && b.task.length > 5) &&
                     form.differentiated_activities.intermediate.some(i => i.task && i.task.length > 5) &&
                     form.differentiated_activities.advanced.some(a => a.task && a.task.length > 5);

    const hasUdl = form.udl_engagement.length > 0 &&
                   form.udl_representation.length > 0 &&
                   form.udl_action_expression.length > 0;

    const safetyVerified = form.safety_standards_maintained === true;

    const count = [has3Tier, hasUdl, safetyVerified].filter(Boolean).length;
    return {
        has3Tier,
        hasUdl,
        safetyVerified,
        percent: Math.round((count / 3) * 100),
        isComplete: count === 3,
    };
});

// Save handler
const save = (status = 'draft') => {
    form.status = status;
    form.put(route('pedagogy.inclusive-designer.update', props.lesson.id), {
        preserveScroll: true,
    });
};

// Submit for facilitator review
const submitForm = useForm({});
const submitForAssessment = () => {
    if (!day10Status.value.isComplete) {
        alert('Please complete all 3-tier differentiated activities, UDL principles, and verify occupational safety before submission.');
        return;
    }
    if (confirm('Submit this adapted inclusive lesson for Facilitator Assessment?')) {
        submitForm.post(route('pedagogy.inclusive-designer.submit', props.lesson.id));
    }
};
</script>

<template>
    <Head :title="`Inclusive Lesson Designer: ${lesson.title} — Greg ICT Hub LMS`" />

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
                        <Link :href="route('pedagogy.lessons.edit', lesson.id)" class="hover:underline">
                            <span>Day 9: {{ lesson.title }}</span>
                        </Link>
                        <span>/</span>
                        <span class="text-teal-600 dark:text-teal-400 font-bold">Module B · Day 10 Inclusive Designer</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Wand2 class="w-7 h-7 text-teal-500" />
                        <span>Inclusive Technical Lesson Designer</span>
                    </h1>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        @click="save('draft')"
                        :disabled="form.processing || !can_update"
                        class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition"
                    >
                        <Save class="w-3.5 h-3.5" />
                        <span>Save Draft</span>
                    </button>

                    <button
                        type="button"
                        @click="submitForAssessment"
                        :disabled="submitForm.processing || !can_update"
                        class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Send class="w-3.5 h-3.5" />
                        <span>Submit for Assessment</span>
                    </button>

                    <Link
                        :href="route('pedagogy.lessons.edit', lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition"
                    >
                        <ArrowLeft class="w-3.5 h-3.5" />
                        <span>Return to Day 9 Lesson</span>
                    </Link>
                </div>
            </div>
        </template>

        <!-- Base Competency Lesson Summary Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-teal-950 via-slate-900 to-rose-950 text-white border border-teal-500/30 shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-teal-300">Base Competency Lesson (Day 9 Foundation)</span>
                <h2 class="text-base font-bold text-white mt-0.5">{{ lesson.title }}</h2>
                <p class="text-xs text-slate-300 mt-1 max-w-2xl line-clamp-1">
                    <strong>Task:</strong> {{ lesson.occupational_task }}
                </p>
            </div>
            <div class="flex items-center space-x-4 text-xs shrink-0">
                <div class="text-right">
                    <div class="text-[11px] text-slate-400">Target Duration</div>
                    <div class="font-bold text-amber-300">{{ totalPlannedMinutes }} / {{ lesson.duration_minutes }} Mins</div>
                </div>
                <div class="text-right">
                    <div class="text-[11px] text-slate-400">Status</div>
                    <span :class="[
                        'text-[10px] font-bold px-2 py-0.5 rounded-full capitalize',
                        adaptation.status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-teal-500/20 text-teal-300'
                    ]">
                        {{ adaptation.status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Non-Negotiable Safety Callout Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-300 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm flex items-start space-x-3">
            <Lock class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
            <div class="text-xs space-y-1">
                <div class="font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wide">
                    TVET Inclusion Rule: Essential Safety Standards are Non-Negotiable
                </div>
                <p class="leading-relaxed">
                    Never automatically lower essential occupational safety standards to accommodate learner differences. Adaptations must provide assistive tools, jigs, communication formats, or extended practice time—while 100% preserving mandatory PPE, electrical isolation, machine guards, and chemical safeguards.
                </p>
                <div class="pt-1 flex items-center space-x-2">
                    <input
                        v-model="form.safety_standards_maintained"
                        type="checkbox"
                        id="safety-lock"
                        class="rounded border-amber-400 text-teal-600 focus:ring-teal-500"
                        required
                    />
                    <label for="safety-lock" class="font-bold text-amber-950 dark:text-amber-100 cursor-pointer">
                        I certify that essential occupational safety standards are fully preserved and uncompromised.
                    </label>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200 dark:border-slate-800 mb-6 flex overflow-x-auto space-x-2 pb-1">
            <button
                type="button"
                @click="activeTab = 'timing'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'timing'
                        ? 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Clock class="w-4 h-4" />
                <span>1. Timing &amp; Lesson Phases</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'differentiated'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'differentiated'
                        ? 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Users class="w-4 h-4" />
                <span>2. 3-Tier Differentiated Activities</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'udl'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'udl'
                        ? 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Sparkles class="w-4 h-4" />
                <span>3. Universal Design for Learning (UDL)</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'accommodations'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'accommodations'
                        ? 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <HeartHandshake class="w-4 h-4" />
                <span>4. Accommodations &amp; Gender Strategies</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'rotation_safety'"
                :class="[
                    'px-4 py-2.5 text-xs font-bold rounded-xl transition flex items-center space-x-2 whitespace-nowrap',
                    activeTab === 'rotation_safety'
                        ? 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border border-teal-500/30'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                ]"
            >
                <Settings2 class="w-4 h-4" />
                <span>5. Workshop Rotation Matrix &amp; Safety</span>
            </button>
        </div>

        <form @submit.prevent="save('draft')" class="space-y-6">
            <!-- TAB 1: Structured Lesson Timing & Sequential Flow -->
            <div v-show="activeTab === 'timing'" class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Structured Practical Lesson Timing</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Allocate realistic workshop timings and synchronize teacher facilitation with learner hands-on execution.
                        </p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        Total: {{ totalPlannedMinutes }} Minutes
                    </span>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="(phase, idx) in form.structured_plan"
                        :key="idx"
                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400">Phase {{ idx + 1 }}</span>
                            <div class="flex items-center space-x-2">
                                <label class="text-[11px] font-bold text-slate-500">Duration (Min):</label>
                                <input
                                    v-model.number="phase.duration_min"
                                    type="number"
                                    min="5"
                                    max="180"
                                    class="w-20 text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-1"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                Phase Name
                            </label>
                            <input
                                v-model="phase.phase"
                                type="text"
                                class="w-full text-xs font-semibold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Teacher Facilitator Action
                                </label>
                                <textarea
                                    v-model="phase.teacher_activity"
                                    rows="2"
                                    placeholder="Explain, demonstrate, coach, verify safety, monitor rotation..."
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                ></textarea>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Learner Hands-on Action
                                </label>
                                <textarea
                                    v-model="phase.learner_activity"
                                    rows="2"
                                    placeholder="Observe, question, mirror, execute in pairs, rotate stations..."
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: 3-Tier Differentiated Activities -->
            <div v-show="activeTab === 'differentiated'" class="space-y-6">
                <!-- Beginner Tier -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span>Tier 1: Beginner / Foundation Level</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Scaffolded tasks, step-by-step pictorial guides, buddy pairing, pre-set jigs (without lowering safety).
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addBeginner"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 text-xs font-semibold hover:bg-emerald-100 transition border border-emerald-200 dark:border-emerald-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Beginner Task</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, idx) in form.differentiated_activities.beginner"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-600">Beginner Activity #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.differentiated_activities.beginner.length > 1"
                                    type="button"
                                    @click="removeBeginner(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Simplified / Scaffolded Task *
                                </label>
                                <input
                                    v-model="item.task"
                                    type="text"
                                    placeholder="e.g. Fabricate single-string circuit with pre-measured wires and pictorial guide"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Scaffold &amp; Assistive Support Provided
                                </label>
                                <input
                                    v-model="item.support"
                                    type="text"
                                    placeholder="e.g. Paired with intermediate peer buddy; bench clamp jig for hand stabilization"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Intermediate Tier -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                <span>Tier 2: Intermediate / Standard Occupational Competency</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Standard blueprints, autonomous execution, full compliance with industry tolerances and speed.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addIntermediate"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 text-xs font-semibold hover:bg-blue-100 transition border border-blue-200 dark:border-blue-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Intermediate Task</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, idx) in form.differentiated_activities.intermediate"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-blue-600">Intermediate Activity #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.differentiated_activities.intermediate.length > 1"
                                    type="button"
                                    @click="removeIntermediate(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Standard Competency Task *
                                </label>
                                <input
                                    v-model="item.task"
                                    type="text"
                                    placeholder="e.g. Wire full 4-string combiner box adhering to schematic blueprint within 50 mins"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    required
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Tier -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                                <span>Tier 3: Advanced / Extension &amp; Peer Leadership</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Fault injection diagnosis, advanced calibration, quality control inspection, peer mentoring.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addAdvanced"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 text-xs font-semibold hover:bg-purple-100 transition border border-purple-200 dark:border-purple-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Advanced Task</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(item, idx) in form.differentiated_activities.advanced"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-purple-600">Advanced Activity #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.differentiated_activities.advanced.length > 1"
                                    type="button"
                                    @click="removeAdvanced(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Advanced / Master Craft Task *
                                </label>
                                <input
                                    v-model="item.task"
                                    type="text"
                                    placeholder="e.g. Integrate remote string monitoring shunt and diagnose injected blind reverse polarity"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                    Quality Assurance or Leadership Challenge
                                </label>
                                <input
                                    v-model="item.challenge"
                                    type="text"
                                    placeholder="e.g. Act as Quality Assurance Lead inspecting beginner terminal torque and logbook entries"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: Universal Design for Learning (UDL) Principles -->
            <div v-show="activeTab === 'udl'" class="space-y-6">
                <!-- Engagement -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <Sparkles class="w-4 h-4 text-amber-500" />
                                <span>UDL Principle 1: Multiple Means of Engagement (The "Why" of Learning)</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Fostering interest, autonomy, persistence, self-regulation, and authentic commercial relevance.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addUdlItem('udl_engagement')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Strategy</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(strategy, idx) in form.udl_engagement"
                            :key="idx"
                            class="flex items-center space-x-2"
                        >
                            <input
                                v-model="form.udl_engagement[idx]"
                                type="text"
                                placeholder="e.g. Connect practical lesson directly to rural solar clinic electrification contracts in Benue"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                            <button
                                v-if="form.udl_engagement.length > 1"
                                type="button"
                                @click="removeUdlItem('udl_engagement', idx)"
                                class="text-slate-400 hover:text-rose-500 p-1"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Representation -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <Eye class="w-4 h-4 text-blue-500" />
                                <span>UDL Principle 2: Multiple Means of Representation (The "What" of Learning)</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Accessible resources, dual-coded pictorial cards, tactile mockups, video micro-demos, and bilingual support.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addUdlItem('udl_representation')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Format</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(rep, idx) in form.udl_representation"
                            :key="idx"
                            class="flex items-center space-x-2"
                        >
                            <input
                                v-model="form.udl_representation[idx]"
                                type="text"
                                placeholder="e.g. Dual-coded pictorial job sheet with embossed tactile markings and video loop"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                            <button
                                v-if="form.udl_representation.length > 1"
                                type="button"
                                @click="removeUdlItem('udl_representation', idx)"
                                class="text-slate-400 hover:text-rose-500 p-1"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action and Expression -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <Zap class="w-4 h-4 text-emerald-500" />
                                <span>UDL Principle 3: Multiple Means of Action &amp; Expression (The "How" of Learning)</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Flexible avenues for trainees to demonstrate technical competence (hands-on build, verbal breakdown, digital photo logs).
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addUdlItem('udl_action_expression')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Expression Mode</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(action, idx) in form.udl_action_expression"
                            :key="idx"
                            class="flex items-center space-x-2"
                        >
                            <input
                                v-model="form.udl_action_expression[idx]"
                                type="text"
                                placeholder="e.g. Physical termination test with torque gauge verification + annotated video logbook"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                            <button
                                v-if="form.udl_action_expression.length > 1"
                                type="button"
                                @click="removeUdlItem('udl_action_expression', idx)"
                                class="text-slate-400 hover:text-rose-500 p-1"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: Accommodations & Gender-Responsive Strategies -->
            <div v-show="activeTab === 'accommodations'" class="space-y-6">
                <!-- Learner Accommodations with Safety Invariance -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                                <HeartHandshake class="w-4 h-4 text-rose-500" />
                                <span>Specific Learner Accommodations (With Verified Safety Non-Negotiables)</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Specify modifications for sensory, physical, cognitive, or language needs without lowering safety standards.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addAccommodation"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Accommodation</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="(acc, idx) in form.learner_accommodations"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-rose-600">Accommodation #{{ idx + 1 }}</span>
                                <button
                                    v-if="form.learner_accommodations.length > 1"
                                    type="button"
                                    @click="removeAccommodation(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Identified Learner Need / Barrier
                                    </label>
                                    <input
                                        v-model="acc.need"
                                        type="text"
                                        placeholder="e.g. Trainee with mild motor tremor / reduced grip strength"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-600 dark:text-slate-400 mb-1">
                                        Instructional / Assistive Accommodation
                                    </label>
                                    <input
                                        v-model="acc.adaptation"
                                        type="text"
                                        placeholder="e.g. Ergonomic lever tool and benchtop clamping jig"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold uppercase text-amber-700 dark:text-amber-400 mb-1">
                                    Preserved Occupational Safety Standard (Uncompromised)
                                </label>
                                <input
                                    v-model="acc.safety_rule_maintained"
                                    type="text"
                                    placeholder="e.g. Full torque spec (2.2 Nm) and electrical PPE strictly maintained"
                                    class="w-full text-xs rounded-lg border-amber-300 dark:border-amber-800 bg-white dark:bg-slate-800 text-amber-900 dark:text-amber-200"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gender-Responsive Instruction -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Gender-Responsive Instruction &amp; Equitable Participation</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Ensure female trainees are empowered in non-traditional trades with leadership roles, ergonomics, and safe workshop culture.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addUdlItem('gender_responsive_strategies')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Strategy</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(strat, idx) in form.gender_responsive_strategies"
                            :key="idx"
                            class="flex items-center space-x-2"
                        >
                            <input
                                v-model="form.gender_responsive_strategies[idx]"
                                type="text"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                            <button
                                v-if="form.gender_responsive_strategies.length > 1"
                                type="button"
                                @click="removeUdlItem('gender_responsive_strategies', idx)"
                                class="text-slate-400 hover:text-rose-500 p-1"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: Workshop Rotation Matrix & Safety Arrangements -->
            <div v-show="activeTab === 'rotation_safety'" class="space-y-6">
                <!-- Rotation Schedule -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Workshop Group &amp; Equipment Rotation Matrix</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Manage equipment bottlenecks and ensure every trainee gets equal hands-on time on industrial machinery.
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-1 text-xs">
                                <label class="text-slate-500 font-bold">Rotation Interval (Min):</label>
                                <input
                                    v-model.number="form.workshop_rotation_schedule.intervals_minutes"
                                    type="number"
                                    min="10"
                                    max="120"
                                    class="w-18 text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-1"
                                />
                            </div>
                            <button
                                type="button"
                                @click="addStation"
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                            >
                                <Plus class="w-3.5 h-3.5" />
                                <span>Add Station</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="(st, idx) in form.workshop_rotation_schedule.stations"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 space-y-2 relative"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-teal-600 dark:text-teal-400">Station {{ st.station_id }}</span>
                                <button
                                    v-if="form.workshop_rotation_schedule.stations.length > 2"
                                    type="button"
                                    @click="removeStation(idx)"
                                    class="text-slate-400 hover:text-rose-500"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Station Name</label>
                                    <input
                                        v-model="st.name"
                                        type="text"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Capacity</label>
                                    <input
                                        v-model.number="st.group_capacity"
                                        type="number"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-500 mb-0.5">Equipment &amp; Tools at Station</label>
                                <input
                                    v-model="st.equipment"
                                    type="text"
                                    placeholder="e.g. Inverter arc welders, chipping hammers, fume hood"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workshop Safety Arrangements -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Workshop Safety Protocols &amp; Emergency Arrangements</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Emergency stops, eyewash access, PPE checklists, and supervisor checkoffs.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addUdlItem('workshop_safety_arrangements')"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-950/50 text-teal-700 dark:text-teal-300 text-xs font-semibold hover:bg-teal-100 transition border border-teal-200 dark:border-teal-800"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Add Safety Protocol</span>
                        </button>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(saf, idx) in form.workshop_safety_arrangements"
                            :key="idx"
                            class="flex items-center space-x-2"
                        >
                            <input
                                v-model="form.workshop_safety_arrangements[idx]"
                                type="text"
                                class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                            />
                            <button
                                v-if="form.workshop_safety_arrangements.length > 1"
                                type="button"
                                @click="removeUdlItem('workshop_safety_arrangements', idx)"
                                class="text-slate-400 hover:text-rose-500 p-1"
                            >
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Floating Action Bar -->
            <div class="sticky bottom-4 p-4 rounded-2xl bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200 dark:border-slate-800 shadow-xl flex items-center justify-between">
                <div class="flex items-center space-x-3 text-xs text-slate-600 dark:text-slate-400">
                    <span :class="day10Status.isComplete ? 'text-emerald-500 font-bold' : 'text-teal-500 font-bold'">
                        {{ day10Status.isComplete ? '✓ Day 10 Ready for Assessment' : `${day10Status.percent}% Completed` }}
                    </span>
                    <span>·</span>
                    <span>Status: <strong class="capitalize">{{ form.status }}</strong></span>
                </div>

                <div class="flex items-center space-x-3">
                    <button
                        type="button"
                        @click="save('draft')"
                        :disabled="form.processing || !can_update"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition"
                    >
                        Save Adaptations
                    </button>
                    <button
                        type="button"
                        @click="submitForAssessment"
                        :disabled="submitForm.processing || !can_update"
                        class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        Submit for Facilitator Assessment
                    </button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
