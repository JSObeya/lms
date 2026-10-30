<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Printer, ArrowLeft, CheckCircle2, XCircle, Award, ShieldCheck } from 'lucide-vue-next';

const props = defineProps({
    attempt: {
        type: Object,
        required: true,
    },
});

const printSlip = () => {
    window.print();
};
</script>

<template>
    <Head :title="`Official Evaluation Slip - ${attempt.user?.name}`" />

    <div class="min-h-screen bg-slate-100 py-6 px-4 print:py-0 print:px-0 print:bg-white text-slate-900 font-sans">
        <!-- Floating Print Bar (Hidden on print) -->
        <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between print:hidden">
            <Link
                :href="route('quizzes.attempt', attempt.id)"
                class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl shadow-xs border border-slate-200 transition"
            >
                <ArrowLeft class="w-4 h-4" />
                <span>Back to Result</span>
            </Link>

            <button
                @click="printSlip"
                class="inline-flex items-center space-x-2 px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-xl text-xs font-bold shadow-md transition"
            >
                <Printer class="w-4 h-4" />
                <span>Print / Save as PDF</span>
            </button>
        </div>

        <!-- Official Printable Paper Slip (A4 Styled) -->
        <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 border border-slate-200 print:border-0 rounded-2xl print:rounded-none shadow-sm print:shadow-none space-y-6">
            <!-- Header Block -->
            <div class="text-center border-b-2 border-slate-800 pb-5 space-y-1.5">
                <div class="flex items-center justify-center space-x-2 text-xs font-black tracking-widest text-slate-500 uppercase">
                    <span>Federal Republic of Nigeria</span>
                    <span>•</span>
                    <span>Federal Ministry of Education</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 uppercase tracking-tight">
                    IDEAS-TVET Technical Teacher Competency Assessment
                </h1>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider">
                    Official Individual Evaluation &amp; Performance Record
                </p>
                <div class="pt-1 text-[11px] font-mono text-slate-400">
                    Document Ref: IDEAS/TVET/EVAL/{{ attempt.id.toString().padStart(6, '0') }} • Date: {{ new Date(attempt.completed_at).toLocaleDateString() }}
                </div>
            </div>

            <!-- Candidate Demographic Details Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Candidate Name:</span>
                    <strong class="text-sm font-bold text-slate-900">{{ attempt.user?.name }}</strong>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Email Address:</span>
                    <span class="text-slate-800 font-medium">{{ attempt.user?.email }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Phone Number:</span>
                    <span class="text-slate-800 font-medium">{{ attempt.user?.phone || 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Institution / College:</span>
                    <strong class="text-slate-900 font-semibold">{{ attempt.user?.institution || 'Technical College' }}</strong>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">State of Posting:</span>
                    <strong class="text-slate-900 font-semibold">{{ attempt.user?.state }} State</strong>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Trade Specialization:</span>
                    <strong class="text-teal-800 font-bold">{{ attempt.user?.trade_specialization || 'Technical Education' }}</strong>
                </div>
            </div>

            <!-- Official Scorecard Box -->
            <div class="p-6 border-2 border-slate-900 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="space-y-2 text-center sm:text-left">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Assessment Specification: {{ attempt.quiz?.title }}
                    </span>
                    <div class="flex items-center space-x-3">
                        <span
                            class="px-3.5 py-1.5 rounded-xl font-black text-xs uppercase tracking-wider inline-flex items-center space-x-1.5 text-white"
                            :class="attempt.passed ? 'bg-emerald-700' : 'bg-rose-700'"
                        >
                            <CheckCircle2 v-if="attempt.passed" class="w-4 h-4" />
                            <XCircle v-else class="w-4 h-4" />
                            <span>{{ attempt.passed ? 'COMPETENCY DEMONSTRATED (PASS)' : 'NEEDS DEVELOPMENTAL SUPPORT' }}</span>
                        </span>
                        <span class="px-3 py-1 bg-slate-100 border border-slate-300 rounded-lg font-bold text-xs">
                            Grade: {{ attempt.grade }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600">
                        Passing Threshold: {{ attempt.quiz?.passing_percentage }}% • Total Examination MCQs: {{ attempt.total_questions }}
                    </p>
                </div>

                <div class="text-center sm:text-right shrink-0">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Official Score</span>
                    <div class="text-4xl font-black text-slate-900">
                        {{ attempt.score }} / {{ attempt.max_score }}
                    </div>
                    <span class="text-sm font-black" :class="attempt.passed ? 'text-emerald-700' : 'text-rose-700'">
                        {{ attempt.percentage }}%
                    </span>
                </div>
            </div>

            <!-- Module Breakdown Table -->
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Domain Performance Breakdown
                </h4>
                <table class="w-full text-xs text-left border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="p-3">Competency Domain</th>
                            <th class="p-3 text-center">Questions</th>
                            <th class="p-3 text-center">Correct</th>
                            <th class="p-3 text-center">Score</th>
                            <th class="p-3 text-right">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <tr v-for="cat in (attempt.category_scores || [])" :key="cat.category">
                            <td class="p-3 font-semibold text-slate-900">{{ cat.category }}</td>
                            <td class="p-3 text-center text-slate-600">{{ cat.total }}</td>
                            <td class="p-3 text-center text-slate-900 font-bold">{{ cat.correct }}</td>
                            <td class="p-3 text-center text-slate-900">{{ cat.score }} / {{ cat.max_score }}</td>
                            <td class="p-3 text-right font-black" :class="cat.percentage >= 60 ? 'text-teal-800' : 'text-rose-700'">
                                {{ cat.percentage }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Quantitative Stats -->
            <div class="grid grid-cols-4 gap-2 text-center text-xs p-3 bg-slate-50 border border-slate-200 rounded-xl">
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block">Correct</span>
                    <strong class="text-emerald-700 font-bold">{{ attempt.correct_answers_count }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block">Incorrect</span>
                    <strong class="text-rose-700 font-bold">{{ attempt.incorrect_answers_count }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block">Unanswered</span>
                    <strong class="text-slate-600 font-bold">{{ attempt.unanswered_count }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase block">Duration</span>
                    <strong class="text-slate-900 font-bold">{{ attempt.formatted_duration }}</strong>
                </div>
            </div>

            <!-- Sign-Off & Official Seals -->
            <div class="pt-8 border-t border-slate-300 grid grid-cols-2 gap-8 text-xs text-slate-600">
                <div class="space-y-10">
                    <div class="border-b border-slate-400 pb-1">
                        <span class="font-bold text-slate-900 block">Assessor / Facilitator Signature</span>
                    </div>
                    <div class="flex items-center space-x-2 text-[11px] text-slate-500">
                        <ShieldCheck class="w-4 h-4 text-teal-600 shrink-0" />
                        <span>System Verified Digital Assessment Trail</span>
                    </div>
                </div>

                <div class="space-y-10">
                    <div class="border-b border-slate-400 pb-1">
                        <span class="font-bold text-slate-900 block">IDEAS Project Coordinator / Official Stamp</span>
                    </div>
                    <div class="text-[10px] text-right text-slate-400 font-mono">
                        Generated: {{ new Date().toLocaleString() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    @page {
        margin: 15mm 15mm;
        size: A4 portrait;
    }
}
</style>
