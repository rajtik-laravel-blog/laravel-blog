<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Edit2, Eye, FileText } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as authorPosts, create as createPost, edit as editPost } from '@/routes/author/posts';
import { show as showPost } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    image_url: string | null;
    created_at: string;
    tags: Array<{ id: number; name: string; slug: string }>;
}

interface Props {
    posts: {
        data: Post[];
        links: any[];
        meta: any;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nástěnka',
        href: dashboard().url,
    },
    {
        title: 'Moje články',
        href: authorPosts().url,
    },
];

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('cs-CZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Moje články" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Správa článků</h1>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Přehled a správa všech vašich publikovaných článků.</p>
                </div>
                <Button as-child>
                    <Link :href="createPost().url">Vytvořit nový článek</Link>
                </Button>
            </div>

            <div class="overflow-hidden rounded-xl border border-[#19140015] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-[#19140015] bg-[#19140005] dark:border-[#3E3E3A] dark:bg-[#ffffff05]">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Článek</th>
                                <th class="hidden px-6 py-4 font-semibold sm:table-cell">Štítky</th>
                                <th class="hidden px-6 py-4 font-semibold md:table-cell">Datum</th>
                                <th class="px-6 py-4 text-right font-semibold">Akce</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#19140015] dark:divide-[#3E3E3A]">
                            <tr v-for="post in posts.data" :key="post.id" class="group hover:bg-[#19140003] dark:hover:bg-[#ffffff03]">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div v-if="post.image_url" class="hidden h-12 w-20 overflow-hidden rounded-md border border-[#19140015] dark:border-[#3E3E3A] sm:block">
                                            <img :src="post.image_url" class="h-full w-full object-cover" />
                                        </div>
                                        <div v-else class="hidden h-12 w-20 items-center justify-center rounded-md bg-[#19140008] dark:bg-[#ffffff08] sm:flex">
                                            <FileText class="size-4 text-zinc-400" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ post.title }}</div>
                                            <div class="mt-1 text-xs text-zinc-500 line-clamp-1 max-w-[200px] sm:max-w-xs">{{ post.excerpt }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="hidden px-6 py-4 sm:table-cell">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge v-for="tag in post.tags" :key="tag.id" variant="secondary" class="text-[10px] px-1.5 py-0 bg-zinc-100 dark:bg-zinc-800">
                                            {{ tag.name }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="hidden px-6 py-4 text-zinc-500 md:table-cell">
                                    {{ formatDate(post.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="icon" as-child title="Zobrazit">
                                            <Link :href="showPost(post.slug).url">
                                                <Eye class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="icon" as-child title="Upravit">
                                            <Link :href="editPost(post.id).url">
                                                <Edit2 class="size-4" />
                                            </Link>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="posts.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-zinc-500">
                                    Zatím jste nenapsali žádný článek.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="posts.meta && posts.meta.last_page > 1" class="border-t border-[#19140015] px-6 py-4 dark:border-[#3E3E3A]">
                    <!-- Simple pagination for now -->
                    <nav class="flex items-center justify-center gap-2">
                        <Link
                            v-for="link in posts.links"
                            :key="link.label"
                            :href="link.url || ''"
                            class="px-3 py-1 text-sm rounded-md transition-colors"
                            :class="[
                                link.active ? 'bg-[#f53003] text-white' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </nav>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
