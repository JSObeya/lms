<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    Search,
    Plus,
    BookOpen,
    Wand2,
    Eye,
    Users,
    Table,
    Layers,
    Filter,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    courses: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const category = ref(props.filters.category || '');
const mode = ref(props.filters.mode || '');
const myCoursesOnly = ref(props.filters.my_courses === '1');

const applyFilter = () => {
    router.get(
        route('courses.index'),
        {
            search: search.value || undefined,
            category: category.value || undefined,
            mode: mode.value || undefined,
            my_courses: myCoursesOnly.value ? '1' : undefined,
        },
        { preserveState: true, replace: true }
    );
};
</script>

<template>
    <Head title="Course Catalogue — Greg ICT Hub LMS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                        TVET Course Catalogue &amp; Training Studios
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Independent teacher courses across 8 priority trades and modern technical education domains.
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <Link
                        :href="route('courses.create')"
                        class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm shadow-sm transition"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Create Course</span>
                    </Link>
                </div>
            </div>
        </template>

        <!-- Filters & Search Toolbar -->
        <div class="mb-8 p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Search Input -->
                <div class="relative flex-1 max-w-md">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
                    <input
                        type="text"
                        v-model="search"
                        @keydown.enter="applyFilter"
                        placeholder="Search courses by title, code or keyword..."
                        class="w-full pl-10 pr-4 py-2 rounded-xl text-sm border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:border-teal-500 focus:ring-teal-500"
                    />
                </div>

                <!-- Mode Toggle -->
                <div class="flex items-center space-x-2">
                    <button
                        @click="mode = ''; applyFilter()"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition',
                            mode === '' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'
                        ]"
                    >
                        All Modes
                    </button>
                    <button
                        @click="mode = 'practice'; applyFilter()"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition',
                            mode === 'practice' ? 'bg-amber-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'
                        ]"
                    >
                        Mode A · Practice Sandboxes
                    </button>
                    <button
                        @click="mode = 'institutional'; applyFilter()"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold transition',
                            mode === 'institutional' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'
                        ]"
                    >
                        Mode B · Institutional
                    </button>
                </div>
            </div>

            <!-- Categories Filter Pills -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-1 pt-1 text-xs">
                <span class="text-slate-400 text-[11px] font-bold uppercase shrink-0">Trades:</span>
                <button
                    @click="category = ''; applyFilter()"
                    :class="[
                        'px-2.5 py-1 rounded-full whitespace-nowrap transition font-medium',
                        category === '' ? 'bg-teal-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
                    ]"
                >
                    All Categories
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="category = cat.slug; applyFilter()"
                    :class="[
                        'px-2.5 py-1 rounded-full whitespace-nowrap transition font-medium',
                        category === cat.slug ? 'bg-teal-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'
                    ]"
                >
                    {{ cat.name }}
                </button>
            </div>
        </div>

        <!-- Course Cards Grid -->
        <div v-if="courses.data.length === 0" class="p-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
            <BookOpen class="w-12 h-12 mx-auto text-slate-400 mb-3" />
            <h4 class="text-base font-bold text-slate-800 dark:text-slate-200">No courses match your filter</h4>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Try resetting your search query or trade filter, or create a new course.
            </p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="course in courses.data"
                :key="course.id"
                class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition flex flex-col justify-between"
            >
                <div>
                    <!-- Badge Header -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span
                            :class="[
                                'text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full',
                                course.is_practice
                                    ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800'
                                    : 'bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800'
                            ]"
                        >
                            {{ course.is_practice ? 'Mode A · Practice Sandbox' : 'Mode B · Institutional' }}
                        </span>

                        <span
                            :class="[
                                'text-[10px] font-semibold px-2 py-0.5 rounded-full',
                                course.status === 'published'
                                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300'
                                    : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                            ]"
                        >
                            {{ course.status }}
                        </span>
                    </div>

                    <h3 class="font-bold text-base text-slate-900 dark:text-white leading-snug line-clamp-2">
                        {{ course.title }}
                    </h3>

                    <div class="text-xs text-teal-600 dark:text-teal-400 font-semibold mt-1 flex items-center justify-between">
                        <span>{{ course.category?.name }}</span>
                        <span class="text-slate-400 font-mono">{{ course.code }}</span>
                    </div>

                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-3">
                        {{ course.summary || 'Practical technical instruction course.' }}
                    </p>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                        <span>{{ course.sections_count || 0 }} Sections</span>
                        <span>{{ course.assignments_count || 0 }} Assignments</span>
                        <span>{{ course.students_count || 0 }} Students</span>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <Link
                        :href="route('courses.show', course.id)"
                        class="text-xs font-bold text-teal-600 dark:text-teal-400 hover:underline"
                    >
                        View Syllabus &rarr;
                    </Link>

                    <div class="flex items-center space-x-1">
                        <Link
                            :href="route('courses.preview', course.id)"
                            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900"
                            title="Preview as Student"
                        >
                            <Eye class="w-4 h-4" />
                        </Link>

                        <Link
                            v-if="course.user_id === $page.props.auth.user.id || $page.props.auth.user.roles.includes('super_admin')"
                            :href="route('courses.builder', course.id)"
                            class="px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs flex items-center space-x-1"
                        >
                            <Wand2 class="w-3.5 h-3.5" />
                            <span>Builder</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="courses.links?.length > 3" class="mt-8 flex justify-center space-x-1">
            <template v-for="(link, key) in courses.links" :key="key">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    v-html="link.label"
                    :class="[
                        'px-3 py-1.5 text-xs rounded-lg transition',
                        link.active
                            ? 'bg-teal-600 text-white font-bold'
                            : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100'
                    ]"
                />
            </template>
        </div>
    </AuthenticatedLayout>
</template>
