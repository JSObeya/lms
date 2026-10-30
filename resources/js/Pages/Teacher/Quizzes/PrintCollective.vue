<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft, ShieldCheck, CheckCircle2, XCircle } from 'lucide-vue-next';

const props = defineProps({
    attempts: {
        type: Array,
        required: true,
    },
    summary: {
        type: Object,
        required: true,
    },
});

const printSheet = () => {
    window.print();
};
</script>

<template>
    <Head title="Collective Master Evaluation Sheet - TVET Teacher Assessments" />

    <div class="min-h-screen bg-slate-100 py-6 px-4 print:py-0 print:px-0 print:bg-white text-slate-900 font-sans">
        <!-- Floating Action Header (Hidden when printing) -->
        <div class="max-w-6xl mx-auto mb-6 flex items-center justify-between print:hidden">
            <Link
                :href="route('admin.quizzes.analytics')"
                class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl shadow-xs border border-slate-200 transition"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Back to Analytics Dashboard</span>
            </Link>

            <button
                @click="printSheet"
                class="inline-flex items-center space-x-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-md transition"
            >
                <Printer class="w-4 h-4 text-amber-400" />
                <span>Print Master Evaluation Sheet</span>
            </button>
        </div>

        <!-- Master Printable Document (A4 Landscape / Clean Fit) -->
        <div class="max-w-6xl mx-auto bg-white p-8 sm:p-10 border border-slate-200 print:border-0 rounded-2xl print:rounded-none shadow-sm print:shadow-none space-y-5">
            <!-- Institutional Header -->
            <div class="text-center border-b-2 border-slate-900 pb-4 space-y-1">
                <div class="text-[10px] font-black tracking-widest text-slate-500 uppercase">
                    Federal Republic of Nigeria • Federal Ministry of Education
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
                    IDEAS-TVET Teacher Competency Assessment
                </h1>
                <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Master Evaluation Sheet &amp; Collective Results Roster (50 MCQs)
                </h2>
                <div class="pt-1 flex flex-wrap items-center justify-center gap-x-6 text-[11px] text-slate-500">
                    <span>Generated: <strong>{{ summary.generated_at }}</strong></span>
                    <span>Evaluator: <strong>{{ summary.evaluator_name }}</strong></span>
                    <span>Cohort Size: <strong>{{ summary.total_evaluated }} Educators</strong></span>
                </div>
            </div>

            <!-- Summary KPI Strip -->
            <div class="grid grid-cols-4 gap-3 p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-center">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Total Candidates</span>
                    <strong class="text-base font-black text-slate-900">{{ summary.total_evaluated }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Competency Certified</span>
                    <strong class="text-base font-black text-emerald-700">{{ summary.total_passed }} ({{ summary.pass_rate }}%)</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Needs Support</span>
                    <strong class="text-base font-black text-rose-700">{{ summary.total_needs_review }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block font-semibold">Cohort Mean Score</span>
                    <strong class="text-base font-black text-teal-800">{{ summary.average_score }}%</strong>
                </div>
            </div>

            <!-- Collective Results Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left border-collapse border border-slate-300">
                    <thead class="bg-slate-100 text-slate-800 uppercase font-bold text-[10px]">
                        <tr class="border-b border-slate-300">
                            <th class="p-2 border-r border-slate-300 text-center w-8">S/N</th>
                            <th class="p-2 border-r border-slate-300">Teacher / Candidate</th>
                            <th class="p-2 border-r border-slate-300">Technical College / Institution</th>
                            <th class="p-2 border-r border-slate-300">State</th>
                            <th class="p-2 border-r border-slate-300">Trade Specialization</th>
                            <th class="p-2 border-r border-slate-300 text-center w-16">Score /50</th>
                            <th class="p-2 border-r border-slate-300 text-center w-16">%</th>
                            <th class="p-2 border-r border-slate-300 text-center">Evaluation Outcome</th>
                            <th class="p-2 text-center w-28">Assessor Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr
                            v-for="(att, idx) in attempts"
                            :key="att.id"
                            class="print:break-inside-avoid"
                        >
                            <td class="p-2 text-center font-bold text-slate-500 border-r border-slate-200">
                                {{ idx + 1 }}
                            </td>
                            <td class="p-2 font-bold text-slate-900 border-r border-slate-200">
                                <div>{{ att.user?.name || 'Educator' }}</div>
                                <div class="text-[10px] text-slate-500 font-normal">{{ att.user?.email }}</div>
                            </td>
                            <td class="p-2 text-slate-800 border-r border-slate-200">
                                {{ att.user?.institution || 'Technical College' }}
                            </td>
                            <td class="p-2 text-slate-700 border-r border-slate-200">
                                {{ att.user?.state }}
                            </td>
                            <td class="p-2 font-medium text-slate-700 border-r border-slate-200">
                                {{ att.user?.trade_specialization || 'Technical Education' }}
                            </td>
                            <td class="p-2 text-center font-black text-slate-900 border-r border-slate-200">
                                {{ att.score }}
                            </td>
                            <td class="p-2 text-center font-black border-r border-slate-200" :class="att.percentage >= 60 ? 'text-emerald-700' : 'text-rose-700'">
                                {{ att.percentage }}%
                            </td>
                            <td class="p-2 text-center border-r border-slate-200">
                                <span
                                    class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider"
                                    :class="att.passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                >
                                    {{ att.passed ? 'COMPETENT' : 'NEEDS SUPPORT' }}
                                </span>
                            </td>
                            <td class="p-2 text-center text-[10px] text-slate-500 font-medium">
                                {{ att.grade }}
                            </td>
                        </tr>

                        <tr v-if="!attempts || attempts.length === 0">
                            <td colspan="9" class="p-6 text-center text-slate-500">
                                No assessment records selected for collective printing.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Signatures Block -->
            <div class="pt-8 border-t-2 border-slate-800 grid grid-cols-2 gap-12 text-xs print:break-inside-avoid">
                <div class="space-y-10">
                    <div class="border-b border-slate-400 pb-1">
                        <span class="font-bold text-slate-900 block">Lead TVET Facilitator / Chief Assessor</span>
                        <span class="text-[10px] text-slate-500">Signature &amp; Date: _____________________________</span>
                    </div>
                    <div class="flex items-center space-x-1.5 text-[10px] text-slate-500">
                        <ShieldCheck class="w-4 h-4 text-teal-700 shrink-0" />
                        <span>National TVET Certification &amp; Standardized Assessment Trail</span>
                    </div>
                </div>

                <div class="space-y-10">
                    <div class="border-b border-slate-400 pb-1">
                        <span class="font-bold text-slate-900 block">National Project Coordinator / State Lead</span>
                        <span class="text-[10px] text-slate-500">Signature &amp; Seal: _____________________________</span>
                    </div>
                    <div class="text-[10px] text-right text-slate-400 font-mono">
                        Page 1 of 1 • System Generated
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    @page {
        margin: 10mm 10mm;
        size: A4 landscape;
    }
}
</style>
