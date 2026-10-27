<script setup>
import { Head, Link } from '@inertiajs/vue3';
import {
    Printer,
    ArrowLeft,
    CheckCircle2,
    ShieldAlert,
    ShieldCheck,
    Clock,
    Sparkles,
    Wrench,
    Zap
} from 'lucide-vue-next';

const props = defineProps({
    lesson: Object,
    printed_at: String,
});

const triggerPrint = () => {
    window.print();
};
</script>

<template>
    <Head :title="`Export: ${lesson.title} — TVET Competency Lesson Plan`" />

    <div class="min-h-screen bg-slate-100 dark:bg-slate-950 py-8 px-4 font-sans text-slate-800 dark:text-slate-100 print:bg-white print:p-0 print:text-black">
        <!-- Floating Print Toolbar (Hidden during print) -->
        <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between print:hidden">
            <Link
                :href="route('pedagogy.lessons.edit', lesson.id)"
                class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition shadow-xs"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Back to Editor</span>
            </Link>

            <div class="flex items-center space-x-3">
                <span class="text-xs text-slate-500">Official TVET Lesson Plan Format</span>
                <button
                    type="button"
                    @click="triggerPrint"
                    class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-sm transition"
                >
                    <Printer class="w-4 h-4" />
                    <span>Print / Save as PDF</span>
                </button>
            </div>
        </div>

        <!-- Printable Document Body -->
        <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-xl border border-slate-200 text-slate-900 print:shadow-none print:border-none print:p-6 print:rounded-none">
            <!-- Header with Official TVET Branding -->
            <div class="border-b-2 border-rose-900 pb-6 mb-6">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-[11px] font-black uppercase tracking-widest text-rose-800">
                            Federal Ministry of Education · IDEAS–TVET Technical Training Project
                        </div>
                        <h1 class="text-2xl font-black text-slate-900 mt-1">
                            Competency-Based Technical Lesson Plan
                        </h1>
                        <div class="text-xs text-slate-600 mt-0.5">
                            Benue State TVET Technical &amp; Vocational Training Hub · Greg ICT Hub LMS
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <div class="font-bold text-slate-800">Version {{ lesson.version }}.0</div>
                        <div>Date: {{ printed_at }}</div>
                    </div>
                </div>

                <!-- Metadata Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-slate-200 text-xs">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-500">Occupational Trade</div>
                        <div class="font-bold text-slate-900">{{ lesson.trade_category ? lesson.trade_category.name : 'Technical Trade' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-500">Target Level</div>
                        <div class="font-bold text-slate-900">{{ lesson.target_level }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-500">Duration</div>
                        <div class="font-bold text-slate-900">{{ lesson.duration_minutes }} Minutes</div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-500">Instructor</div>
                        <div class="font-bold text-slate-900">{{ lesson.user ? lesson.user.name : 'TVET Certified Instructor' }}</div>
                    </div>
                </div>
            </div>

            <!-- Standard Reference -->
            <div v-if="lesson.occupational_standard" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs mb-6 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-teal-800">
                        Occupational Standard: {{ lesson.occupational_standard.code }} — {{ lesson.occupational_standard.title }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-teal-100 text-teal-800">
                        {{ lesson.occupational_standard.is_official ? 'Approved NOS Nigeria' : 'Instructor Exemplar' }}
                    </span>
                </div>
                <div class="text-[11px] text-slate-600">
                    Source: {{ lesson.occupational_standard.source_authority }} ({{ lesson.occupational_standard.version }})
                </div>
            </div>

            <!-- Occupational Task -->
            <div class="mb-6">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-1">Occupational Task / Duty</h2>
                <div class="text-sm font-semibold text-slate-900 bg-rose-50/50 p-3 rounded-lg border border-rose-100">
                    {{ lesson.occupational_task }}
                </div>
            </div>

            <!-- Measurable Learning Outcomes -->
            <div class="mb-6">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-2">Measurable Learning Outcomes</h2>
                <ol class="list-decimal list-inside space-y-1 text-xs text-slate-800">
                    <li v-for="(out, idx) in lesson.learning_outcomes" :key="idx" class="leading-relaxed">
                        <span class="font-bold text-slate-900">{{ out.verb }}</span> ({{ out.condition }}):
                        {{ out.outcome_text || `${out.standard}` }}
                    </li>
                </ol>
            </div>

            <!-- 4-Step Demonstration Plan -->
            <div class="mb-6">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-2">Instructor 4-Step Demonstration Sequence</h2>
                <div class="space-y-3">
                    <div
                        v-for="step in lesson.demonstration_plan"
                        :key="step.step"
                        class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1"
                    >
                        <div class="flex items-center justify-between font-bold text-slate-900">
                            <span>Step {{ step.step }}: {{ step.name }}</span>
                            <span v-if="step.key_tolerance" class="text-teal-700 font-semibold text-[11px]">
                                Tolerance: {{ step.key_tolerance }}
                            </span>
                        </div>
                        <p class="text-slate-700">{{ step.instructor_action }}</p>
                        <div v-if="step.safety_ppe" class="text-[11px] text-rose-700 font-semibold pt-1">
                            Safety / PPE: {{ step.safety_ppe }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Learner Practice (Guided & Independent) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-2">Guided Practice (With Checkpoints)</h2>
                    <ul class="space-y-2 text-xs">
                        <li
                            v-for="(g, idx) in lesson.guided_practice"
                            :key="idx"
                            class="p-2.5 rounded-lg border border-slate-200 bg-slate-50"
                        >
                            <div class="font-semibold text-slate-900">{{ idx + 1 }}. {{ g.activity }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Checkpoint: {{ g.check_point }}</div>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-2">Independent Practice</h2>
                    <ul class="space-y-2 text-xs">
                        <li
                            v-for="(ind, idx) in lesson.independent_practice"
                            :key="idx"
                            class="p-2.5 rounded-lg border border-slate-200 bg-slate-50"
                        >
                            <div class="font-semibold text-slate-900">{{ idx + 1 }}. {{ ind.activity }}</div>
                            <div class="text-[11px] text-teal-700 mt-0.5">Metric: {{ ind.success_metric }} ({{ ind.time_minutes }}m)</div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Problem-Based Learning: Fault Diagnosis -->
            <div v-if="lesson.fault_diagnosis_scenarios && lesson.fault_diagnosis_scenarios.length" class="mb-6">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-2">Problem-Based Fault Diagnosis Scenarios</h2>
                <div class="space-y-2">
                    <div
                        v-for="(f, idx) in lesson.fault_diagnosis_scenarios"
                        :key="idx"
                        class="p-3 rounded-lg border border-slate-200 text-xs"
                    >
                        <div class="font-bold text-slate-900">Symptom: {{ f.fault_symptom }}</div>
                        <div class="text-[11px] text-slate-600 mt-0.5"><strong>Root Cause:</strong> {{ f.root_cause }}</div>
                        <div class="text-[11px] text-slate-600"><strong>Procedure:</strong> {{ f.diagnostic_procedure }}</div>
                        <div v-if="f.safety_rule" class="text-[11px] text-rose-700 font-semibold mt-1">Safety: {{ f.safety_rule }}</div>
                    </div>
                </div>
            </div>

            <!-- Assessment Criteria & Evidence -->
            <div class="mb-6">
                <h2 class="text-xs font-black uppercase tracking-wider text-rose-900 mb-2">Assessment Criteria &amp; Evidence Requirements</h2>
                <table class="w-full text-left text-xs border border-slate-200 divide-y divide-slate-200">
                    <thead class="bg-slate-100 text-slate-700 font-bold">
                        <tr>
                            <th class="p-2">Criterion</th>
                            <th class="p-2">Evidence Type</th>
                            <th class="p-2">Passing Standard</th>
                            <th class="p-2">Safety Flag</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr v-for="(crit, idx) in lesson.assessment_criteria" :key="idx">
                            <td class="p-2 font-medium">{{ crit.criterion }}</td>
                            <td class="p-2 text-slate-600">{{ crit.evidence_type }}</td>
                            <td class="p-2 text-slate-600">{{ crit.passing_score }}</td>
                            <td class="p-2">
                                <span v-if="crit.is_safety_critical" class="text-[10px] font-bold text-rose-700 uppercase">Critical Safety</span>
                                <span v-else class="text-[10px] text-slate-400">Standard</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Inclusive Adaptations Section (Day 10 Extension) -->
            <div v-if="lesson.inclusive_adaptation" class="border-t-2 border-teal-800 pt-6 mt-6 page-break-before">
                <div class="text-[10px] font-black uppercase tracking-widest text-teal-800">
                    Day 10 Inclusive Instructional Adaptation
                </div>
                <h3 class="text-lg font-bold text-slate-900 mt-0.5 mb-4">
                    Universal Design for Learning (UDL) &amp; Differentiated Schedule
                </h3>

                <!-- 3-Tier Activities -->
                <div class="grid grid-cols-3 gap-3 text-xs mb-4">
                    <div class="p-3 rounded-lg border border-emerald-200 bg-emerald-50/50">
                        <div class="font-bold text-emerald-900 mb-1">Tier 1: Beginner</div>
                        <ul class="space-y-1 text-[11px] text-slate-700">
                            <li v-for="(b, i) in lesson.inclusive_adaptation.differentiated_activities?.beginner" :key="i">
                                • {{ b.task }}
                            </li>
                        </ul>
                    </div>
                    <div class="p-3 rounded-lg border border-blue-200 bg-blue-50/50">
                        <div class="font-bold text-blue-900 mb-1">Tier 2: Intermediate</div>
                        <ul class="space-y-1 text-[11px] text-slate-700">
                            <li v-for="(m, i) in lesson.inclusive_adaptation.differentiated_activities?.intermediate" :key="i">
                                • {{ m.task }}
                            </li>
                        </ul>
                    </div>
                    <div class="p-3 rounded-lg border border-purple-200 bg-purple-50/50">
                        <div class="font-bold text-purple-900 mb-1">Tier 3: Advanced</div>
                        <ul class="space-y-1 text-[11px] text-slate-700">
                            <li v-for="(a, i) in lesson.inclusive_adaptation.differentiated_activities?.advanced" :key="i">
                                • {{ a.task }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Workshop Equipment Rotation Matrix -->
                <div v-if="lesson.inclusive_adaptation.workshop_rotation_schedule" class="mb-4">
                    <div class="text-xs font-bold text-slate-900 mb-1">
                        Equipment Rotation Matrix (Interval: {{ lesson.inclusive_adaptation.workshop_rotation_schedule.intervals_minutes }} Mins)
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                        <div
                            v-for="st in lesson.inclusive_adaptation.workshop_rotation_schedule.stations"
                            :key="st.station_id"
                            class="p-2 border border-slate-200 rounded-lg bg-slate-50"
                        >
                            <div class="font-bold text-slate-900">Station {{ st.station_id }}: {{ st.name }}</div>
                            <div class="text-slate-500 text-[10px]">Max: {{ st.group_capacity }} trainees</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="border-t border-slate-300 pt-8 mt-12 grid grid-cols-2 gap-8 text-xs">
                <div>
                    <div class="border-b border-slate-400 pb-12"></div>
                    <div class="font-bold text-slate-900 mt-1">Instructor Signature &amp; Date</div>
                    <div class="text-[11px] text-slate-500">{{ lesson.user ? lesson.user.name : '' }}</div>
                </div>
                <div>
                    <div class="border-b border-slate-400 pb-12"></div>
                    <div class="font-bold text-slate-900 mt-1">Facilitator / Verifier Signature &amp; Date</div>
                    <div class="text-[11px] text-slate-500">IDEAS-TVET Assessment Committee</div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    @page {
        margin: 1.5cm;
    }
    .page-break-before {
        page-break-before: always;
    }
}
</style>
