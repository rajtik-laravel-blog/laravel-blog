<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PostForm from '@/components/author/PostForm.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as authorPosts, store as storePost } from '@/routes/author/posts';
import { type BreadcrumbItem } from '@/types';

defineProps<{
    tags: Array<{ id: number; name: string; slug: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nástěnka',
        href: dashboard().url,
    },
    {
        title: 'Moje články',
        href: authorPosts().url,
    },
    {
        title: 'Vytvořit článek',
        href: '#',
    },
];

const form = useForm({
    title: '',
    excerpt: '',
    content: '',
    image: null as File | null,
    image_url: null as string | null,
    tags: [] as string[],
});

const submit = () => {
    form.post(storePost().url, {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Vytvořit článek" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="mx-auto max-w-md md:max-w-3xl lg:max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <PostForm
                :form="form"
                :all-tags="tags"
                submit-label="Publikovat článek"
                @submit="submit"
                @update:image="form.image = $event"
                @update:tags="form.tags = $event"
            >
                <template #header>
                    <h1 class="text-3xl font-bold tracking-tight">Vytvořit nový článek</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Podělte se o své myšlenky se světem.</p>
                </template>
            </PostForm>
        </main>
    </AppLayout>
</template>
