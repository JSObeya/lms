<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PasswordInput from '@/Components/PasswordInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowRight, UserPlus } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">Create an Account</h1>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Join the IDEAS–TVET Practical Learning Portal</p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Full Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="e.g. John Doe"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email Address" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                    :has-error="!!form.errors.password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    class="mt-1"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    :has-error="!!form.errors.password_confirmation"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center py-3 text-sm font-bold shadow-md shadow-brand-900/20"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <UserPlus class="w-4 h-4 mr-2" />
                    Register Account
                </PrimaryButton>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800 text-center">
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                    Already have an account?
                    <Link
                        :href="route('login')"
                        class="font-bold text-brand-700 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 hover:underline ms-1 inline-flex items-center"
                    >
                        <span>Sign In</span>
                        <ArrowRight class="w-3.5 h-3.5 ms-1" />
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
