<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Award,
    ArrowLeft,
    Copy,
    CheckCircle2
} from 'lucide-vue-next';

defineProps({
    rubric: Object,
    can_edit: Boolean,
});
</script>

<template>
    <Head :title="`Rubric: ${rubric.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-3">
                <Link
                    :href="route('rubrics.index')"
                    class="p-2 rounded-xl border border-slate-200 dark:border-slate-800 hover:bg-slate-100"
                >
                    <ArrowLeft class="w-4 h-4 text-slate-500" />
                </Link>
                <div>
                    <div class="text-xs text-teal-600 font-bold uppercase tracking-wider">
                        {{ rubric.course?.title || 'Global Assessment Template' }}
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        {{ rubric.title }}
                    </h1>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto space-y-6">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Rubric Specifications</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ rubric.description || 'No description.' }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 uppercase font-bold">Total Evaluation Points</span>
                    <div class="text-2xl font-black text-teal-600">{{ rubric.total_points }} Marks</div>
                </div>
            </div>

            <!-- Criteria Matrix -->
            <div class="space-y-4">
                <div
                    v-for="(crit, idx) in rubric.criteria"
                    :key="crit.id"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3"
                >
                    <div class="flex items-center justify-between border-b pb-2">
                        <div>
                            <span class="text-xs font-bold text-teal-600 uppercase">Criterion {{ idx + 1 }}</span>
                            <h4 class="font-bold text-base text-slate-900 dark:text-white">{{ crit.title }}</h4>
                        </div>
                        <span class="text-xs font-black text-teal-600 px-2 py-0.5 bg-teal-50 rounded-full">
                            Max {{ crit.max_points }} pts
                        </span>
                    </div>

                    <p v-if="crit.description" class="text-xs text-slate-500">{{ crit.description }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 pt-1">
                        <div
                            v-for="lvl in crit.levels"
                            :key="lvl.id"
                            class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 text-xs"
                        >
                            <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center justify-between">
                                <span>{{ lvl.title }}</span>
                                <span class="text-teal-600 font-bold">{{ lvl.points }} pts</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 leading-snug">{{ lvl.descriptor }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
