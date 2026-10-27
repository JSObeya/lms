<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {
    BookOpen,
    Wand2,
    Users,
    Award,
    ShieldCheck,
    ArrowRight,
    Sparkles,
    Sun,
    Zap,
    Flame,
    Sprout,
    Utensils,
    Hammer,
    Wrench,
    Scissors,
    Layers,
    CheckCircle2
} from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    featured_courses: Array,
    categories: Array,
});

const getTradeIcon = (iconName) => {
    switch (iconName) {
        case 'Sun': return Sun;
        case 'Zap': return Zap;
        case 'Flame': return Flame;
        case 'Sprout': return Sprout;
        case 'Utensils': return Utensils;
        case 'Hammer': return Hammer;
        case 'Wrench': return Wrench;
        case 'Scissors': return Scissors;
        default: return Layers;
    }
};
</script>

<template>
    <Head title="Greg ICT Hub LMS — Technical &amp; Vocational Education" />

    <div class="min-h-screen bg-slate-950 font-sans text-slate-100 flex flex-col selection:bg-teal-500 selection:text-white">
        <!-- Modern Header -->
        <header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <Link :href="route('home')" class="focus:outline-none">
                    <ApplicationLogo />
                </Link>

                <nav class="flex items-center space-x-4">
                    <template v-if="$page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-500 text-white font-bold text-sm shadow-lg shadow-teal-500/20 transition flex items-center space-x-1.5"
                        >
                            <span>Enter Dashboard</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition"
                        >
                            Sign In
                        </Link>
                        <Link
                            :href="route('login')"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-500 to-blue-600 hover:from-teal-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-teal-500/20 transition flex items-center space-x-1.5"
                        >
                            <span>Teacher Studio Login</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section with Glowing Gradients -->
        <section class="relative pt-20 pb-28 px-4 sm:px-6 lg:px-8 overflow-hidden">
            <!-- Glow background spheres -->
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-gradient-to-r from-teal-500/20 via-blue-600/20 to-purple-600/10 blur-[120px] rounded-full pointer-events-none"></div>

            <div class="max-w-5xl mx-auto text-center relative z-10">
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-teal-500/10 border border-teal-500/30 text-teal-300 text-xs font-bold uppercase tracking-wider mb-6">
                    <Sparkles class="w-3.5 h-3.5 text-teal-400" />
                    <span>IDEAS–TVET Technical &amp; Vocational Training Hub</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-black text-white tracking-tight leading-[1.1]">
                    Next-Generation LMS for
                    <span class="bg-gradient-to-r from-teal-400 via-cyan-400 to-blue-500 bg-clip-text text-transparent">
                        Technical &amp; Vocational
                    </span>
                    Excellence
                </h1>

                <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-3xl mx-auto leading-relaxed">
                    Designed by <strong>Greg &amp; Co. ICT Ltd, Makurdi, Benue State</strong> for 200 IDEAS-TVET teachers and institutions. Empowering technical instructors to create independent courses, formulate measurable learning objectives, design rubric assessments, and export verified grade reports.
                </p>

                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link
                        :href="route('login')"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-base shadow-xl shadow-teal-500/20 transition flex items-center justify-center space-x-2"
                    >
                        <span>Access Teacher Workspace</span>
                        <ArrowRight class="w-5 h-5" />
                    </Link>

                    <a
                        href="#trades"
                        class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-slate-900/80 hover:bg-slate-800 text-slate-200 border border-slate-700/80 font-bold text-base transition flex items-center justify-center space-x-2"
                    >
                        <span>Explore 8 TVET Trades</span>
                    </a>
                </div>

                <!-- Dual Architecture Highlights -->
                <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-3xl mx-auto text-left">
                    <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm">
                        <div class="text-amber-400 font-bold text-xs uppercase tracking-wider mb-1">Mode A · Teacher Training</div>
                        <h2 class="text-sm font-bold text-white">Isolated Sandbox Studios</h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Individual sandboxes for 200 TVET teachers with synthetic practice learner generation and guided Day 8 competency wizard.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-sm">
                        <div class="text-blue-400 font-bold text-xs uppercase tracking-wider mb-1">Mode B · Institutional LMS</div>
                        <h2 class="text-sm font-bold text-white">Accredited Trade Delivery</h2>
                        <p class="text-xs text-slate-400 mt-1">
                            Real student enrollments, practical observation checklists, safety critical reviews, and verifiable CSV grade reports.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8 Priority TVET Trades Section -->
        <section id="trades" class="py-20 bg-slate-900/40 border-y border-slate-800/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-xs font-bold text-teal-400 uppercase tracking-widest mb-2">Technical Specializations</h2>
                    <h3 class="text-3xl font-black text-white">8 Priority TVET Trade Categories</h3>
                    <p class="text-xs text-slate-400 mt-2">
                        Comprehensive curriculum frameworks tailored for vocational craft centers and technical colleges.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div
                        v-for="cat in categories?.filter(c => c.is_tvet_trade)"
                        :key="cat.id"
                        class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-teal-500/40 hover:shadow-lg hover:shadow-teal-500/5 transition group"
                    >
                        <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                            <component :is="getTradeIcon(cat.icon)" class="w-6 h-6" />
                        </div>
                        <h4 class="font-bold text-base text-white group-hover:text-teal-400 transition">
                            {{ cat.name }}
                        </h4>
                        <p class="text-xs text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                            {{ cat.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Primary Demonstration Course Showcase -->
        <section class="py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-tr from-slate-900 via-blue-950 to-slate-900 border border-teal-500/20 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-8">
                    <div class="max-w-2xl">
                        <span class="px-3 py-1 rounded-full bg-teal-400/10 text-teal-300 text-xs font-bold uppercase tracking-wider border border-teal-400/20">
                            Primary Demonstration Benchmark
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-white mt-3">
                            Solar PV Installation — Practical Training
                        </h2>
                        <p class="text-sm text-slate-300 mt-3 leading-relaxed">
                            Experience the full end-to-end LMS workflow: from ABCD measurable learning objectives and technical manual downloads, through multi-criteria rubric evaluation (Component ID, Safety, Checklist) and CSV gradebook generation.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-4 text-xs text-slate-300">
                            <span class="flex items-center space-x-1">
                                <CheckCircle2 class="w-4 h-4 text-teal-400" />
                                <span>Rubric: 100 Marks (5 Criteria)</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <CheckCircle2 class="w-4 h-4 text-teal-400" />
                                <span>Practice Grade: 78/100</span>
                            </span>
                            <span class="flex items-center space-x-1">
                                <CheckCircle2 class="w-4 h-4 text-teal-400" />
                                <span>Hostinger Ready</span>
                            </span>
                        </div>
                    </div>

                    <Link
                        :href="route('login')"
                        class="px-8 py-4 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-sm shadow-xl transition shrink-0"
                    >
                        Sign In to Inspect &rarr;
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-auto border-t border-slate-900 bg-slate-950 py-10 text-xs text-slate-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 text-white font-bold flex items-center justify-center text-sm">G</div>
                    <div>
                        <div class="font-bold text-slate-200">Greg &amp; Co. ICT Ltd</div>
                        <div>Makurdi, Benue State, Nigeria</div>
                    </div>
                </div>

                <div class="text-center md:text-right">
                    <p>Production domain: <span class="text-teal-400">https://lms.gregicthub.com</span></p>
                    <p class="mt-1">In compliance with Nigeria Data Protection Act (NDPA 2023).</p>
                </div>
            </div>
        </footer>
    </div>
</template>
