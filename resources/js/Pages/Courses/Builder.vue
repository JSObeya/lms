<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Plus,
    FolderPlus,
    FileText,
    Target,
    Paperclip,
    ClipboardList,
    Eye,
    Table,
    Edit2,
    Trash2,
    ArrowUp,
    ArrowDown,
    ExternalLink,
    Download,
    CheckCircle2,
    Sparkles,
    Award,
    Layers,
    X,
    FileCode,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    course: Object,
    day8_progress: Object,
});

// Modal state toggles
const showSectionModal = ref(false);
const showLessonModal = ref(false);
const showObjectiveModal = ref(false);
const showResourceModal = ref(false);

const activeSectionId = ref(null);
const activeLessonId = ref(null);

// Forms
const sectionForm = useForm({
    title: '',
    description: '',
});

const lessonForm = useForm({
    title: '',
    content: '',
});

const objectiveForm = useForm({
    course_section_id: null,
    lesson_id: null,
    action_verb: 'Identify',
    condition: 'Given a supervised workshop rig and reference worksheet',
    performance_standard: 'correctly identify at least 8 of 10 components and explain functions',
    complete_text: '',
    bloom_level: 'apply',
});

const resourceForm = useForm({
    course_section_id: null,
    lesson_id: null,
    title: '',
    description: '',
    resource_type: 'pdf',
    external_url: '',
    file: null,
});

// Submissions
const createSection = () => {
    sectionForm.post(route('sections.store', props.course.id), {
        onSuccess: () => {
            sectionForm.reset();
            showSectionModal.value = false;
        },
    });
};

const openLessonModal = (sectionId) => {
    activeSectionId.value = sectionId;
    lessonForm.reset();
    showLessonModal.value = true;
};

const createLesson = () => {
    lessonForm.post(route('lessons.store', activeSectionId.value), {
        onSuccess: () => {
            lessonForm.reset();
            showLessonModal.value = false;
        },
    });
};

const openObjectiveModal = (sectionId, lessonId = null) => {
    activeSectionId.value = sectionId;
    activeLessonId.value = lessonId;
    objectiveForm.course_section_id = sectionId;
    objectiveForm.lesson_id = lessonId;
    showObjectiveModal.value = true;
};

const createObjective = () => {
    objectiveForm.post(route('objectives.store', props.course.id), {
        onSuccess: () => {
            objectiveForm.reset();
            showObjectiveModal.value = false;
        },
    });
};

const openResourceModal = (sectionId, lessonId = null) => {
    activeSectionId.value = sectionId;
    activeLessonId.value = lessonId;
    resourceForm.course_section_id = sectionId;
    resourceForm.lesson_id = lessonId;
    showResourceModal.value = true;
};

const createResource = () => {
    resourceForm.post(route('resources.store', props.course.id), {
        onSuccess: () => {
            resourceForm.reset();
            showResourceModal.value = false;
        },
    });
};

// Reordering sections
const moveSection = (index, direction) => {
    const sections = [...props.course.sections];
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= sections.length) return;

    const temp = sections[index];
    sections[index] = sections[targetIndex];
    sections[targetIndex] = temp;

    const orderedIds = sections.map(s => s.id);
    router.post(route('sections.reorder', props.course.id), { ordered_ids: orderedIds }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Visual Builder: ${course.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2 text-xs font-semibold text-teal-600 dark:text-teal-400 mb-1">
                        <span>{{ course.category?.name }}</span>
                        <span>·</span>
                        <span class="font-mono">{{ course.code }}</span>
                        <span>·</span>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300">
                            {{ course.is_practice ? 'Mode A · Practice Sandbox' : 'Mode B · Institutional' }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Visual Course Builder: {{ course.title }}
                    </h1>
                </div>

                <div class="flex items-center space-x-2">
                    <Link
                        :href="route('courses.preview', course.id)"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 font-semibold text-xs flex items-center space-x-1.5 transition"
                    >
                        <Eye class="w-4 h-4 text-slate-500" />
                        <span>Preview as Student</span>
                    </Link>

                    <Link
                        :href="route('gradebook.show', course.id)"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-800 dark:text-slate-200 font-semibold text-xs flex items-center space-x-1.5 transition"
                    >
                        <Table class="w-4 h-4 text-slate-500" />
                        <span>Gradebook</span>
                    </Link>

                    <button
                        @click="showSectionModal = true"
                        class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center space-x-1.5 shadow-sm transition"
                    >
                        <FolderPlus class="w-4 h-4" />
                        <span>Add Topic / Section</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Course Sections & Content Stream -->
        <div class="space-y-6">
            <div v-if="course.sections?.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <FolderPlus class="w-12 h-12 mx-auto text-slate-400 mb-3" />
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No sections added to this course</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Click <strong>"Add Topic / Section"</strong> below to create your first topic (verifies Wizard Step 2). Once created, your section card will reveal the <strong>"Add Resource"</strong> button to attach your Word guide, PDF, or technical worksheet (verifies Wizard Step 4).
                </p>
                <button
                    @click="showSectionModal = true"
                    class="mt-4 px-4 py-2 rounded-xl bg-teal-600 text-white font-bold text-xs hover:bg-teal-700 transition"
                >
                    Add First Section
                </button>
            </div>

            <div
                v-for="(section, sIndex) in course.sections"
                :key="section.id"
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden"
            >
                <!-- Section Header -->
                <div class="p-5 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-bold text-teal-600 dark:text-teal-400 uppercase tracking-wider">
                                Section {{ sIndex + 1 }}
                            </span>
                            <span class="text-slate-300 dark:text-slate-600">·</span>
                            <span class="text-xs text-slate-400">Order: {{ section.order_index }}</span>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">
                            {{ section.title }}
                        </h2>
                        <p v-if="section.description" class="text-xs text-slate-500 mt-1">
                            {{ section.description }}
                        </p>
                    </div>

                    <!-- Action Controls for Section -->
                    <div class="flex items-center space-x-2 shrink-0">
                        <button
                            @click="moveSection(sIndex, -1)"
                            :disabled="sIndex === 0"
                            class="p-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 disabled:opacity-30"
                            title="Move Up"
                        >
                            <ArrowUp class="w-3.5 h-3.5" />
                        </button>
                        <button
                            @click="moveSection(sIndex, 1)"
                            :disabled="sIndex === course.sections.length - 1"
                            class="p-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 disabled:opacity-30"
                            title="Move Down"
                        >
                            <ArrowDown class="w-3.5 h-3.5" />
                        </button>

                        <button
                            @click="openObjectiveModal(section.id)"
                            class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-teal-600 dark:text-teal-400 text-xs font-bold flex items-center space-x-1"
                        >
                            <Target class="w-3.5 h-3.5" />
                            <span>Add Objective</span>
                        </button>

                        <button
                            @click="openResourceModal(section.id)"
                            class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-blue-600 dark:text-blue-400 text-xs font-bold flex items-center space-x-1"
                        >
                            <Paperclip class="w-3.5 h-3.5" />
                            <span>Add Resource</span>
                        </button>

                        <Link
                            :href="route('assignments.create', course.id)"
                            class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-amber-600 dark:text-amber-400 text-xs font-bold flex items-center space-x-1"
                        >
                            <ClipboardList class="w-3.5 h-3.5" />
                            <span>Add Assignment</span>
                        </Link>
                    </div>
                </div>

                <!-- Section Body (Objectives, Resources, Assignments, Lessons) -->
                <div class="p-6 space-y-5">
                    <!-- 1. Measurable Objectives -->
                    <div v-if="section.objectives?.length > 0">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center space-x-1.5">
                            <Target class="w-3.5 h-3.5 text-teal-600" />
                            <span>Measurable Learning Objectives</span>
                        </div>
                        <div class="space-y-2">
                            <div
                                v-for="obj in section.objectives"
                                :key="obj.id"
                                class="p-3 rounded-xl bg-teal-50/60 dark:bg-teal-950/20 border border-teal-100 dark:border-teal-900/40 text-xs"
                            >
                                <div class="flex items-center space-x-2 text-[10px] font-bold uppercase text-teal-700 dark:text-teal-400 mb-1">
                                    <span>Bloom: {{ obj.bloom_level }}</span>
                                    <span>·</span>
                                    <span>Action Verb: {{ obj.action_verb }}</span>
                                </div>
                                <div class="font-medium text-slate-800 dark:text-slate-200">
                                    {{ obj.complete_text }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Learning Resources (PDF, URL, Worksheets) -->
                    <div v-if="section.resources?.length > 0">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center space-x-1.5">
                            <Paperclip class="w-3.5 h-3.5 text-blue-600" />
                            <span>Learning Resources</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="res in section.resources"
                                :key="res.id"
                                class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700 text-xs flex items-center justify-between"
                            >
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center space-x-1.5">
                                        <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300">
                                            {{ res.resource_type }}
                                        </span>
                                        <span class="truncate">{{ res.title }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ res.formatted_file_size || res.external_url || 'Resource file' }}
                                    </div>
                                </div>

                                <div>
                                    <a
                                        v-if="res.file_path"
                                        :href="route('resources.download', res.id)"
                                        class="p-1.5 rounded-lg bg-white dark:bg-slate-700 border text-teal-600 hover:text-teal-700 inline-block"
                                        title="Download Resource"
                                    >
                                        <Download class="w-3.5 h-3.5" />
                                    </a>
                                    <a
                                        v-else-if="res.external_url"
                                        :href="res.external_url"
                                        target="_blank"
                                        class="p-1.5 rounded-lg bg-white dark:bg-slate-700 border text-blue-600 hover:text-blue-700 inline-block"
                                        title="Open External URL"
                                    >
                                        <ExternalLink class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Assignments -->
                    <div v-if="section.assignments?.length > 0">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 flex items-center space-x-1.5">
                            <ClipboardList class="w-3.5 h-3.5 text-amber-600" />
                            <span>Practical Assessment Assignments</span>
                        </div>
                        <div class="space-y-3">
                            <div
                                v-for="assign in section.assignments"
                                :key="assign.id"
                                class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                            >
                                <div>
                                    <div class="font-bold text-sm text-slate-900 dark:text-white">
                                        {{ assign.title }}
                                    </div>
                                    <div class="text-slate-500 mt-1 flex items-center space-x-3">
                                        <span>Max: {{ assign.max_score }} marks</span>
                                        <span>·</span>
                                        <span>Due: {{ assign.due_date ? new Date(assign.due_date).toLocaleDateString() : 'No date' }}</span>
                                        <span>·</span>
                                        <span v-if="assign.rubric" class="text-teal-600 font-semibold">
                                            Rubric: {{ assign.rubric.title }}
                                        </span>
                                    </div>
                                </div>

                                <Link
                                    :href="route('assignments.show', assign.id)"
                                    class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition"
                                >
                                    View / Grade Submissions &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Section Modal -->
        <div v-if="showSectionModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3">Add Course Section / Topic</h3>
                <form @submit.prevent="createSection" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Topic Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="sectionForm.title"
                            placeholder="e.g. Topic 1: Solar PV Fundamentals and Safety"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Description / Overview
                        </label>
                        <textarea
                            v-model="sectionForm.description"
                            rows="3"
                            placeholder="Briefly describe what trainees will learn in this section..."
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showSectionModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="sectionForm.processing"
                            class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs"
                        >
                            Save Section
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Objective Modal -->
        <div v-if="showObjectiveModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Write Measurable Learning Objective</h3>
                <p class="text-xs text-slate-500 mb-4">Formulate an ABCD measurable outcome for TVET competency.</p>

                <form @submit.prevent="createObjective" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Condition (Given what resources / tools?) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="objectiveForm.condition"
                            placeholder="e.g. Given a supervised solar PV training rig and multimeter"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                Action Verb <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                v-model="objectiveForm.action_verb"
                                placeholder="e.g. identify & test"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                Bloom Level
                            </label>
                            <select
                                v-model="objectiveForm.bloom_level"
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            >
                                <option value="remember">Remember</option>
                                <option value="understand">Understand</option>
                                <option value="apply">Apply (Practical)</option>
                                <option value="analyze">Analyze</option>
                                <option value="evaluate">Evaluate</option>
                                <option value="create">Create</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Performance Standard / Criterion <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="objectiveForm.performance_standard"
                            placeholder="e.g. correctly identify 8 of 10 components without safety errors"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showObjectiveModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="objectiveForm.processing"
                            class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs"
                        >
                            Save Objective
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add Resource Modal -->
        <div v-if="showResourceModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Attach Learning Resource</h3>
                <form @submit.prevent="createResource" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Resource Type
                        </label>
                        <select
                            v-model="resourceForm.resource_type"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        >
                            <option value="docx">Word Document / Guide (.docx)</option>
                            <option value="pdf">PDF Document (.pdf)</option>
                            <option value="url">Educational URL (Link)</option>
                            <option value="video_embed">External Video Embed</option>
                            <option value="image">Diagram / Schematic Image</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Resource Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="resourceForm.title"
                            placeholder="e.g. Component Technical Manual"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                    </div>

                    <div v-if="resourceForm.resource_type === 'url' || resourceForm.resource_type === 'video_embed'">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            External URL <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="url"
                            v-model="resourceForm.external_url"
                            placeholder="https://example.com/guide.pdf"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                    </div>

                    <div v-else>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            File Upload (PDF, DOCX, Images max 20MB) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="file"
                            @input="resourceForm.file = $event.target.files[0]"
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"
                            required
                        />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <button
                            type="button"
                            @click="showResourceModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="resourceForm.processing"
                            class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs"
                        >
                            Upload &amp; Attach
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
