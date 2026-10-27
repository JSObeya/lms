<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
    BookOpen,
    Eye,
    EyeOff,
    Sun,
    Moon,
    LayoutDashboard,
    FileCheck2,
    Award,
    HelpCircle,
    Search,
    CheckCircle2,
    Wand2,
    ArrowRight,
    ShieldCheck,
    Download,
    ExternalLink,
    ChevronRight,
    Briefcase,
    Sparkles,
    User
} from 'lucide-vue-next';

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const searchQuery = ref('');
const activeTab = ref('getting-started');

// Interactive demo state for trainees to test right in the guide!
const demoPassword = ref('TraineeSecret2026!');

const sections = [
    {
        id: 'getting-started',
        name: '1. Login & Show/Hide Password',
        icon: ShieldCheck,
        badge: 'Essential',
        title: 'Accessing Your Account & Securing Password',
        summary: 'Learn how to sign in, use the new password visibility eye toggle, and prevent login errors.',
        content: [
            {
                step: 'Step 1: Open the Portal',
                text: 'Navigate to https://lms.gregicthub.com in any modern browser on your phone, tablet, or laptop computer.'
            },
            {
                step: 'Step 2: Enter Your Credentials',
                text: 'Type your official institutional email address provided by Greg & Co. ICT or your TVET training institution.'
            },
            {
                step: 'Step 3: Use the Show / Hide Password Toggle',
                text: 'Click or tap the Eye icon (👁️) inside the password box to reveal the characters you have entered. This helps verify complex passwords and symbols before clicking Submit.'
            },
            {
                step: 'Step 4: Privacy & Workshop Security',
                text: 'Always tap the Eye Slash icon (👁️🗨️) to re-mask your password when working near classmates or in public computer laboratories.'
            }
        ]
    },
    {
        id: 'themes',
        name: '2. Light & Dark Themes',
        icon: Sun,
        badge: 'UI Feature',
        title: 'Switching Between Light and Dark Mode',
        summary: 'Adjust the display mode according to your environment—bright daylight in the workshop or low-light evening study.',
        content: [
            {
                step: 'Step 1: Find the Theme Toggle',
                text: 'Look for the Sun ☀️ or Moon 🌙 icon in the top navigation bar (desktop) or inside the mobile menu drawer (smartphones).'
            },
            {
                step: 'Step 2: Instant Theme Switching',
                text: 'Click the button once to instantly shift from daytime Light Mode to deep charcoal Dark Mode (#020617) designed for reduced eye strain.'
            },
            {
                step: 'Step 3: Automatic Persistence',
                text: 'Your choice is saved in your browser storage automatically. You will never need to readjust it upon returning.'
            }
        ]
    },
    {
        id: 'dashboard-courses',
        name: '3. Dashboard & Courses',
        icon: LayoutDashboard,
        badge: 'Daily Use',
        title: 'Navigating Your Courses & Learning Objectives',
        summary: 'Review your enrolled vocational trades, modular syllabi, and Bloom\'s taxonomy learning outcomes.',
        content: [
            {
                step: 'Step 1: Your Trainee Dashboard',
                text: 'After signing in, your Dashboard displays your active trade courses, upcoming practical assignment deadlines, and performance analytics.'
            },
            {
                step: 'Step 2: Exploring Course Modules',
                text: 'Click into any course (e.g., Solar Installation, Electrical Maintenance, Welding) to see the sequential modules from safety to practical execution.'
            },
            {
                step: 'Step 3: Checking Learning Objectives',
                text: 'Each module clearly states measurable competencies (Remembering, Applying, Analyzing, Creating) so you know exactly what workshop tasks will be evaluated.'
            },
            {
                step: 'Step 4: Downloading Schematics & Manuals',
                text: 'Access the Resources section inside any lesson to download authorized wiring schematics, technical data sheets, and equipment specifications.'
            }
        ]
    },
    {
        id: 'assignments',
        name: '4. Submitting Assignments',
        icon: FileCheck2,
        badge: 'Hands-on',
        title: 'Uploading Workshop Proof & Written Tasks',
        summary: 'Step-by-step guidance on uploading photo evidence of technical projects, technical documents, and reflections.',
        content: [
            {
                step: 'Step 1: Review Assignment Brief & Due Date',
                text: 'Open the assignment from your course or dashboard. Note the submission deadline and read the specific instructions given by your instructor.'
            },
            {
                step: 'Step 2: Consult the Attached Rubric',
                text: 'Review the grading criteria before beginning your workshop practical to understand how each aspect (Safety, Precision, Cleanliness) is scored.'
            },
            {
                step: 'Step 3: Attach Practical Evidence',
                text: 'You can type written descriptions and upload files: photos of completed circuits or welds, PDF workshop reports, or ZIP archives (up to 25 MB).'
            },
            {
                step: 'Step 4: Confirm Submission',
                text: 'Click "Submit Practical Assignment". You will receive an immediate confirmation with a verified timestamp.'
            }
        ]
    },
    {
        id: 'rubrics-grades',
        name: '5. Rubrics & Competency Grades',
        icon: Award,
        badge: 'Assessment',
        title: 'Understanding Multi-Criteria Rubric Scoring',
        summary: 'How teachers evaluate your vocational skills and how you can track your scores and feedback.',
        content: [
            {
                step: 'Step 1: Transparent Multi-Level Scoring',
                text: 'Unlike standard tests, vocational skills are evaluated across multiple criteria: Workshop Safety & PPE, Dimensional Precision, Operational Testing, and Final Finish.'
            },
            {
                step: 'Step 2: Qualitative Instructor Feedback',
                text: 'Read detailed notes left by your instructor highlighting what you did right and actionable tips for technical improvement.'
            },
            {
                step: 'Step 3: Grade Release & Record of Competency',
                text: 'Once grades are verified and released by your instructor, your gradebook updates automatically and contributes to your certification standing.'
            }
        ]
    },
    {
        id: 'day8',
        name: '6. Day 8 TVET Wizard',
        icon: Wand2,
        badge: 'Capstone',
        title: 'Mastering the Day 8 Intensive Practical Wizard',
        summary: 'Complete guided vocational capstone assessments with facilitator verification.',
        content: [
            {
                step: 'Step 1: Accessing the Wizard',
                text: 'Click "Day 8 Practical Wizard" in the top navigation bar to launch the specialized vocational practice engine.'
            },
            {
                step: 'Step 2: Pre-Task Safety & Station Setup',
                text: 'Follow the digital checklist for personal protective equipment, tool inspection, and workspace hazard mitigation.'
            },
            {
                step: 'Step 3: Facilitator Sign-Off',
                text: 'Demonstrate your completed vocational task to the visiting facilitator. The facilitator will verify your milestone directly on the portal.'
            }
        ]
    },
    {
        id: 'faq',
        name: '7. Troubleshooting & FAQ',
        icon: HelpCircle,
        badge: 'Help',
        title: 'Answers to Common Questions & Support',
        summary: 'Solutions for login troubles, upload limits, forgotten passwords, and contact channels.',
        content: [
            {
                step: 'Q: What if I forget my password?',
                text: 'Click "Forgot your password?" on the login page or ask your center administrator to trigger a password reset link to your email.'
            },
            {
                step: 'Q: What file formats are accepted for submissions?',
                text: 'You can upload images (.jpg, .png), documents (.pdf, .docx, .xlsx), and compressed archives (.zip) up to 25 MB.'
            },
            {
                step: 'Q: Can I use this on my mobile phone?',
                text: 'Yes! Greg ICT Hub LMS is engineered for mobile smartphones, tablets, and desktop workstations with equal responsiveness.'
            },
            {
                step: 'Q: How do I contact technical support?',
                text: 'Contact Greg & Co. ICT Ltd support at support@gregicthub.com or call during business hours in Makurdi, Benue State.'
            }
        ]
    }
];

const filteredSections = computed(() => {
    if (!searchQuery.value.trim()) return sections;
    const q = searchQuery.value.toLowerCase();
    return sections.filter(sec => 
        sec.name.toLowerCase().includes(q) ||
        sec.title.toLowerCase().includes(q) ||
        sec.summary.toLowerCase().includes(q) ||
        sec.content.some(c => c.step.toLowerCase().includes(q) || c.text.toLowerCase().includes(q))
    );
});

const currentSection = computed(() => {
    return sections.find(s => s.id === activeTab.value) || sections[0];
});
</script>

<template>
    <Head title="Trainee User Manual &amp; Practical Guide — Greg ICT Hub LMS" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans text-slate-800 dark:text-slate-100 flex flex-col transition-colors duration-200">
        <!-- Standalone Header for Trainee Guide -->
        <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('home')" class="focus:outline-none">
                        <ApplicationLogo />
                    </Link>
                    <span class="hidden sm:inline-block text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200 dark:border-brand-800">
                        Trainee Manual
                    </span>
                </div>

                <div class="flex items-center space-x-3">
                    <ThemeToggle />

                    <template v-if="isAuthenticated">
                        <Link
                            :href="route('dashboard')"
                            class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs transition flex items-center space-x-1.5 shadow-sm"
                        >
                            <LayoutDashboard class="w-3.5 h-3.5" />
                            <span>My Dashboard</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white font-semibold text-xs transition flex items-center space-x-1.5 shadow-sm"
                        >
                            <span>Sign In</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="bg-gradient-to-b from-brand-900 to-slate-900 text-white py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
            <div class="max-w-7xl mx-auto relative z-10 text-center">
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-brand-800/60 border border-brand-700/60 text-brand-200 text-xs font-semibold mb-4">
                    <Sparkles class="w-3.5 h-3.5 text-brand-300" />
                    <span>Greg &amp; Co. ICT Limited &bull; IDEAS–TVET Practical System</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight">
                    Trainee User Manual &amp; Practical Guide
                </h1>
                <p class="mt-4 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                    A comprehensive, step-by-step handbook to guide you through login, password security, light/dark themes, vocational course study, and rubric evaluations.
                </p>

                <!-- Search Input in Hero -->
                <div class="mt-8 max-w-xl mx-auto relative">
                    <Search class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search manual (e.g. password, theme, upload, rubric)..."
                        class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-slate-800/90 border border-slate-700 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 shadow-xl text-sm"
                    />
                </div>
            </div>
        </section>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Sidebar Navigation -->
                <aside class="lg:col-span-4 space-y-2">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm sticky top-24">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-3">
                            Guide Chapters
                        </h2>

                        <nav class="space-y-1">
                            <button
                                v-for="section in filteredSections"
                                :key="section.id"
                                @click="activeTab = section.id"
                                :class="[
                                    'w-full text-left px-3.5 py-3 rounded-xl text-sm font-semibold transition flex items-center justify-between',
                                    activeTab === section.id
                                        ? 'bg-brand-50 dark:bg-brand-950/60 text-brand-800 dark:text-brand-300 border border-brand-200 dark:border-brand-800 shadow-sm'
                                        : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white'
                                ]"
                            >
                                <div class="flex items-center space-x-3 truncate">
                                    <component :is="section.icon" class="w-4 h-4 shrink-0" :class="activeTab === section.id ? 'text-brand-700 dark:text-brand-400' : 'text-slate-400'" />
                                    <span class="truncate">{{ section.name }}</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 shrink-0">
                                    {{ section.badge }}
                                </span>
                            </button>
                        </nav>

                        <!-- Interactive Feature Test Widgets -->
                        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-800">
                            <h3 class="text-xs font-bold text-slate-900 dark:text-white mb-2 flex items-center space-x-1.5">
                                <Sparkles class="w-3.5 h-3.5 text-brand-600 dark:text-brand-400" />
                                <span>Try Interactive Widgets</span>
                            </h3>

                            <!-- Password Toggle Demo Card -->
                            <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 space-y-2">
                                <label class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                    Test Show / Hide Password:
                                </label>
                                <PasswordInput
                                    v-model="demoPassword"
                                    placeholder="Type anything..."
                                    class="text-xs py-2"
                                />
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                    Click the eye icon to see characters toggle between masked and visible.
                                </p>
                            </div>

                            <!-- Theme Toggle Demo Card -->
                            <div class="mt-3 p-3 rounded-xl bg-slate-100 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-200">Light / Dark Theme</p>
                                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Switch current mode</p>
                                </div>
                                <ThemeToggle />
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Chapter Details View -->
                <section class="lg:col-span-8">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 shadow-sm">
                        <!-- Chapter Header -->
                        <div class="border-b border-slate-200 dark:border-slate-800 pb-6 mb-6">
                            <div class="flex items-center space-x-2 text-brand-700 dark:text-brand-400 text-xs font-bold uppercase tracking-wider mb-2">
                                <component :is="currentSection.icon" class="w-4 h-4" />
                                <span>Chapter Reference: {{ currentSection.badge }}</span>
                            </div>
                            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
                                {{ currentSection.title }}
                            </h2>
                            <p class="mt-2 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                {{ currentSection.summary }}
                            </p>
                        </div>

                        <!-- Step-by-Step Cards -->
                        <div class="space-y-4">
                            <div
                                v-for="(item, idx) in currentSection.content"
                                :key="idx"
                                class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-700/60 transition hover:border-brand-300 dark:hover:border-brand-700"
                            >
                                <div class="flex items-start space-x-3.5">
                                    <div class="w-7 h-7 rounded-full bg-brand-100 dark:bg-brand-900/60 text-brand-800 dark:text-brand-300 font-black text-xs flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                                        {{ idx + 1 }}
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                            {{ item.step }}
                                        </h3>
                                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                            {{ item.text }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Callout Box -->
                        <div class="mt-8 p-6 rounded-2xl bg-gradient-to-r from-brand-900 to-slate-900 text-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-base">Ready to Put This into Practice?</h4>
                                <p class="text-xs text-slate-300 mt-1">
                                    Log in to your account and explore your assigned vocational course syllabi.
                                </p>
                            </div>
                            <Link
                                :href="isAuthenticated ? route('dashboard') : route('login')"
                                class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-md transition flex items-center space-x-1.5 shrink-0"
                            >
                                <span>{{ isAuthenticated ? 'Go to Dashboard' : 'Sign in to LMS' }}</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; 2026 Greg &amp; Co. ICT Ltd, Makurdi, Benue State, Nigeria. All rights reserved.</p>
                <p class="font-medium text-brand-700 dark:text-brand-400">IDEAS–TVET Technical &amp; Vocational Learning Management System</p>
            </div>
        </footer>
    </div>
</template>
