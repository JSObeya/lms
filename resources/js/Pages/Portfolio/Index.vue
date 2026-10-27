<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Briefcase,
    Plus,
    Share2,
    CheckCircle2,
    ExternalLink,
    FileText,
    Award,
    Sparkles
} from 'lucide-vue-next';

const props = defineProps({
    portfolio: Object,
    categories: Array,
    facilitators: Array,
});

const showItemModal = ref(false);
const itemForm = useForm({
    category: 'lesson_documents',
    title: '',
    description: '',
    url: '',
});

const shareForm = useForm({
    shared_with_facilitator_id: props.portfolio.shared_with_facilitator_id || '',
    is_shared: Boolean(props.portfolio.is_shared),
});

const saveItem = () => {
    itemForm.post(route('portfolio.items.store'), {
        onSuccess: () => {
            itemForm.reset();
            showItemModal.value = false;
        },
    });
};

const updateShare = () => {
    shareForm.post(route('portfolio.share'));
};

const formatCategoryName = (cat) => {
    return cat.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};
</script>

<template>
    <Head title="Digital Teaching Portfolio — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        Digital TVET Teaching Portfolio
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Document your pedagogical practice, lesson artifacts, microteaching materials, and rubric evidence.
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    <button
                        @click="showItemModal = true"
                        class="px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition flex items-center space-x-1.5"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Add Artifact</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Portfolio Overview & Completeness -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Portfolio Completeness</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Covers {{ portfolio.items?.length || 0 }} artifacts across 9 TVET professional categories.
                    </p>
                    <div class="mt-3 flex items-center space-x-3">
                        <div class="flex-1 bg-slate-100 dark:bg-slate-800 h-3 rounded-full overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-teal-500 to-emerald-500 h-full rounded-full transition-all duration-500"
                                :style="{ width: `${portfolio.completion_percentage}%` }"
                            ></div>
                        </div>
                        <span class="text-sm font-black text-teal-600">{{ portfolio.completion_percentage }}%</span>
                    </div>
                </div>

                <!-- Sharing Controls -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 md:w-80">
                    <div class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-2 flex items-center space-x-1.5">
                        <Share2 class="w-3.5 h-3.5 text-teal-600" />
                        <span>Share With Facilitator</span>
                    </div>
                    <form @submit.prevent="updateShare" class="space-y-2">
                        <select
                            v-model="shareForm.shared_with_facilitator_id"
                            class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800"
                        >
                            <option value="">Select Master Assessor...</option>
                            <option v-for="f in facilitators" :key="f.id" :value="f.id">
                                {{ f.name }}
                            </option>
                        </select>
                        <div class="flex items-center justify-between pt-1">
                            <label class="text-xs font-medium text-slate-600 dark:text-slate-300 flex items-center space-x-2">
                                <input type="checkbox" v-model="shareForm.is_shared" class="rounded text-teal-600 h-3.5 w-3.5" />
                                <span>Enable Sharing</span>
                            </label>
                            <button
                                type="submit"
                                class="px-2.5 py-1 rounded bg-teal-600 text-white font-bold text-[11px]"
                            >
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Portfolio Items by Category Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="cat in categories"
                    :key="cat"
                    class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between mb-3 border-b pb-2">
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                                {{ formatCategoryName(cat) }}
                            </h4>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600">
                                {{ portfolio.items?.filter(i => i.category === cat).length || 0 }}
                            </span>
                        </div>

                        <!-- Artifacts in this category -->
                        <div class="space-y-2">
                            <div
                                v-for="item in portfolio.items?.filter(i => i.category === cat)"
                                :key="item.id"
                                class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 text-xs"
                            >
                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ item.title }}</div>
                                <p v-if="item.description" class="text-[11px] text-slate-500 mt-0.5">{{ item.description }}</p>
                                <a
                                    v-if="item.url"
                                    :href="item.url"
                                    target="_blank"
                                    class="text-[10px] text-teal-600 font-bold hover:underline inline-flex items-center space-x-1 mt-1"
                                >
                                    <span>View Evidence</span>
                                    <ExternalLink class="w-3 h-3" />
                                </a>
                            </div>

                            <div
                                v-if="portfolio.items?.filter(i => i.category === cat).length === 0"
                                class="text-center py-4 text-xs text-slate-400"
                            >
                                No artifact added yet.
                            </div>
                        </div>
                    </div>

                    <button
                        @click="itemForm.category = cat; showItemModal = true"
                        class="mt-4 w-full py-1.5 rounded-lg border border-dashed border-slate-300 dark:border-slate-700 text-slate-500 hover:text-teal-600 hover:border-teal-400 text-xs font-semibold flex items-center justify-center space-x-1"
                    >
                        <Plus class="w-3.5 h-3.5" />
                        <span>Add to {{ formatCategoryName(cat) }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Artifact Modal -->
        <div v-if="showItemModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3">Add Portfolio Artifact</h3>
                <form @submit.prevent="saveItem" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Portfolio Category
                        </label>
                        <select
                            v-model="itemForm.category"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        >
                            <option v-for="c in categories" :key="c" :value="c">
                                {{ formatCategoryName(c) }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Artifact Title <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="itemForm.title"
                            placeholder="e.g. Solar PV Inspection Checklist Evidence"
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Description / Reflexive Summary
                        </label>
                        <textarea
                            v-model="itemForm.description"
                            rows="2"
                            placeholder="Describe how this artifact illustrates your TVET instructional competency..."
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            Artifact URL (Cloud Document / Video / Drive Link)
                        </label>
                        <input
                            type="url"
                            v-model="itemForm.url"
                            placeholder="https://drive.google.com/..."
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-teal-500"
                        />
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-3 border-t">
                        <button
                            type="button"
                            @click="showItemModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="itemForm.processing"
                            class="px-5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs"
                        >
                            Save Artifact
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
