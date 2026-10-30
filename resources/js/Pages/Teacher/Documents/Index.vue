<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {
    FileText,
    Download,
    Eye,
    UploadCloud,
    Search,
    Filter,
    Plus,
    Trash2,
    Edit3,
    CheckCircle2,
    AlertCircle,
    HardDrive,
    FileCheck,
    Layers,
    Shield,
    BookOpen,
    ExternalLink,
    Clock,
    User,
    X,
    FileDown
} from 'lucide-vue-next';

const props = defineProps({
    documents: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({ search: '', category: 'all', file_type: 'all' }),
    },
    stats: {
        type: Object,
        default: () => ({ total_documents: 0, pdf_count: 0, total_downloads: 0 }),
    },
    canManage: {
        type: Boolean,
        default: false,
    },
});

const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');
const selectedFileType = ref(props.filters.file_type || 'all');

// Modals
const showUploadModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const documentToEdit = ref(null);
const documentToDelete = ref(null);

// Upload Form
const uploadForm = useForm({
    title: '',
    category: props.categories[0] || 'Curriculum & Standards',
    description: '',
    target_audience: 'teachers',
    is_published: true,
    document_file: null,
});

// Edit Form
const editForm = useForm({
    title: '',
    category: '',
    description: '',
    target_audience: 'teachers',
    is_published: true,
    document_file: null,
});

const applyFilters = () => {
    router.get(
        route('teacher-documents.index'),
        {
            search: searchQuery.value || undefined,
            category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
            file_type: selectedFileType.value !== 'all' ? selectedFileType.value : undefined,
        },
        { preserveState: true, replace: true }
    );
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = 'all';
    selectedFileType.value = 'all';
    router.get(route('teacher-documents.index'), {}, { preserveState: true, replace: true });
};

const handleFileUpload = (event) => {
    uploadForm.document_file = event.target.files[0] || null;
};

const handleEditFileUpload = (event) => {
    editForm.document_file = event.target.files[0] || null;
};

const submitUpload = () => {
    uploadForm.post(route('teacher-documents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showUploadModal.value = false;
            uploadForm.reset();
        },
    });
};

const openEditModal = (doc) => {
    documentToEdit.value = doc;
    editForm.title = doc.title;
    editForm.category = doc.category;
    editForm.description = doc.description || '';
    editForm.target_audience = doc.target_audience || 'teachers';
    editForm.is_published = !!doc.is_published;
    editForm.document_file = null;
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!documentToEdit.value) return;
    
    // Use POST with _method PUT to support multipart file upload
    editForm.transform((data) => ({
        ...data,
        _method: 'PUT',
    })).post(route('teacher-documents.update', documentToEdit.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            documentToEdit.value = null;
            editForm.reset();
        },
    });
};

const confirmDelete = (doc) => {
    documentToDelete.value = doc;
    showDeleteModal.value = true;
};

const executeDelete = () => {
    if (!documentToDelete.value) return;
    router.delete(route('teacher-documents.destroy', documentToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            documentToDelete.value = null;
        },
    });
};

const getCategoryBadgeClass = (category) => {
    switch (category) {
        case 'Curriculum & Standards':
            return 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800';
        case 'Lesson Planning':
            return 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300 border-teal-200 dark:border-teal-800';
        case 'Policies & Guidelines':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800';
        case 'Teaching Aids & Worksheets':
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800';
        case 'Workshop & Safety':
            return 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800';
        case 'Assessment & Rubrics':
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800';
        default:
            return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700';
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Teacher Documents & PDF Downloads" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-teal-500/10 dark:bg-teal-500/20 rounded-xl text-teal-600 dark:text-teal-400">
                            <FileDown class="w-6 h-6" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                                Teacher Document Repository
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                Official institutional documents, curriculum guidelines, and workshop safety manuals uploaded by admin for teacher download.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="canManage" class="flex items-center space-x-3">
                    <button
                        @click="showUploadModal = true"
                        class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-xl shadow-sm shadow-teal-600/30 transition focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                    >
                        <Plus class="w-4 h-4 mr-2" />
                        Upload Document
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Offline Information Banner & Metrics Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-950/50 flex items-center justify-center text-teal-600 dark:text-teal-400 shrink-0">
                        <BookOpen class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white">
                            {{ stats.total_documents }}
                        </div>
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Available Documents
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/50 flex items-center justify-center text-rose-600 dark:text-rose-400 shrink-0">
                        <FileText class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white">
                            {{ stats.pdf_count }}
                        </div>
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            PDF Documents
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center text-blue-600 dark:text-blue-400 shrink-0">
                        <Download class="w-6 h-6" />
                    </div>
                    <div>
                        <div class="text-2xl font-extrabold text-slate-900 dark:text-white">
                            {{ stats.total_downloads }}
                        </div>
                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400">
                            Total Downloads by Teachers
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-teal-600 to-emerald-700 text-white p-5 rounded-2xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center space-x-2 text-xs font-semibold uppercase tracking-wider text-teal-100">
                            <HardDrive class="w-4 h-4" />
                            <span>Offline Readiness</span>
                        </div>
                        <p class="text-xs text-teal-50 mt-1 leading-relaxed">
                            Click <strong class="text-white">Download PDF</strong> to save official guides directly onto your laptop or desktop system for offline review.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Controls -->
            <div class="bg-white dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <Search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input
                            v-model="searchQuery"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Search documents by title, keyword, or filename..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                        />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select
                            v-model="selectedCategory"
                            @change="applyFilters"
                            class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl py-2.5 px-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                        >
                            <option value="all">All Categories</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">
                                {{ cat }}
                            </option>
                        </select>

                        <select
                            v-model="selectedFileType"
                            @change="applyFilters"
                            class="bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 rounded-xl py-2.5 px-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                        >
                            <option value="all">All File Types</option>
                            <option value="pdf">PDF Documents</option>
                            <option value="doc">Word / Documents</option>
                        </select>

                        <button
                            @click="applyFilters"
                            class="px-4 py-2.5 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white rounded-xl text-sm font-medium transition"
                        >
                            Filter
                        </button>

                        <button
                            v-if="searchQuery || selectedCategory !== 'all' || selectedFileType !== 'all'"
                            @click="resetFilters"
                            class="px-3 py-2.5 text-xs text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 font-medium transition"
                        >
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Category quick pills -->
                <div class="flex items-center space-x-2 overflow-x-auto pb-1 text-xs scrollbar-none">
                    <span class="text-slate-400 font-medium shrink-0 flex items-center space-x-1">
                        <Filter class="w-3.5 h-3.5" />
                        <span>Categories:</span>
                    </span>
                    <button
                        @click="selectedCategory = 'all'; applyFilters()"
                        :class="[
                            'px-3 py-1 rounded-full font-medium transition whitespace-nowrap',
                            selectedCategory === 'all'
                                ? 'bg-teal-600 text-white shadow-xs'
                                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                        ]"
                    >
                        All
                    </button>
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="selectedCategory = cat; applyFilters()"
                        :class="[
                            'px-3 py-1 rounded-full font-medium transition whitespace-nowrap',
                            selectedCategory === cat
                                ? 'bg-teal-600 text-white shadow-xs'
                                : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'
                        ]"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>

            <!-- Documents Grid -->
            <div v-if="documents.data && documents.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="doc in documents.data"
                    :key="doc.id"
                    class="bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between group"
                >
                    <div class="space-y-3">
                        <!-- Top Meta Header -->
                        <div class="flex items-start justify-between gap-2">
                            <span
                                :class="[
                                    'inline-block px-2.5 py-1 rounded-lg text-xs font-semibold border',
                                    getCategoryBadgeClass(doc.category)
                                ]"
                            >
                                {{ doc.category }}
                            </span>

                            <!-- Format badge -->
                            <div class="flex items-center space-x-1.5 shrink-0">
                                <span
                                    v-if="doc.is_pdf"
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30"
                                >
                                    PDF
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 uppercase"
                                >
                                    {{ doc.file_extension }}
                                </span>

                                <span
                                    v-if="canManage && !doc.is_published"
                                    class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-500 uppercase"
                                >
                                    Draft
                                </span>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug group-hover:text-teal-600 dark:group-hover:text-teal-400 transition">
                            {{ doc.title }}
                        </h3>

                        <!-- Description -->
                        <p v-if="doc.description" class="text-xs text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                            {{ doc.description }}
                        </p>

                        <!-- File details strip -->
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/60 flex items-center justify-between text-[11px] text-slate-400">
                            <div class="flex items-center space-x-1.5 truncate max-w-[170px]" :title="doc.file_name">
                                <FileText class="w-3.5 h-3.5 shrink-0 text-slate-400" />
                                <span class="truncate">{{ doc.file_name }}</span>
                            </div>
                            <span class="font-semibold text-slate-600 dark:text-slate-300 shrink-0">
                                {{ doc.formatted_file_size }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span class="flex items-center space-x-1">
                                <Clock class="w-3.5 h-3.5" />
                                <span>{{ formatDate(doc.created_at) }}</span>
                            </span>
                            <span class="flex items-center space-x-1 text-teal-600 dark:text-teal-400 font-medium">
                                <Download class="w-3.5 h-3.5" />
                                <span>{{ doc.download_count }} downloads</span>
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800/80 space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Download PDF to Individual System -->
                            <a
                                :href="route('teacher-documents.download', doc.id)"
                                class="inline-flex items-center justify-center px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs transition space-x-1.5 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                title="Download document file directly to your computer"
                            >
                                <Download class="w-3.5 h-3.5" />
                                <span>Download {{ doc.is_pdf ? 'PDF' : 'File' }}</span>
                            </a>

                            <!-- Preview in Browser -->
                            <a
                                :href="route('teacher-documents.preview', doc.id)"
                                target="_blank"
                                class="inline-flex items-center justify-center px-3 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700/60 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold transition space-x-1.5"
                                title="Preview document in browser tab"
                            >
                                <Eye class="w-3.5 h-3.5" />
                                <span>Preview</span>
                            </a>
                        </div>

                        <!-- Admin Manage Row -->
                        <div v-if="canManage" class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                            <button
                                @click="openEditModal(doc)"
                                class="p-1.5 text-slate-400 hover:text-teal-600 dark:hover:text-teal-400 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700/50 transition"
                                title="Edit Document Details"
                            >
                                <Edit3 class="w-3.5 h-3.5" />
                            </button>
                            <button
                                @click="confirmDelete(doc)"
                                class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                                title="Delete Document"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="bg-white dark:bg-slate-800/80 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center shadow-sm space-y-4"
            >
                <div class="w-16 h-16 rounded-2xl bg-teal-50 dark:bg-teal-950/50 flex items-center justify-center text-teal-600 dark:text-teal-400 mx-auto">
                    <BookOpen class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        No documents found
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mt-1">
                        {{ searchQuery || selectedCategory !== 'all' ? 'Try adjusting your search criteria or resetting the category filter.' : 'There are currently no documents in the repository.' }}
                    </p>
                </div>
                <div v-if="searchQuery || selectedCategory !== 'all' || selectedFileType !== 'all'">
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 rounded-xl text-xs font-semibold transition"
                    >
                        Reset All Filters
                    </button>
                </div>
                <div v-else-if="canManage">
                    <button
                        @click="showUploadModal = true"
                        class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs transition"
                    >
                        Upload First Document
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="documents.links && documents.links.length > 3" class="flex justify-center mt-6">
                <nav class="flex items-center space-x-1">
                    <Link
                        v-for="(link, i) in documents.links"
                        :key="i"
                        :href="link.url || '#'"
                        :class="[
                            'px-3.5 py-1.5 rounded-lg text-xs font-medium transition',
                            link.active
                                ? 'bg-teal-600 text-white font-bold'
                                : link.url
                                ? 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700'
                                : 'text-slate-400 opacity-50 cursor-not-allowed'
                        ]"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- Admin Upload Document Modal -->
        <!-- ========================================================= -->
        <Modal :show="showUploadModal" @close="showUploadModal = false" max-width="2xl">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-teal-500/10 rounded-xl text-teal-600 dark:text-teal-400">
                            <UploadCloud class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Upload Document for Teachers
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                PDF documents, curriculum guidelines, or workshop manuals for teacher download.
                            </p>
                        </div>
                    </div>
                    <button @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitUpload" class="space-y-4">
                    <!-- Title -->
                    <div>
                        <InputLabel for="upload-title" value="Document Title *" />
                        <TextInput
                            id="upload-title"
                            v-model="uploadForm.title"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. IDEAS-TVET Competency-Based Lesson Delivery Framework 2026"
                            required
                        />
                        <InputError :message="uploadForm.errors.title" class="mt-1" />
                    </div>

                    <!-- Category & Audience Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="upload-category" value="Category *" />
                            <select
                                id="upload-category"
                                v-model="uploadForm.category"
                                class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                required
                            >
                                <option v-for="cat in categories" :key="cat" :value="cat">
                                    {{ cat }}
                                </option>
                            </select>
                            <InputError :message="uploadForm.errors.category" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="upload-audience" value="Target Audience *" />
                            <select
                                id="upload-audience"
                                v-model="uploadForm.target_audience"
                                class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                required
                            >
                                <option value="teachers">Technical Teachers (TVET)</option>
                                <option value="all">All Roles (Teachers, Facilitators, Trainees)</option>
                                <option value="facilitators">Facilitators &amp; Lead Instructors Only</option>
                            </select>
                            <InputError :message="uploadForm.errors.target_audience" class="mt-1" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel for="upload-description" value="Document Description" />
                        <textarea
                            id="upload-description"
                            v-model="uploadForm.description"
                            rows="3"
                            class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                            placeholder="Provide a summary of the contents, usage instructions, or relevant trade standards..."
                        ></textarea>
                        <InputError :message="uploadForm.errors.description" class="mt-1" />
                    </div>

                    <!-- File Input -->
                    <div>
                        <InputLabel for="upload-file" value="Select File to Upload * (PDF Recommended, Max 30MB)" />
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100/50 transition">
                            <div class="space-y-1 text-center">
                                <FileText class="mx-auto h-10 w-10 text-teal-500" />
                                <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                    <label
                                        for="document_file"
                                        class="relative cursor-pointer font-semibold text-teal-600 dark:text-teal-400 hover:underline"
                                    >
                                        <span>Click to browse file</span>
                                        <input
                                            id="document_file"
                                            name="document_file"
                                            type="file"
                                            class="sr-only"
                                            accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx,.ppt,.txt,.zip"
                                            @change="handleFileUpload"
                                            required
                                        />
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    PDF, Word DOCX, Excel XLSX, PPTX, or ZIP up to 30MB
                                </p>
                                <p v-if="uploadForm.document_file" class="text-xs font-bold text-teal-600 dark:text-teal-400 pt-2 flex items-center justify-center space-x-1">
                                    <CheckCircle2 class="w-4 h-4" />
                                    <span>Selected: {{ uploadForm.document_file.name }} ({{ (uploadForm.document_file.size / 1024 / 1024).toFixed(2) }} MB)</span>
                                </p>
                            </div>
                        </div>
                        <InputError :message="uploadForm.errors.document_file" class="mt-1" />
                    </div>

                    <!-- Published status -->
                    <div class="flex items-center space-x-2 pt-2">
                        <input
                            id="upload-published"
                            v-model="uploadForm.is_published"
                            type="checkbox"
                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 h-4 w-4"
                        />
                        <label for="upload-published" class="text-xs font-medium text-slate-700 dark:text-slate-300">
                            Publish immediately (visible to teachers for download)
                        </label>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showUploadModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="uploadForm.processing" class="bg-teal-600 hover:bg-teal-700">
                            <span v-if="uploadForm.processing">Uploading...</span>
                            <span v-else>Upload Document</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- ========================================================= -->
        <!-- Admin Edit Document Modal -->
        <!-- ========================================================= -->
        <Modal :show="showEditModal" @close="showEditModal = false" max-width="2xl">
            <div class="p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="p-2 bg-teal-500/10 rounded-xl text-teal-600 dark:text-teal-400">
                            <Edit3 class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                Edit Document Details
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Update title, category, description, or replace document file.
                            </p>
                        </div>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <InputLabel for="edit-title" value="Document Title *" />
                        <TextInput
                            id="edit-title"
                            v-model="editForm.title"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="editForm.errors.title" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="edit-category" value="Category *" />
                            <select
                                id="edit-category"
                                v-model="editForm.category"
                                class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                required
                            >
                                <option v-for="cat in categories" :key="cat" :value="cat">
                                    {{ cat }}
                                </option>
                            </select>
                            <InputError :message="editForm.errors.category" class="mt-1" />
                        </div>

                        <div>
                            <InputLabel for="edit-audience" value="Target Audience *" />
                            <select
                                id="edit-audience"
                                v-model="editForm.target_audience"
                                class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl py-2 px-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                                required
                            >
                                <option value="teachers">Technical Teachers (TVET)</option>
                                <option value="all">All Roles</option>
                                <option value="facilitators">Facilitators Only</option>
                            </select>
                            <InputError :message="editForm.errors.target_audience" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="edit-description" value="Document Description" />
                        <textarea
                            id="edit-description"
                            v-model="editForm.description"
                            rows="3"
                            class="mt-1 block w-full bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl p-3 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-teal-500"
                        ></textarea>
                        <InputError :message="editForm.errors.description" class="mt-1" />
                    </div>

                    <!-- Optional replacement file -->
                    <div>
                        <InputLabel for="edit-replacement-file" value="Replace Document File (Optional)" />
                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400 mb-1">
                            Current file: <strong class="text-slate-800 dark:text-slate-200">{{ documentToEdit?.file_name }}</strong> ({{ documentToEdit?.formatted_file_size }})
                        </div>
                        <input
                            id="edit-replacement-file"
                            type="file"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 dark:file:bg-teal-950/60 dark:file:text-teal-300 hover:file:bg-teal-100"
                            accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx,.ppt,.txt,.zip"
                            @change="handleEditFileUpload"
                        />
                        <InputError :message="editForm.errors.document_file" class="mt-1" />
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input
                            id="edit-published"
                            v-model="editForm.is_published"
                            type="checkbox"
                            class="rounded border-slate-300 text-teal-600 focus:ring-teal-500 h-4 w-4"
                        />
                        <label for="edit-published" class="text-xs font-medium text-slate-700 dark:text-slate-300">
                            Published (visible to teachers)
                        </label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                        <SecondaryButton @click="showEditModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="editForm.processing" class="bg-teal-600 hover:bg-teal-700">
                            <span v-if="editForm.processing">Saving...</span>
                            <span v-else>Save Changes</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- ========================================================= -->
        <!-- Delete Confirmation Modal -->
        <!-- ========================================================= -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="md">
            <div class="p-6 space-y-4">
                <div class="flex items-center space-x-3 text-rose-600">
                    <div class="p-2 bg-rose-100 dark:bg-rose-900/40 rounded-full">
                        <Trash2 class="w-6 h-6" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        Delete Document
                    </h3>
                </div>

                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                    Are you sure you want to delete <strong class="text-slate-900 dark:text-white">{{ documentToDelete?.title }}</strong>? Teachers will no longer be able to download this document.
                </p>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="executeDelete">
                        Delete Document
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

