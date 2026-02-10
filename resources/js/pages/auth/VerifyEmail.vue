<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Ověření e-mailu"
        description="Ověřte prosím svou e-mailovou adresu kliknutím na odkaz, který jsme vám právě poslali."
    >
        <Head title="Ověření e-mailu" />

        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            Nový ověřovací odkaz byl odeslán na e-mailovou adresu, kterou jste uvedli při registraci.
        </div>

        <Form
            v-bind="send.form()"
            class="space-y-6 text-center"
            v-slot="{ processing }"
        >
            <Button :disabled="processing" variant="secondary">
                <Spinner v-if="processing" />
                Zaslat ověřovací e-mail znovu
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                Odhlásit se
            </TextLink>
        </Form>
    </AuthLayout>
</template>
