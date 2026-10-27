<script setup>
import { ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    id: {
        type: String,
        default: 'password',
    },
    name: {
        type: String,
        default: 'password',
    },
    placeholder: {
        type: String,
        default: '••••••••',
    },
    autocomplete: {
        type: String,
        default: 'current-password',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    hasError: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);

const showPassword = ref(false);

const toggleShowPassword = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="relative w-full">
        <input
            :id="id"
            :name="name"
            :type="showPassword ? 'text' : 'password'"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :required="required"
            :autofocus="autofocus"
            :class="[
                'w-full rounded-xl pr-11 py-2.5 text-sm transition-all duration-150 shadow-sm',
                'bg-white dark:bg-slate-900/90 text-slate-900 dark:text-slate-100',
                hasError
                    ? 'border-red-500 dark:border-red-500 focus:border-red-500 focus:ring-red-500/20'
                    : 'border-slate-300 dark:border-slate-700 focus:border-brand-700 dark:focus:border-rose-500 focus:ring-4 focus:ring-brand-700/10 dark:focus:ring-rose-500/10'
            ]"
        />
        <button
            type="button"
            @click="toggleShowPassword"
            :title="showPassword ? 'Hide password' : 'Show password'"
            tabindex="-1"
            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors focus:outline-none"
        >
            <EyeOff v-if="showPassword" class="w-4 h-4" />
            <Eye v-else class="w-4 h-4" />
        </button>
    </div>
</template>
