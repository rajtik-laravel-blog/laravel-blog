<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Calendar, Clock, Tag } from 'lucide-vue-next';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

import BlogFooter from '@/components/BlogFooter.vue';
import BlogNavigation from '@/components/BlogNavigation.vue';
import PostCard from '@/components/PostCard.vue';
import ScrollToTop from '@/components/ScrollToTop.vue';
import { home } from '@/routes';
import { show } from '@/routes/posts/index';

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    image_url: string | null;
    created_at: string;
    created_at_human?: string;
    read_time?: string;
    category?: string;
    author?: { name: string; initials: string };
    tags?: Array<{ id: number; name: string; slug: string }>;
}

interface PaginatedPosts {
    data: Post[];
    prev_page_url: string | null;
    next_page_url: string | null;
    current_page: number;
}

defineProps<{
    posts: PaginatedPosts;
    featuredPost?: Post | null;
    selectedTag?: { id: number; name: string; slug: string } | null;
}>();
</script>

<template>
    <Head title="Domů" />

    <div
        class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]"
    >
        <!-- Top Bar -->
        <BlogNavigation />

        <!-- Hero -->
        <section class="relative isolate overflow-hidden pt-16 pb-20">
            <!-- Background decorations -->
            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
                <div
                    class="absolute -top-40 -right-40 size-[500px] rounded-full bg-[#FF2D20]/5 blur-3xl dark:bg-[#FF2D20]/10"
                />
                <div
                    class="absolute -bottom-40 -left-40 size-[400px] rounded-full bg-[#FF2D20]/3 blur-3xl dark:bg-[#FF2D20]/5"
                />
            </div>

            <div class="mx-auto max-w-6xl px-6">
                <div class="mx-auto max-w-3xl text-center mb-16">
                    <h1
                        class="text-4xl leading-tight font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white"
                    >
                        <template v-if="$page.props.search">
                            Výsledky hledání pro "{{ $page.props.search }}"
                        </template>
                        <template v-else>
                            <span v-if="selectedTag">Články se štítkem #{{ selectedTag.name }}</span>
                            <template v-else>
                                <span class="text-[#FF2D20]">Laravel</span> zblízka: tipy, triky a zkušenosti z praxe
                            </template>
                        </template>
                    </h1>
                    <p
                        v-if="!selectedTag && !$page.props.search"
                        class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-gray-400"
                    >
                        Neoficiální blog o Laravelu – návody, praktické tipy,
                        deep dive do funkcí a zkušenosti z reálného vývoje s PHP a Laravel frameworkem.
                    </p>
                    <div v-if="selectedTag || $page.props.search" class="mt-4">
                        <Link
                            :href="home().url"
                            class="text-sm font-medium text-[#FF2D20] hover:underline"
                        >
                            Zrušit filtr
                        </Link>
                    </div>
                </div>

                <!-- Featured Post (Latest item) -->
                <div v-if="featuredPost && !selectedTag && !$page.props.search" class="mb-20">
                    <div
                        class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-gray-800 dark:bg-[#161616]"
                    >
                        <div class="grid gap-0 lg:grid-cols-2">
                            <div class="relative overflow-hidden">
                                <img
                                    v-if="featuredPost.image_url"
                                    :src="featuredPost.image_url"
                                    :alt="featuredPost.title"
                                    class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-105 lg:h-full"
                                />
                                <div v-else class="h-64 w-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center lg:h-full">
                                    <span class="text-gray-400">No image</span>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="rounded-lg bg-[#FF2D20] px-3 py-1 text-xs font-bold tracking-wide text-white uppercase"
                                    >
                                        Nejnovější
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col justify-center p-8 lg:p-12">
                                <div class="mb-4 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <TooltipProvider :delay-duration="0">
                                        <Tooltip>
                                            <TooltipTrigger as-child>
                                                <span class="inline-flex items-center gap-1.5 cursor-default relative z-10">
                                                    <Calendar class="size-3.5" />
                                                    {{ featuredPost.created_at_human }}
                                                </span>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <span class="text-xs">{{ featuredPost.created_at_formatted }}</span>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                    <span v-if="featuredPost.read_time" class="inline-flex items-center gap-1.5">
                                        <Clock class="size-3.5" />
                                        {{ featuredPost.read_time }}
                                    </span>
                                    <span
                                        v-if="featuredPost.category"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-[#FF2D20]/10 px-2.5 py-0.5 text-xs font-medium text-[#FF2D20]"
                                    >
                                        <Tag class="size-3" />
                                        {{ featuredPost.category }}
                                    </span>
                                    <template v-if="featuredPost.tags?.length">
                                        <div class="relative z-10 flex flex-wrap gap-2">
                                            <Link
                                                v-for="tag in featuredPost.tags"
                                                :key="tag.id"
                                                :href="home({ query: { tag: tag.slug } }).url"
                                                class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 hover:bg-[#f53003]/10 hover:text-[#f53003] dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-[#FF4433]/10 dark:hover:text-[#FF4433] transition-colors"
                                            >
                                                #{{ tag.name }}
                                            </Link>
                                        </div>
                                    </template>
                                </div>
                                <h2
                                    class="mb-4 text-2xl leading-tight font-bold text-gray-900 transition-colors group-hover:text-[#FF2D20] lg:text-3xl dark:text-white"
                                >
                                    <Link :href="show({ slug: featuredPost.slug }).url">
                                        <span class="absolute inset-0"></span>
                                        {{ featuredPost.title }}
                                    </Link>
                                </h2>
                                <p class="mb-6 leading-relaxed text-gray-600 dark:text-gray-400">
                                    {{ featuredPost.excerpt }}
                                </p>
                                <Link
                                    :href="show({ slug: featuredPost.slug }).url"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-[#FF2D20] transition-colors hover:text-[#e52a1e]"
                                >
                                    Číst dál
                                    <ArrowRight
                                        class="size-4 transition-transform group-hover:translate-x-1"
                                    />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Posts grid -->
                <div class="mt-10">
                    <h2 v-if="!selectedTag && !$page.props.search" class="mb-8 text-2xl font-bold tracking-tight text-gray-900 lg:text-3xl dark:text-white">
                        Všechny články
                    </h2>
                    <!-- Posts grid -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <PostCard
                            v-for="post in posts.data"
                            :key="post.id"
                            :post="post"
                        />
                    </div>

                    <!-- Pagination Bottom -->
                    <div v-if="posts.prev_page_url || posts.next_page_url" class="mt-10 flex items-center justify-between">
                        <Link
                            v-if="posts.prev_page_url"
                            :href="posts.prev_page_url"
                            class="rounded-md px-4 py-2 text-sm font-medium text-[#FF2D20] transition-colors hover:bg-[#FF2D20]/5 dark:hover:bg-[#FF2D20]/10"
                        >
                            &laquo; Předchozí
                        </Link>
                        <div v-else class="px-4 py-2 text-sm text-zinc-400 dark:text-zinc-600">
                            &laquo; Předchozí
                        </div>

                        <Link
                            v-if="posts.next_page_url"
                            :href="posts.next_page_url"
                            class="rounded-md px-4 py-2 text-sm font-medium text-[#FF2D20] transition-colors hover:bg-[#FF2D20]/5 dark:hover:bg-[#FF2D20]/10"
                        >
                            Další &raquo;
                        </Link>
                        <div v-else class="px-4 py-2 text-sm text-zinc-400 dark:text-zinc-600">
                            Další &raquo;
                        </div>
                    </div>
                </div>

                <div
                    v-if="!posts.data.length"
                    class="mt-16 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]"
                >
                    Zatím žádné příspěvky. Začněte vytvořením svého prvního
                    článku.
                </div>

                <div class="h-20" />
            </div>
        </section>

        <!-- Footer -->
        <BlogFooter />

        <!-- Scroll to Top -->
        <ScrollToTop />
    </div>
</template>
