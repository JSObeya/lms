<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
</script>

<template>
    <div
        class="min-h-screen flex flex-col justify-between bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-200 px-4 py-6"
    >
        <!-- Top Utility Bar -->
        <div class="max-w-md w-full mx-auto flex items-center justify-between">
            <Link
                href="/"
                class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-brand-700 dark:text-slate-400 dark:hover:text-rose-400 transition-colors group"
            >
                <ArrowLeft class="w-4 h-4 mr-1.5 transition-transform group-hover:-translate-x-0.5" />
                Back to Home
            </Link>
            <div class="flex items-center space-x-2.5">
                <Link
                    v-if="!route().current('register')"
                    :href="route('register')"
                    class="text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-brand-700 dark:hover:text-brand-400 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:border-brand-600 transition"
                >
                    Register
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-brand-700 dark:hover:text-brand-400 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 hover:border-brand-600 transition"
                >
                    Sign In
                </Link>
                <ThemeToggle />
            </div>
        </div>

        <!-- Center Login/Auth Card -->
        <div class="w-full max-w-md mx-auto my-auto py-8">
            <div class="flex flex-col items-center mb-6">
                <Link href="/" class="focus:outline-none focus:ring-2 focus:ring-brand-700/40 rounded-2xl p-1">
                    <ApplicationLogo size="lg" />
                </Link>
            </div>

            <div
                class="w-full overflow-hidden bg-white dark:bg-slate-900/90 border border-slate-200/90 dark:border-slate-800 shadow-xl shadow-slate-900/5 dark:shadow-black/40 rounded-3xl p-6 sm:p-8 backdrop-blur-sm"
            >
                <slot />
            </div>
        </div>

        <!-- Footer Note -->
        <footer class="w-full max-w-md mx-auto text-center text-xs text-slate-400 dark:text-slate-600">
            &copy; {{ new Date().getFullYear() }} Greg &amp; Co. ICT Limited, Makurdi, Benue State.
        </footer>
    </div>
</template>
