<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    ClipboardList,
    ArrowLeft,
    Check,
    Calendar,
    Award
} from 'lucide-vue-next';

const props = defineProps({
    course: Object,
    sections: Array,
    rubrics: Array,
});

const form = useForm({
    course_section_id: props.sections[0]?.id || '',
    rubric_id: props.rubrics[0]?.id || '',
    title: '',
    instructions: '',
    max_score: 100,
    allow_submissions_from: '',
    due_date: '',
    cut_off_date: '',
    timezone: 'Africa/Lagos',
    submission_type: 'file_and_text',
    accepted_file_types: 'pdf,docx,jpg,png,zip',
    max_files: 2,
    max_file_size_mb: 15,
    late_submission_policy: 'allowed',
    status: 'published',
});

const submit = () => {
    form.post(route('assignments.store', props.course.id));
};
</script>

<template>
    <Head :title="`Create Assignment — ${course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Link
                    :href="route('courses.builder', course.id)"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100"
                >
                    <ArrowLeft class="w-4 h-4 text-slate-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Create Assignment / Practical Exercise
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Define instructions, rubrics, and deadlines for <strong>{{ course.title }}</strong>.
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <!-- Title & Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Assignment Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.title"
                            placeholder="e.g. Solar PV Components & Safety Inspection"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                        <div v-if="form.errors.title" class="text-xs text-rose-500 mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Associated Topic / Section
                        </label>
                        <select
                            v-model="form.course_section_id"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        >
                            <option v-for="sec in sections" :key="sec.id" :value="sec.id">
                                {{ sec.title }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Instructions -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                        Instructions &amp; Practical Requirements <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        v-model="form.instructions"
                        rows="4"
                        placeholder="Detailed assignment brief, required workshop observations, expected deliverables, and safety notes..."
                        class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        required
                    ></textarea>
                </div>

                <!-- Rubric Attachment & Max Score -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Assessment Rubric
                        </label>
                        <select
                            v-model="form.rubric_id"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        >
                            <option value="">No Rubric (Manual Points Grading)</option>
                            <option v-for="rub in rubrics" :key="rub.id" :value="rub.id">
                                {{ rub.title }} ({{ rub.total_points }} marks)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Maximum Score <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="number"
                            v-model="form.max_score"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                    </div>
                </div>

                <!-- Submission Types & File Format -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Submission Mode
                        </label>
                        <select
                            v-model="form.submission_type"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        >
                            <option value="file_and_text">File Upload + Online Text</option>
                            <option value="file">File Upload Only (PDF / DOCX)</option>
                            <option value="online_text">Online Text Entry Only</option>
                            <option value="url">External Project URL</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Accepted File Extensions
                        </label>
                        <input
                            type="text"
                            v-model="form.accepted_file_types"
                            placeholder="pdf,docx,jpg,png,zip"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        />
                    </div>
                </div>

                <!-- Deadlines & Timezone -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Due Date (Target Deadline)
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.due_date"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Cut-Off Date (Strict Close)
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.cut_off_date"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        />
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                    <Link
                        :href="route('courses.builder', course.id)"
                        class="px-4 py-2 text-xs font-semibold text-slate-500"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow transition"
                    >
                        Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
