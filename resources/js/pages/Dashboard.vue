<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { MessageSquare, Reply, FileText, ArrowRight } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as authorPosts } from '@/routes/author/posts';
import { index as commentsIndex } from '@/routes/comments';
import { show as showPost } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';

interface Stats {
    total_comments: number;
    total_replies: number;
    total_articles?: number;
}

interface LatestReply {
    id: number;
    content: string;
    created_at_human: string;
    user: {
        name: string;
    };
    post: {
        title: string;
        slug: string;
    };
}

defineProps<{
    stats: Stats;
    latestReplies: LatestReply[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nástěnka',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Nástěnka" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 p-4 md:p-8">
            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-[#19140015] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400">
                            <MessageSquare class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500">Moje komentáře</p>
                            <h3 class="text-2xl font-bold">{{ stats.total_comments }}</h3>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-[#19140015] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400">
                            <Reply class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500">Odpovědi na mě</p>
                            <h3 class="text-2xl font-bold">{{ stats.total_replies }}</h3>
                        </div>
                    </div>
                </div>

                <div v-if="stats.total_articles !== undefined" class="rounded-xl border border-[#19140015] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400">
                            <FileText class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500">Moje články</p>
                            <h3 class="text-2xl font-bold">{{ stats.total_articles }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Latest Replies -->
                <div class="rounded-xl border border-[#19140015] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <div class="border-b border-[#19140015] px-6 py-4 dark:border-[#3E3E3A]">
                        <h3 class="font-semibold">Poslední odpovědi</h3>
                    </div>
                    <div class="p-6">
                        <div v-if="latestReplies.length > 0" class="space-y-6">
                            <div v-for="reply in latestReplies" :key="reply.id" class="flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ reply.user.name }}</span>
                                    <span class="text-xs text-zinc-500">{{ reply.created_at_human }}</span>
                                </div>
                                <p class="text-sm text-zinc-600 line-clamp-2 dark:text-zinc-400 italic">
                                    "{{ reply.content }}"
                                </p>
                                <div class="flex items-center gap-1 text-xs">
                                    <span class="text-zinc-400 text-nowrap">U článku:</span>
                                    <a :href="showPost(reply.post.slug).url" class="text-[#f53003] hover:underline dark:text-[#FF4433] line-clamp-1">
                                        {{ reply.post.title }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-8 text-center text-sm text-zinc-500">
                            Zatím žádné odpovědi.
                        </div>
                    </div>
                </div>

                <!-- Quick Actions / Links -->
                <div class="flex flex-col gap-4">
                    <Link
                        :href="commentsIndex().url"
                        class="flex items-center justify-between rounded-xl border border-[#19140015] bg-white p-6 shadow-sm transition-colors hover:bg-zinc-50 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:bg-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex size-10 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <MessageSquare class="size-5" />
                            </div>
                            <div>
                                <h4 class="font-semibold">Spravovat komentáře</h4>
                                <p class="text-xs text-zinc-500">Zobrazit, upravit nebo smazat vaše komentáře</p>
                            </div>
                        </div>
                        <ArrowRight class="size-5 text-zinc-400" />
                    </Link>

                    <Link
                        v-if="$page.props.auth.user.is_author"
                        :href="authorPosts().url"
                        class="flex items-center justify-between rounded-xl border border-[#19140015] bg-white p-6 shadow-sm transition-colors hover:bg-zinc-50 dark:border-[#3E3E3A] dark:bg-[#161615] dark:hover:bg-white/5"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex size-10 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <FileText class="size-5" />
                            </div>
                            <div>
                                <h4 class="font-semibold">Moje články</h4>
                                <p class="text-xs text-zinc-500">Správa vašich publikovaných článků</p>
                            </div>
                        </div>
                        <ArrowRight class="size-5 text-zinc-400" />
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
