<script setup>
defineProps({
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg', 'xl'
    },
    showText: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="flex items-center space-x-3 select-none">
        <!-- Logo Image Container -->
        <div
            :class="[
                'relative flex items-center justify-center rounded-xl transition-all duration-200 p-1',
                size === 'sm' ? 'w-9 h-9' : '',
                size === 'md' ? 'w-11 h-11' : '',
                size === 'lg' ? 'w-14 h-14' : '',
                size === 'xl' ? 'w-20 h-20' : '',
                'bg-white/95 dark:bg-white/95 shadow-sm border border-slate-200/80 dark:border-rose-900/40 ring-1 ring-black/5'
            ]"
        >
            <img
                src="/images/logo.png"
                alt="Greg & Co. ICT Limited Logo"
                class="w-full h-full object-contain"
            />
        </div>

        <!-- Typography Badge -->
        <div v-if="showText" class="flex flex-col text-left">
            <span
                :class="[
                    'font-black tracking-tight leading-tight',
                    size === 'sm' ? 'text-sm' : '',
                    size === 'md' ? 'text-base' : '',
                    size === 'lg' ? 'text-xl' : '',
                    size === 'xl' ? 'text-2xl' : '',
                    'text-slate-900 dark:text-white'
                ]"
            >
                GREG <span class="text-slate-500 font-semibold">&amp;</span> CO.
                <span class="text-brand-800 dark:text-rose-400 font-extrabold ml-1.5">LMS</span>
            </span>
            <div class="flex items-center space-x-1.5">
                <span class="inline-block w-1.5 h-1.5 rounded-full bg-brand-700 dark:bg-rose-500"></span>
                <span class="text-[10px] tracking-wider uppercase font-semibold text-slate-500 dark:text-slate-400">
                    TVET Educational Hub
                </span>
            </div>
        </div>
    </div>
</template>
