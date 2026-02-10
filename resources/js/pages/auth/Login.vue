<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Přihlaste se do svého účtu"
        description="Zadejte e-mail a heslo pro přihlášení"
    >
        <Head title="Přihlášení" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">E-mailová adresa</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Heslo</Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="5"
                        >
                            Zapomněli jste heslo?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Heslo"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" name="remember" :tabindex="3" />
                        <span>Zapamatovat přihlášení</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" />
                    Přihlásit se
                </Button>
            </div>

            <div class="relative my-2 text-center">
                <span class="px-2 text-xs uppercase tracking-wider text-zinc-500">nebo pokračujte přes</span>
            </div>

            <div class="flex items-center justify-center gap-3" aria-label="Sociální přihlášení">
                <a href="/auth/github/redirect" class="inline-flex size-10 items-center justify-center rounded-md border bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-transparent dark:text-zinc-200 dark:hover:bg-zinc-900" tabindex="5" aria-label="Continue with GitHub">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-5" aria-hidden="true">
                        <path d="M12 0C5.37 0 0 5.37 0 12a12 12 0 008.205 11.387c.6.111.82-.261.82-.58 0-.287-.01-1.047-.016-2.055-3.338.726-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.758-1.333-1.758-1.089-.745.083-.73.083-.73 1.205.085 1.84 1.237 1.84 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.776.418-1.305.762-1.605-2.665-.304-5.466-1.333-5.466-5.931 0-1.31.468-2.381 1.236-3.221-.124-.303-.536-1.523.116-3.176 0 0 1.008-.322 3.301 1.23a11.5 11.5 0 016.004 0c2.292-1.552 3.299-1.23 3.299-1.23.653 1.653.241 2.873.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.61-2.804 5.624-5.476 5.921.43.371.823 1.102.823 2.222 0 1.604-.015 2.896-.015 3.289 0 .321.216.697.825.579A12.004 12.004 0 0024 12c0-6.63-5.37-12-12-12z"/>
                    </svg>
                </a>
                <a href="/auth/google/redirect" class="inline-flex size-10 items-center justify-center rounded-md border bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-800 dark:bg-transparent dark:text-zinc-200 dark:hover:bg-zinc-900" tabindex="6" aria-label="Continue with Google">
                    <svg viewBox="0 0 24 24" class="size-5" aria-hidden="true">
                        <path fill="#EA4335" d="M12 10.2v3.6h5.1c-.22 1.16-.9 2.14-1.92 2.8l3.1 2.4C20.3 17.5 21 15.4 21 12.9c0-.66-.06-1.3-.18-1.9H12z"/>
                        <path fill="#34A853" d="M6.6 14.3l-.9.7-2.5 1.9C4.4 19.7 7 21 10 21c2.7 0 5-1 6.7-2.7l-3.1-2.4c-.86.58-1.95.93-3.6.93-2.74 0-5.07-1.85-5.9-4.33z"/>
                        <path fill="#4A90E2" d="M3.2 7.6A8.99 8.99 0 013 12c0 1.18.23 2.3.64 3.3l3-2.3a5.38 5.38 0 01-.29-1.7c0-.59.1-1.15.29-1.7l-3.04-2.3z"/>
                        <path fill="#FBBC05" d="M10 6.1c1.47 0 2.79.5 3.83 1.49l2.87-2.87C15 2.8 12.7 2 10 2 7 2 4.4 3.3 2.7 5.1l3 2.3C6.6 7 8.3 6.1 10 6.1z"/>
                    </svg>
                </a>
            </div>

            <div
                class="text-center text-sm text-muted-foreground"
                v-if="canRegister"
            >
                Nemáte účet?
                <TextLink :href="register()" :tabindex="6">Zaregistrovat se</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
