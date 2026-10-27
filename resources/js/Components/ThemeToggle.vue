<script setup>
import { ref, onMounted } from 'vue';
import { Sun, Moon } from 'lucide-vue-next';

const isDark = ref(false);

const updateThemeState = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

const toggleTheme = () => {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
        isDark.value = false;
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
        isDark.value = true;
    }
};

onMounted(() => {
    updateThemeState();
});
</script>

<template>
    <button
        type="button"
        @click="toggleTheme"
        :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
        aria-label="Toggle theme"
        class="relative inline-flex items-center justify-center p-2 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-700/50 text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200/80 dark:text-slate-300 dark:hover:text-white dark:bg-slate-800 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700/80 shadow-sm"
    >
        <span class="sr-only">Toggle theme</span>
        <!-- Sun icon (shown in dark mode to switch to light) -->
        <Sun
            v-if="isDark"
            class="w-4 h-4 text-amber-400 transition-transform duration-300 rotate-0 hover:rotate-45"
        />
        <!-- Moon icon (shown in light mode to switch to dark) -->
        <Moon
            v-else
            class="w-4 h-4 text-slate-700 transition-transform duration-300 -rotate-12 hover:rotate-0"
        />
    </button>
</template>
