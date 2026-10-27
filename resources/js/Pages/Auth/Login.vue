<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, UserPlus } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6 text-center">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Sign In to LMS</h1>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Greg &amp; Co. ICT Limited &bull; IDEAS–TVET Portal</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email Address" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="trainee@gregicthub.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <PasswordInput
                    id="password"
                    class="mt-1"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    :has-error="!!form.errors.password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-xs sm:text-sm text-slate-600 dark:text-slate-400"
                        >Remember me</span
                    >
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 hover:underline"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center py-3 text-sm font-bold shadow-md shadow-brand-900/20"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Sign In
                </PrimaryButton>
            </div>

            <!-- Prominent Register Button / Link -->
            <div v-if="canRegister !== false" class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Don't have an account yet?
                    <Link
                        :href="route('register')"
                        class="font-bold text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 hover:underline ms-1 inline-flex items-center"
                    >
                        <span>Create an Account / Register</span>
                        <ArrowRight class="w-3.5 h-3.5 ms-1" />
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
