<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BookOpen,
    ArrowLeft,
    Sparkles,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    categories: Array,
});

const form = useForm({
    title: '',
    code: '',
    trade_category_id: props.categories[0]?.id || '',
    summary: '',
    description: '',
    duration: '4 Weeks',
    difficulty_level: 'intermediate',
    start_date: '',
    end_date: '',
    visibility: 'public',
    is_practice: true,
});

const submit = () => {
    form.post(route('courses.store'));
};
</script>

<template>
    <Head title="Create New Course — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Link
                    :href="route('courses.index')"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                >
                    <ArrowLeft class="w-4 h-4 text-slate-500" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Course Creation Wizard
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Initialize your independent technical course or TVET training sandbox.
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-3xl mx-auto">
            <form @submit.prevent="submit" class="bg-white dark:bg-slate-900 p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                <!-- Mode Selection Alert -->
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800">
                    <label class="flex items-start space-x-3 cursor-pointer">
                        <input
                            type="checkbox"
                            v-model="form.is_practice"
                            class="rounded border-amber-400 text-teal-600 focus:ring-teal-500 h-5 w-5 mt-0.5"
                        />
                        <div>
                            <span class="font-bold text-sm text-amber-900 dark:text-amber-200">
                                Mode A — Teacher Training &amp; LMS Practice Sandbox
                            </span>
                            <p class="text-xs text-amber-700 dark:text-amber-300/80 mt-0.5">
                                Enables synthetic practice learner generation and fulfills Step 1 of your IDEAS-TVET Day 8 competency wizard.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Title & Code -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Course Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.title"
                            placeholder="e.g. Solar PV Installation — Practical Training"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                            required
                        />
                        <div v-if="form.errors.title" class="text-xs text-rose-500 mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Course Code
                        </label>
                        <input
                            type="text"
                            v-model="form.code"
                            placeholder="e.g. TVET-SPV-01"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm font-mono focus:border-teal-500 focus:ring-teal-500 uppercase"
                        />
                        <div v-if="form.errors.code" class="text-xs text-rose-500 mt-1">{{ form.errors.code }}</div>
                    </div>
                </div>

                <!-- Category & Level -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Trade Specialization / Category <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="form.trade_category_id"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                            required
                        >
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }} ({{ cat.is_tvet_trade ? 'TVET Priority Trade' : 'Professional' }})
                            </option>
                        </select>
                        <div v-if="form.errors.trade_category_id" class="text-xs text-rose-500 mt-1">{{ form.errors.trade_category_id }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Difficulty Level
                        </label>
                        <select
                            v-model="form.difficulty_level"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                        >
                            <option value="beginner">Beginner (Foundational)</option>
                            <option value="intermediate">Intermediate (Trade Technician)</option>
                            <option value="advanced">Advanced (Master Craftsman)</option>
                        </select>
                    </div>
                </div>

                <!-- Summary -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                        Course Summary (Short Description)
                    </label>
                    <textarea
                        v-model="form.summary"
                        rows="2"
                        placeholder="Concise 1-2 sentence summary of competencies trainees will acquire..."
                        class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                    ></textarea>
                </div>

                <!-- Full Description -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                        Detailed Syllabus &amp; Workshop Prerequisites
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        placeholder="Comprehensive course outline, PPE requirements, tools, and safety protocols..."
                        class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                    ></textarea>
                </div>

                <!-- Duration & Dates -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Duration
                        </label>
                        <input
                            type="text"
                            v-model="form.duration"
                            placeholder="e.g. 40 Hours"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Start Date
                        </label>
                        <input
                            type="date"
                            v-model="form.start_date"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            End Date
                        </label>
                        <input
                            type="date"
                            v-model="form.end_date"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500 focus:ring-teal-500"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                    <Link
                        :href="route('courses.index')"
                        class="px-5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm font-semibold transition"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-sm shadow-md transition flex items-center space-x-2"
                    >
                        <Check class="w-4 h-4" />
                        <span>Launch Course Builder &rarr;</span>
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
