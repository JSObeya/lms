<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';

const isBusy = ref(false);
const activeRequests = ref(0);
let cleanupFns = [];

onMounted(() => {
    const unregisterStart = router.on('start', () => {
        activeRequests.value++;
        isBusy.value = true;
    });

    const unregisterFinish = router.on('finish', () => {
        activeRequests.value = Math.max(0, activeRequests.value - 1);
        if (activeRequests.value === 0) {
            isBusy.value = false;
        }
    });

    const unregisterCancel = router.on('cancel', () => {
        activeRequests.value = Math.max(0, activeRequests.value - 1);
        if (activeRequests.value === 0) {
            isBusy.value = false;
        }
    });

    const unregisterError = router.on('error', () => {
        activeRequests.value = Math.max(0, activeRequests.value - 1);
        if (activeRequests.value === 0) {
            isBusy.value = false;
        }
    });

    cleanupFns = [unregisterStart, unregisterFinish, unregisterCancel, unregisterError];
});

onUnmounted(() => {
    cleanupFns.forEach((fn) => typeof fn === 'function' && fn());
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 translate-y-[-10px] scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-[-10px] scale-95"
    >
        <div
            v-if="isBusy"
            class="fixed top-3 right-4 sm:right-6 z-[9999] pointer-events-none select-none"
            aria-live="polite"
            role="status"
        >
            <div
                class="flex items-center space-x-2.5 px-3.5 py-2 rounded-full bg-slate-900/90 dark:bg-slate-800/95 text-white shadow-xl shadow-brand-950/20 border border-brand-500/40 backdrop-blur-md"
            >
                <!-- Rolling Animated Spinner Icon -->
                <div class="relative flex items-center justify-center">
                    <Loader2 class="w-4 h-4 text-rose-400 animate-spin" />
                    <span class="absolute w-2 h-2 rounded-full bg-rose-500/30 animate-ping"></span>
                </div>

                <span class="text-xs font-semibold tracking-wide text-slate-100">
                    Loading...
                </span>

                <!-- Pulsing activity dot -->
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            </div>
        </div>
    </Transition>
</template>
