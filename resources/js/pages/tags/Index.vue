<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import BlogFooter from '@/components/BlogFooter.vue';
import BlogNavigation from '@/components/BlogNavigation.vue';
import { home } from '@/routes';

defineProps<{
    tags: Array<{
        id: number;
        name: string;
        slug: string;
        posts_count: number;
    }>;
}>();
</script>

<template>
    <Head title="Procházet štítky" />

    <div class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <!-- Top Bar -->
        <BlogNavigation />

        <main class="mx-auto max-w-7xl px-6 pt-16 sm:pt-20 lg:pt-24 pb-20">
            <div class="mx-auto max-w-3xl text-center mb-12">
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Procházet podle témat</h1>
                <p class="mt-3 text-sm leading-6 text-[#706f6c] dark:text-[#A1A09A]">
                    Prozkoumejte všechny články uspořádané podle štítků. Kliknutím na štítek zobrazíte všechny související poznatky.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <Link
                    v-for="tag in tags"
                    :key="tag.id"
                    :href="home({ query: { tag: tag.slug } }).url"
                    class="group relative flex flex-col p-6 bg-white dark:bg-[#161615] rounded-xl border border-[#19140015] dark:border-[#3E3E3A] transition-all hover:shadow-md hover:border-[#f53003]/30 dark:hover:border-[#FF4433]/30"
                >
                    <span class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] group-hover:text-[#f53003] dark:group-hover:text-[#FF4433] transition-colors">
                        #{{ tag.name }}
                    </span>
                    <span class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        {{ tag.posts_count }} {{ tag.posts_count === 1 ? 'článek' : (tag.posts_count >= 2 && tag.posts_count <= 4 ? 'články' : 'článků') }}
                    </span>

                    <div class="absolute bottom-4 right-6 opacity-0 group-hover:opacity-100 transition-opacity translate-x-1 group-hover:translate-x-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </div>
                </Link>
            </div>

            <div v-if="!tags.length" class="text-center py-20">
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Nebyly nalezeny žádné štítky.</p>
            </div>
        </main>

        <!-- Footer -->
        <BlogFooter />
    </div>
</template>
