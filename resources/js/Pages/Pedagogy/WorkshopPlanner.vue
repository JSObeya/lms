<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Settings2,
    Users,
    Clock,
    ShieldCheck,
    ShieldAlert,
    AlertCircle,
    Compass,
    Wand2,
    Edit3,
    Layers,
    ArrowRight
} from 'lucide-vue-next';

const props = defineProps({
    lesson: Object,
    adaptation: Object,
    my_lessons: Array,
    can_update: Boolean,
});

const switchLesson = (event) => {
    const id = event.target.value;
    if (id) {
        router.get(route('pedagogy.workshop-planner', id));
    }
};

const stations = props.adaptation?.workshop_rotation_schedule?.stations || [];
const interval = props.adaptation?.workshop_rotation_schedule?.intervals_minutes || 30;
const safetyArrangements = props.adaptation?.workshop_safety_arrangements || [];
</script>

<template>
    <Head title="Workshop Management Planner — Greg ICT Hub LMS" />

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
                        <span class="text-emerald-600 dark:text-emerald-400 font-bold">Workshop Management Planner</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white flex items-center space-x-3">
                        <Settings2 class="w-7 h-7 text-emerald-500" />
                        <span>Workshop Station Matrix &amp; Safety Planner</span>
                    </h1>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2 text-xs">
                        <label class="text-slate-500 font-bold">Active Lesson:</label>
                        <select
                            :value="lesson ? lesson.id : ''"
                            @change="switchLesson"
                            class="text-xs rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200"
                        >
                            <option value="">-- Choose Lesson --</option>
                            <option v-for="l in my_lessons" :key="l.id" :value="l.id">
                                {{ l.title }}
                            </option>
                        </select>
                    </div>

                    <Link
                        v-if="lesson"
                        :href="route('pedagogy.inclusive-designer.edit', lesson.id)"
                        class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold shadow-sm transition"
                    >
                        <Wand2 class="w-3.5 h-3.5" />
                        <span>Edit Adaptations</span>
                    </Link>
                </div>
            </div>
        </template>

        <div v-if="!lesson" class="py-16 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <Settings2 class="w-12 h-12 mx-auto text-slate-400 mb-3" />
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">No Lesson Selected</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                Select an existing lesson above or author a new competency lesson to view workshop equipment rotation matrix.
            </p>
        </div>

        <div v-else class="space-y-8">
            <!-- Header Lesson Info -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        {{ lesson.trade_category ? lesson.trade_category.name : 'TVET Technical Trade' }}
                    </span>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">{{ lesson.title }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-3xl">
                        <strong>Task:</strong> {{ lesson.occupational_task }}
                    </p>
                </div>

                <div class="flex items-center space-x-3 text-xs">
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200">
                        <div class="font-bold">Rotation Interval</div>
                        <div class="text-lg font-black">{{ interval }} Mins</div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        <div class="font-bold">Total Stations</div>
                        <div class="text-lg font-black">{{ stations.length }}</div>
                    </div>
                </div>
            </div>

            <!-- Equipment Rotation Matrix Cards -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <Settings2 class="w-5 h-5 text-emerald-500" />
                            <span>Workshop Station &amp; Equipment Rotation Matrix</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Each group rotates every {{ interval }} minutes to ensure equal access to technical tools.
                        </p>
                    </div>
                </div>

                <div v-if="stations.length === 0" class="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                    <p class="text-xs text-slate-500">No rotation stations configured yet for this lesson.</p>
                    <Link :href="route('pedagogy.inclusive-designer.edit', lesson.id)" class="text-xs text-teal-600 font-semibold mt-2 inline-block">
                        Configure Rotation Schedule in Inclusive Designer →
                    </Link>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div
                        v-for="st in stations"
                        :key="st.station_id"
                        class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center">
                                    {{ st.station_id }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    Max: {{ st.group_capacity || 4 }} Trainees
                                </span>
                            </div>

                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ st.name }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                                <strong>Tools &amp; Equipment:</strong> {{ st.equipment || 'Standard workshop equipment' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500">
                            Rotation Period: {{ interval }} mins / session
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workshop Safety Protocols -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                            <ShieldCheck class="w-5 h-5 text-emerald-500" />
                            <span>Workshop Safety Non-Negotiables &amp; Emergency Protocols</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Mandatory health, safety, and environmental safeguards active during all practical sessions.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        v-for="(saf, idx) in safetyArrangements"
                        :key="idx"
                        class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-start space-x-3"
                    >
                        <ShieldAlert class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" />
                        <span class="text-xs text-slate-700 dark:text-slate-300 font-medium leading-relaxed">{{ saf }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
