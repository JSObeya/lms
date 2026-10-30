<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    BookOpen,
    Wand2,
    Award,
    Briefcase,
    Users,
    LogOut,
    User,
    CheckCircle2,
    AlertCircle,
    X,
    BookMarked,
    ShieldCheck,
    Layers,
    Compass,
    Wrench,
    Settings2,
    ChevronDown,
    Sparkles,
    FileDown
} from 'lucide-vue-next';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import PageActivityIndicator from '@/Components/PageActivityIndicator.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();

const user = computed(() => page.props.auth?.user || {});
const roles = computed(() => user.value.roles || []);

const isTeacher = computed(() => roles.value.includes('teacher'));
const isAdmin = computed(() => roles.value.includes('super_admin') || roles.value.includes('lms_admin'));
const isFacilitator = computed(() => roles.value.includes('facilitator'));
const isStudent = computed(() => roles.value.includes('student'));

const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const showFlash = ref(true);
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-100 flex flex-col">
        <PageActivityIndicator />

        <!-- Top Navigation Bar -->
        <nav class="sticky top-0 z-40 bg-slate-900 border-b border-slate-800 text-white shadow-lg shadow-black/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Brand & Primary Nav -->
                    <div class="flex items-center space-x-8">
                        <Link :href="route('dashboard')" class="flex items-center space-x-3 focus:outline-none">
                            <ApplicationLogo />
                        </Link>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex items-center space-x-1">
                            <Link
                                :href="route('dashboard')"
                                :class="[
                                    'px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2',
                                    route().current('dashboard')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <LayoutDashboard class="w-4 h-4" />
                                <span>Dashboard</span>
                            </Link>

                            <Link
                                :href="route('courses.index')"
                                :class="[
                                    'px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2',
                                    route().current('courses.*') && !route().current('courses.builder')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <BookOpen class="w-4 h-4" />
                                <span>Courses</span>
                            </Link>

                            <!-- TVET Pedagogy Studio Menu Dropdown -->
                            <div v-if="isTeacher || isFacilitator || isAdmin" class="relative">
                                <Dropdown align="left" width="64">
                                    <template #trigger>
                                        <button
                                            type="button"
                                            :class="[
                                                'px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-1.5',
                                                route().current('pedagogy.*')
                                                    ? 'bg-rose-900/40 text-rose-300 border border-rose-500/40 shadow-sm shadow-rose-900/20'
                                                    : 'text-rose-200/90 hover:text-white hover:bg-slate-800'
                                            ]"
                                        >
                                            <Sparkles class="w-4 h-4 text-rose-400" />
                                            <span>TVET Pedagogy Studio</span>
                                            <span class="bg-rose-500/20 text-rose-300 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">Days 9-10</span>
                                            <ChevronDown class="w-3.5 h-3.5 ml-0.5 opacity-70" />
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                            IDEAS-TVET Instructional Design
                                        </div>
                                        <DropdownLink :href="route('pedagogy.studio')" class="flex items-center space-x-2">
                                            <Layers class="w-4 h-4 text-rose-500" />
                                            <div>
                                                <div class="font-medium">TVET Pedagogy Studio</div>
                                                <div class="text-[11px] text-slate-400">Progressive Days 8–12 Overview Hub</div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink :href="route('pedagogy.lessons.builder')" class="flex items-center space-x-2">
                                            <Compass class="w-4 h-4 text-amber-500" />
                                            <div>
                                                <div class="font-medium">Competency-Based Lesson Builder</div>
                                                <div class="text-[11px] text-slate-400">Day 9 Practical 4-Step Builder</div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink :href="route('pedagogy.inclusive-designer.index')" class="flex items-center space-x-2">
                                            <Wand2 class="w-4 h-4 text-teal-500" />
                                            <div>
                                                <div class="font-medium">Inclusive Lesson Designer</div>
                                                <div class="text-[11px] text-slate-400">Day 10 UDL, Differentiated &amp; Safe</div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink :href="route('pedagogy.demo-project-planner')" class="flex items-center space-x-2">
                                            <Wrench class="w-4 h-4 text-blue-500" />
                                            <div>
                                                <div class="font-medium">Demonstration and Project Planner</div>
                                                <div class="text-[11px] text-slate-400">PBL &amp; Fault Diagnosis Scenarios</div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink :href="route('pedagogy.workshop-planner')" class="flex items-center space-x-2">
                                            <Settings2 class="w-4 h-4 text-emerald-500" />
                                            <div>
                                                <div class="font-medium">Workshop Management Planner</div>
                                                <div class="text-[11px] text-slate-400">Equipment Rotation &amp; Group Matrix</div>
                                            </div>
                                        </DropdownLink>
                                        <DropdownLink :href="route('portfolio.index')" class="flex items-center space-x-2 border-t border-slate-100 dark:border-slate-800">
                                            <Briefcase class="w-4 h-4 text-purple-500" />
                                            <div>
                                                <div class="font-medium">My Teaching Portfolio</div>
                                                <div class="text-[11px] text-slate-400">Verified Teaching Artifacts &amp; Plans</div>
                                            </div>
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Day 8 Practice Wizard -->
                            <Link
                                v-if="isTeacher || isFacilitator || isAdmin"
                                :href="route('day8.wizard')"
                                :class="[
                                    'px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-1.5',
                                    route().current('day8.*')
                                        ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm shadow-amber-500/10'
                                        : 'text-amber-300/80 hover:text-amber-200 hover:bg-slate-800'
                                ]"
                            >
                                <Wand2 class="w-4 h-4 text-amber-400" />
                                <span>Day 8 Wizard</span>
                            </Link>

                            <!-- Facilitator Reviews Portal -->
                            <Link
                                v-if="isFacilitator || isAdmin"
                                :href="route('facilitator.pedagogy-reviews.index')"
                                :class="[
                                    'px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-1.5',
                                    route().current('facilitator.pedagogy-reviews.*')
                                        ? 'bg-purple-600/20 text-purple-300 border border-purple-500/40'
                                        : 'text-purple-300/80 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <Award class="w-4 h-4 text-purple-400" />
                                <span>Facilitator Reviews</span>
                            </Link>

                            <!-- Rubric Designer -->
                            <Link
                                v-if="isTeacher || isAdmin"
                                :href="route('rubrics.index')"
                                :class="[
                                    'px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-1.5',
                                    route().current('rubrics.*')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <Award class="w-4 h-4" />
                                <span>Rubrics</span>
                            </Link>

                            <!-- My Teaching Portfolio (Direct link) -->
                            <Link
                                v-if="isTeacher"
                                :href="route('portfolio.index')"
                                :class="[
                                    'px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-1.5',
                                    route().current('portfolio.*')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <Briefcase class="w-4 h-4" />
                                <span>My Teaching Portfolio</span>
                            </Link>

                            <!-- Teacher Document Repository (PDF Downloads) -->
                            <Link
                                v-if="isTeacher || isFacilitator || isAdmin"
                                :href="route('teacher-documents.index')"
                                :class="[
                                    'px-3 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-1.5',
                                    route().current('teacher-documents.*')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30 shadow-xs'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <FileDown class="w-4 h-4 text-teal-400" />
                                <span>Documents</span>
                            </Link>

                            <!-- User Administration (Admins) -->
                            <Link
                                v-if="isAdmin"
                                :href="route('admin.users.index')"
                                :class="[
                                    'px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2',
                                    route().current('admin.users.*')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <Users class="w-4 h-4" />
                                <span>Users</span>
                            </Link>

                            <!-- System Audit Trail (Admins) -->
                            <Link
                                v-if="isAdmin"
                                :href="route('admin.audit-logs.index')"
                                :class="[
                                    'px-3.5 py-2 rounded-lg text-sm font-medium transition flex items-center space-x-2',
                                    route().current('admin.audit-logs.*')
                                        ? 'bg-teal-600/20 text-teal-400 border border-teal-500/30'
                                        : 'text-slate-300 hover:text-white hover:bg-slate-800'
                                ]"
                            >
                                <ShieldCheck class="w-4 h-4 text-emerald-400" />
                                <span>System Audit</span>
                            </Link>
                        </div>
                    </div>

                    <!-- User Profile & Action Dropdown -->
                    <div class="hidden md:flex items-center space-x-3">
                        <!-- User Guide Link -->
                        <Link
                            :href="route('user.guide')"
                            class="px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center space-x-1.5 text-xs font-medium border border-slate-700/60 shadow-sm"
                            title="Complete User & Admin Reference Guide"
                        >
                            <BookMarked class="w-4 h-4 text-brand-400" />
                            <span class="hidden lg:inline">User Guide</span>
                        </Link>

                        <!-- Theme Toggle -->
                        <ThemeToggle />

                        <div class="text-right hidden xl:block pl-2 border-l border-slate-800">
                            <div class="text-xs font-semibold text-white leading-tight">
                                {{ user.name }}
                            </div>
                            <div class="text-[11px] text-teal-400 font-medium tracking-wide">
                                {{ user.trade_specialization || 'Technical Instructor' }}
                            </div>
                        </div>

                        <div class="relative">
                            <Dropdown align="right" width="56">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="flex items-center space-x-2 rounded-full ring-2 ring-teal-500/40 p-1 hover:ring-teal-400 transition bg-slate-800"
                                    >
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-teal-600 to-blue-600 text-white font-bold flex items-center justify-center text-xs">
                                            {{ user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                                        </div>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Signed in as</p>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ user.email }}</p>
                                        <span class="inline-block mt-1 text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-300">
                                            {{ roles[0] || 'User' }}
                                        </span>
                                    </div>

                                    <DropdownLink :href="route('profile.edit')" class="flex items-center space-x-2">
                                        <User class="w-4 h-4 text-slate-400" />
                                        <span>Account Profile</span>
                                    </DropdownLink>

                                    <DropdownLink v-if="isTeacher || isFacilitator || isAdmin" :href="route('teacher-documents.index')" class="flex items-center space-x-2">
                                        <FileDown class="w-4 h-4 text-teal-500" />
                                        <span>Teacher Documents</span>
                                    </DropdownLink>

                                    <DropdownLink v-if="isAdmin" :href="route('admin.audit-logs.index')" class="flex items-center space-x-2 text-emerald-600 dark:text-emerald-400">
                                        <ShieldCheck class="w-4 h-4" />
                                        <span>System Audit Logs</span>
                                    </DropdownLink>

                                    <DropdownLink :href="route('user.guide')" class="flex items-center space-x-2">
                                        <BookMarked class="w-4 h-4 text-brand-500" />
                                        <span>User &amp; Admin Guide</span>
                                    </DropdownLink>

                                    <DropdownLink :href="route('logout')" method="post" as="button" class="flex items-center space-x-2 text-rose-600 dark:text-rose-400">
                                        <LogOut class="w-4 h-4" />
                                        <span>Log Out</span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Mobile Menu Hamburger Button -->
                    <div class="flex items-center md:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="md:hidden border-t border-slate-800 bg-slate-900 px-4 pt-2 pb-4 space-y-1">
                <Link
                    :href="route('dashboard')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800"
                >
                    Dashboard
                </Link>
                <Link
                    :href="route('courses.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800"
                >
                    Courses
                </Link>

                <!-- TVET Pedagogy Studio Links (Mobile) -->
                <div v-if="isTeacher || isFacilitator || isAdmin" class="pt-2 pb-1 border-t border-slate-800/80">
                    <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-rose-400 mb-1">
                        TVET Pedagogy &amp; Instructional Design Studio
                    </div>
                    <Link
                        :href="route('pedagogy.studio')"
                        class="block px-3 py-1.5 rounded-md text-sm font-medium text-rose-300 hover:bg-slate-800 flex items-center space-x-2"
                    >
                        <Sparkles class="w-4 h-4 text-rose-400" />
                        <span>TVET Pedagogy Studio</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.lessons.builder')"
                        class="block px-3 py-1.5 rounded-md text-sm font-medium text-amber-300 hover:bg-slate-800 flex items-center space-x-2"
                    >
                        <Compass class="w-4 h-4 text-amber-400" />
                        <span>Competency-Based Lesson Builder</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.inclusive-designer.index')"
                        class="block px-3 py-1.5 rounded-md text-sm font-medium text-teal-300 hover:bg-slate-800 flex items-center space-x-2"
                    >
                        <Wand2 class="w-4 h-4 text-teal-400" />
                        <span>Inclusive Lesson Designer</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.demo-project-planner')"
                        class="block px-3 py-1.5 rounded-md text-sm font-medium text-blue-300 hover:bg-slate-800 flex items-center space-x-2"
                    >
                        <Wrench class="w-4 h-4 text-blue-400" />
                        <span>Demonstration and Project Planner</span>
                    </Link>
                    <Link
                        :href="route('pedagogy.workshop-planner')"
                        class="block px-3 py-1.5 rounded-md text-sm font-medium text-emerald-300 hover:bg-slate-800 flex items-center space-x-2"
                    >
                        <Settings2 class="w-4 h-4 text-emerald-400" />
                        <span>Workshop Management Planner</span>
                    </Link>
                </div>

                <Link
                    v-if="isTeacher || isFacilitator || isAdmin"
                    :href="route('day8.wizard')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-amber-300 hover:bg-slate-800"
                >
                    Day 8 Practical Wizard (TVET)
                </Link>

                <Link
                    v-if="isFacilitator || isAdmin"
                    :href="route('facilitator.pedagogy-reviews.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-purple-300 hover:bg-slate-800 flex items-center space-x-2"
                >
                    <Award class="w-4 h-4 text-purple-400" />
                    <span>Facilitator Reviews Portal</span>
                </Link>

                <Link
                    v-if="isTeacher || isAdmin"
                    :href="route('rubrics.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800"
                >
                    Rubrics
                </Link>
                <Link
                    v-if="isTeacher"
                    :href="route('portfolio.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 flex items-center space-x-2"
                >
                    <Briefcase class="w-4 h-4 text-teal-400" />
                    <span>My Teaching Portfolio</span>
                </Link>
                <Link
                    v-if="isTeacher || isFacilitator || isAdmin"
                    :href="route('teacher-documents.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 flex items-center space-x-2"
                >
                    <FileDown class="w-4 h-4 text-teal-400" />
                    <span>Teacher Documents &amp; PDFs</span>
                </Link>
                <Link
                    v-if="isAdmin"
                    :href="route('admin.users.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800"
                >
                    Users &amp; 200 Teachers Management
                </Link>
                <Link
                    v-if="isAdmin"
                    :href="route('admin.audit-logs.index')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-emerald-400 hover:bg-slate-800 flex items-center space-x-2"
                >
                    <ShieldCheck class="w-4 h-4 text-emerald-400" />
                    <span>System Audit Trail</span>
                </Link>

                <Link
                    :href="route('user.guide')"
                    class="block px-3 py-2 rounded-md text-base font-medium text-brand-300 hover:bg-slate-800 flex items-center space-x-2"
                >
                    <BookMarked class="w-4 h-4 text-brand-400" />
                    <span>User &amp; Admin Reference Guide</span>
                </Link>

                <div class="pt-4 border-t border-slate-800">
                    <div class="px-3 py-2 flex items-center justify-between">
                        <div>
                            <div class="font-medium text-white">{{ user.name }}</div>
                            <div class="text-xs text-slate-400">{{ user.email }}</div>
                        </div>
                        <ThemeToggle />
                    </div>
                    <Link
                        :href="route('profile.edit')"
                        class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800"
                    >
                        Profile
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-rose-400 hover:bg-slate-800"
                    >
                        Log Out
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Flash Notification Toast -->
        <div v-if="showFlash && (flashSuccess || flashError)" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div
                v-if="flashSuccess"
                class="flex items-center justify-between p-4 rounded-xl bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-200 shadow-sm"
            >
                <div class="flex items-center space-x-3">
                    <CheckCircle2 class="w-5 h-5 text-teal-600 dark:text-teal-400 shrink-0" />
                    <p class="text-sm font-medium">{{ flashSuccess }}</p>
                </div>
                <button @click="showFlash = false" class="text-teal-600 dark:text-teal-400 hover:opacity-75">
                    <X class="w-4 h-4" />
                </button>
            </div>

            <div
                v-if="flashError"
                class="flex items-center justify-between p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 shadow-sm"
            >
                <div class="flex items-center space-x-3">
                    <AlertCircle class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" />
                    <p class="text-sm font-medium">{{ flashError }}</p>
                </div>
                <button @click="showFlash = false" class="text-rose-600 dark:text-rose-400 hover:opacity-75">
                    <X class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Optional Page Header Slot -->
        <header v-if="$slots.header" class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; 2026 Greg &amp; Co. ICT Ltd, Makurdi, Benue State, Nigeria. All rights reserved.</p>
                <p class="font-medium text-teal-600 dark:text-teal-400">IDEAS–TVET Technical &amp; Vocational Learning Management System</p>
            </div>
        </footer>
    </div>
</template>
