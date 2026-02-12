<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import BlogNavigation from '@/components/BlogNavigation.vue';
import PostCard from '@/components/PostCard.vue';
import BlogFooter from '@/components/BlogFooter.vue';
import { home } from '@/routes';

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    image_url: string | null;
    created_at: string;
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
        <section class="relative isolate">
            <div
                class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
            >
                <div
                    class="absolute top-10 -left-40 size-[520px] rounded-full bg-[#f53003]/10 blur-3xl dark:bg-[#FF4433]/15"
                />
                <div
                    class="absolute top-24 -right-40 size-[520px] rounded-full bg-[#ffb703]/10 blur-3xl dark:bg-[#ffb703]/15"
                />
            </div>

            <div class="mx-auto max-w-7xl px-6 pt-16 sm:pt-20 lg:pt-24">
                <div class="mx-auto max-w-3xl text-center">
                    <h1
                        class="text-3xl font-semibold tracking-tight sm:text-4xl"
                    >
                        <template v-if="$page.props.search">
                            Výsledky hledání pro "{{ $page.props.search }}"
                        </template>
                        <template v-else>
                            {{
                                selectedTag
                                    ? `Články se štítkem #${selectedTag.name}`
                                    : 'Laravel zblízka: tipy, triky a zkušenosti z praxe'
                            }}
                        </template>
                    </h1>
                    <p
                        v-if="!selectedTag && !$page.props.search"
                        class="mt-3 sm:text-base text-sm leading-6 text-[#706f6c] dark:text-[#A1A09A]"
                    >
                        Neoficiální blog o Laravelu – návody, praktické tipy,
                        deep dive do funkcí a zkušenosti z reálného vývoje s PHP a Laravel frameworkem.
                    </p>
                    <div v-if="selectedTag || $page.props.search" class="mt-4">
                        <Link
                            :href="home().url"
                            class="text-sm font-medium text-[#f53003] hover:underline dark:text-[#FF4433]"
                        >
                            Zrušit filtr
                        </Link>
                    </div>
                </div>

                <!-- Posts grid -->
                <div class="mt-10">
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
                            class="rounded-md px-4 py-2 text-sm font-medium text-zinc-600 transition-colors hover:bg-[#19140008] dark:text-[#A1A09A] dark:hover:bg-[#ffffff0a]"
                        >
                            &laquo; Předchozí
                        </Link>
                        <div v-else class="px-4 py-2 text-sm text-zinc-400 dark:text-zinc-600">
                            &laquo; Předchozí
                        </div>

                        <Link
                            v-if="posts.next_page_url"
                            :href="posts.next_page_url"
                            class="rounded-md px-4 py-2 text-sm font-medium text-zinc-600 transition-colors hover:bg-[#19140008] dark:text-[#A1A09A] dark:hover:bg-[#ffffff0a]"
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
    </div>
</template>
