<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Users,
    CheckCircle2,
    Clock,
    Wand2,
    BookOpen,
    MessageSquare,
    ExternalLink,
    ChevronRight,
    Search
} from 'lucide-vue-next';

const props = defineProps({
    teachers: Object,
    metrics: Object,
});

const selectedTeacher = ref(null);
const showVerifyModal = ref(false);

const form = useForm({
    feedback: '',
    is_approved: true,
});

const openVerifyModal = (teacher) => {
    selectedTeacher.value = teacher;
    form.feedback = teacher.day8_progress?.facilitator_feedback || '';
    form.is_approved = teacher.day8_progress?.is_verified_by_facilitator ?? true;
    showVerifyModal.value = true;
};

const submitVerification = () => {
    if (!selectedTeacher.value?.day8_progress?.id) return;
    form.post(route('day8.verify', selectedTeacher.value.day8_progress.id), {
        onSuccess: () => {
            showVerifyModal.value = false;
        },
    });
};
</script>

<template>
    <Head title="Facilitator Dashboard — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                        <span>Facilitator &amp; Assessor Evaluation Studio</span>
                        <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-bold uppercase tracking-wider">
                            Assessor
                        </span>
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Monitoring teacher practical progress, rubric fidelity, and TVET competency verification.
                    </p>
                </div>
            </div>
        </template>

        <!-- Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <Users class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ metrics.total_assigned_teachers }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Assigned Trainee Teachers</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <CheckCircle2 class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ metrics.verified_wizards }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Day 8 Exercises Verified</div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <Clock class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white">{{ metrics.pending_course_reviews }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">Courses Under Review</div>
                </div>
            </div>
        </div>

        <!-- Assigned Teachers Progress Table -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-base text-slate-900 dark:text-white">
                    Trainee Teachers &amp; Practical Competency Records
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Teacher / Trainee</th>
                            <th class="px-6 py-4">Institution / School</th>
                            <th class="px-6 py-4">Trade Specialization</th>
                            <th class="px-6 py-4">Day 8 Practical Progress</th>
                            <th class="px-6 py-4">Verification</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-for="teacher in teachers.data" :key="teacher.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ teacher.name }}</div>
                                <div class="text-xs text-slate-400">{{ teacher.email }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium">
                                {{ teacher.institution || 'GTC Makurdi' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-teal-50 dark:bg-teal-950/40 text-teal-700 dark:text-teal-300">
                                    {{ teacher.trade_specialization || 'TVET General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-24 bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                                        <div
                                            class="bg-amber-500 h-full rounded-full"
                                            :style="{ width: `${teacher.day8_progress ? (teacher.day8_progress.step_1_course_created ? 10 : 0) + (teacher.day8_progress.step_10_report_exported ? 90 : 20) : 0}%` }"
                                        ></div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                        {{ teacher.day8_progress?.is_verified_by_facilitator ? '10/10' : 'In Progress' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="teacher.day8_progress?.is_verified_by_facilitator"
                                    class="inline-flex items-center space-x-1 text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300"
                                >
                                    <CheckCircle2 class="w-3.5 h-3.5" />
                                    <span>Verified</span>
                                </span>
                                <span
                                    v-else
                                    class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500"
                                >
                                    Awaiting Review
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    v-if="teacher.day8_progress"
                                    @click="openVerifyModal(teacher)"
                                    class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs transition"
                                >
                                    Review &amp; Verify
                                </button>
                                <span v-else class="text-xs text-slate-400">Not started</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verification Modal -->
        <div v-if="showVerifyModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
                    Verify Trainee Practical Exercise
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    Assessing <strong>{{ selectedTeacher?.name }}</strong> for Day 8 LMS Course Creation and Rubric Assessment Practical.
                </p>

                <form @submit.prevent="submitVerification" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Facilitator Evaluation Feedback
                        </label>
                        <textarea
                            v-model="form.feedback"
                            rows="4"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                            placeholder="Enter pedagogical and technical feedback on course objectives, resource alignment, and rubric criteria..."
                        ></textarea>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input
                            type="checkbox"
                            id="is_approved"
                            v-model="form.is_approved"
                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 h-4 w-4"
                        />
                        <label for="is_approved" class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                            Approve &amp; Sign-off Practical Competency
                        </label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showVerifyModal = false"
                            class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold text-sm transition"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow transition"
                        >
                            Save Verification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
