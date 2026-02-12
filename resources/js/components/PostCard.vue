<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { home } from '@/routes';

defineProps<{
    post: {
        id: number;
        title: string;
        slug: string;
        excerpt: string;
        image_url: string;
        created_at: string;
        created_at_human: string;
        created_at_formatted: string;
        author?: { name: string; initials: string };
        tags?: Array<{ id: number; name: string; slug: string }>;
    };
}>();

</script>

<template>
    <article class="group relative flex flex-col items-start bg-white dark:bg-[#161615] rounded-2xl overflow-hidden border border-[#19140015] dark:border-[#3E3E3A] transition-all hover:shadow-lg dark:hover:shadow-2xl dark:hover:shadow-red-500/10">
        <div class="relative w-full aspect-video overflow-hidden bg-[#FDFDFC] dark:bg-[#0a0a0a]">
            <img
                :src="post.image_url || '/images/placeholder.jpg'"
                :alt="post.title"
                class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105"
            />
            <div class="absolute inset-0 bg-linear-to-t from-black/5 to-transparent"></div>
        </div>

        <div class="p-6 flex flex-col flex-1">
            <div class="flex items-center gap-2 mb-3 relative z-10">
                <TooltipProvider :delay-duration="0">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <time :datetime="post.created_at" class="text-[11px] text-[#706f6c] dark:text-[#A1A09A]">
                                {{ post.created_at_human }}
                            </time>
                        </TooltipTrigger>
                        <TooltipContent>
                            <span class="text-xs">{{ post.created_at_formatted }}</span>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </div>

            <h2 class="text-xl font-semibold mb-2 text-[#1b1b18] dark:text-[#EDEDEC] group-hover:text-[#f53003] dark:group-hover:text-[#FF4433] transition-colors leading-tight">
                <Link :href="'/posts/' + post.slug">
                    <span class="absolute inset-0"></span>
                    {{ post.title }}
                </Link>
            </h2>

            <div v-if="post.author" class="flex items-center gap-1.5 mb-3 text-[12px] text-[#706f6c] dark:text-[#A1A09A]">
                <div class="size-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[7px] font-bold text-zinc-500 dark:text-zinc-400 ring-1 ring-black/5 dark:ring-white/10">
                    {{ post.author.initials }}
                </div>
                <span>{{ post.author.name }}</span>
            </div>

            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] line-clamp-3 leading-relaxed mb-4">
                {{ post.excerpt }}
            </p>

            <div v-if="post.tags?.length" class="flex flex-wrap gap-1.5 mb-4 relative z-10">
                <Link
                    v-for="tag in post.tags"
                    :key="tag.id"
                    :href="home({ query: { tag: tag.slug } }).url"
                    class="inline-flex items-center rounded-full bg-[#19140008] dark:bg-[#ffffff0a] px-2 py-0.5 text-[10px] font-medium text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f53003]/10 hover:text-[#f53003] dark:hover:bg-[#FF4433]/10 dark:hover:text-[#FF4433] transition-colors"
                >
                    #{{ tag.name }}
                </Link>
            </div>

            <div class="mt-auto flex items-center text-sm font-medium text-[#f53003] dark:text-[#FF4433]">
                Číst dál
                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </div>
        </div>
    </article>
</template>
