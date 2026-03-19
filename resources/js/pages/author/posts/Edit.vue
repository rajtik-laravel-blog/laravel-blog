<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import PostForm from '@/components/author/PostForm.vue';
import FlashToast from '@/components/FlashToast.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { update as updatePost } from '@/routes/author/posts';
import { type BreadcrumbItem } from '@/types';

const props = defineProps<{
    post: {
        id: number;
        title: string;
        excerpt: string;
        content: string;
        image_url: string | null;
        tags: Array<{ id: number; name: string; slug: string }>;
    };
    tags: Array<{ id: number; name: string; slug: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nástěnka',
        href: dashboard().url,
    },
    {
        title: 'Upravit článek',
        href: '#',
    },
];

const form = useForm({
    title: props.post.title,
    excerpt: props.post.excerpt || '',
    content: props.post.content,
    image: null as File | null,
    image_url: props.post.image_url,
    tags: props.post.tags.map(t => t.name),
});

const submit = () => {
    form.put(updatePost(props.post.id).url);
};
</script>

<template>
    <Head title="Upravit článek" />

    <FlashToast />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <PostForm
                :form="form"
                :all-tags="tags"
                submit-label="Uložit změny"
                @submit="submit"
                @update:image="form.image = $event"
                @update:tags="form.tags = $event"
                @update:title="form.title = $event"
                @update:excerpt="form.excerpt = $event"
                @update:content="form.content = $event"
            >
                <template #header>
                    <h1 class="text-3xl font-bold tracking-tight">Upravit článek</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Aktualizujte svůj obsah pro své čtenáře.</p>
                </template>
            </PostForm>
        </main>
    </AppLayout>
</template>
