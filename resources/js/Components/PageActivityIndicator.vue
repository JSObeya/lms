<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';

const isBusy = ref(false);
const showOverlay = ref(false);
let activeCount = 0;
let overlayTimer = null;
let minDisplayTimer = null;
let cleanupFns = [];

const handleStart = () => {
    activeCount++;
    isBusy.value = true;

    // Show center overlay only for requests taking longer than 220ms
    if (!overlayTimer) {
        overlayTimer = setTimeout(() => {
            if (activeCount > 0) {
                showOverlay.value = true;
            }
        }, 220);
    }
};

const handleFinish = () => {
    activeCount = Math.max(0, activeCount - 1);
    if (activeCount === 0) {
        if (overlayTimer) {
            clearTimeout(overlayTimer);
            overlayTimer = null;
        }

        // Guarantee a minimum display duration of 250ms so user can see activity
        if (!minDisplayTimer) {
            minDisplayTimer = setTimeout(() => {
                if (activeCount === 0) {
                    isBusy.value = false;
                    showOverlay.value = false;
                }
                minDisplayTimer = null;
            }, 250);
        }
    }
};

onMounted(() => {
    const unregisterStart = router.on('start', handleStart);
    const unregisterFinish = router.on('finish', handleFinish);
    const unregisterCancel = router.on('cancel', handleFinish);
    const unregisterError = router.on('error', handleFinish);

    cleanupFns = [unregisterStart, unregisterFinish, unregisterCancel, unregisterError];
});

onUnmounted(() => {
    cleanupFns.forEach((fn) => typeof fn === 'function' && fn());
    if (overlayTimer) clearTimeout(overlayTimer);
    if (minDisplayTimer) clearTimeout(minDisplayTimer);
});
</script>

<template>
    <div>
        <!-- 1. Top Screen Glowing Progress Bar -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-x-0"
            enter-to-class="opacity-100 scale-x-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isBusy"
                class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-600 via-brand-500 to-amber-400 z-[10000] pointer-events-none origin-left shadow-sm shadow-rose-500/50"
            >
                <div class="w-full h-full animate-pulse bg-white/30"></div>
            </div>
        </Transition>

        <!-- 2. Floating Rolling Spinner Badge in Top-Right Corner -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-[-12px] scale-90"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-[-12px] scale-90"
        >
            <div
                v-if="isBusy"
                class="fixed top-3 right-4 sm:right-6 z-[9999] pointer-events-none select-none"
                aria-live="polite"
                role="status"
            >
                <div
                    class="flex items-center space-x-2.5 px-3.5 py-2 rounded-full bg-slate-900/95 dark:bg-slate-800/95 text-white shadow-2xl shadow-black/40 border border-rose-500/40 backdrop-blur-md"
                >
                    <!-- Rolling Animated Spinner Icon -->
                    <div class="relative flex items-center justify-center">
                        <Loader2 class="w-4 h-4 text-rose-400 animate-spin" />
                        <span class="absolute w-2 h-2 rounded-full bg-rose-500/40 animate-ping"></span>
                    </div>

                    <span class="text-xs font-semibold tracking-wide text-slate-100">
                        Loading...
                    </span>

                    <!-- Pulsing activity dot -->
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
            </div>
        </Transition>

        <!-- 3. Centered Translucent Overlay Spinner for Heavy Page Loads (>220ms) -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showOverlay"
                class="fixed inset-0 z-[9998] flex items-center justify-center bg-slate-950/25 dark:bg-slate-950/40 backdrop-blur-[2px] pointer-events-none"
            >
                <div
                    class="bg-slate-900/90 dark:bg-slate-800/95 border border-rose-500/30 rounded-2xl px-6 py-5 shadow-2xl flex flex-col items-center space-y-3 text-white max-w-xs mx-auto"
                >
                    <div class="relative flex items-center justify-center">
                        <div class="w-10 h-10 rounded-full border-3 border-rose-500/20 border-t-rose-500 animate-spin"></div>
                        <Loader2 class="absolute w-5 h-5 text-rose-400 animate-spin" />
                    </div>
                    <div class="text-sm font-semibold tracking-wide text-slate-100">
                        Loading page...
                    </div>
                    <div class="text-[11px] text-slate-400">
                        Updating content
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
