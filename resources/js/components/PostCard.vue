<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {ArrowRight, Calendar} from 'lucide-vue-next';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { home } from '@/routes';
import { show } from '@/routes/posts/index';

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
            <div class="flex flex-wrap items-center gap-2 mb-3 relative z-10">
                <TooltipProvider :delay-duration="0">
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <time :datetime="post.created_at" class="flex items-center gap-1.5 text-[11px] text-[#706f6c] dark:text-[#A1A09A] cursor-default">
                                <Calendar class="size-3" />
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
                <Link :href="show({ slug: post.slug }).url">
                    <span class="absolute inset-0"></span>
                    {{ post.title }}
                </Link>
            </h2>


            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] line-clamp-3 leading-relaxed mb-4">
                {{ post.excerpt }}
            </p>

            <template v-if="post.tags?.length">
                <div class="flex flex-wrap gap-1.5 mb-4 relative z-10">
                    <Link
                        v-for="tag in post.tags"
                        :key="tag.id"
                        :href="home({ query: { tag: tag.slug } }).url"
                        class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 hover:bg-[#f53003]/10 hover:text-[#f53003] dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-[#FF4433]/10 dark:hover:text-[#FF4433] transition-colors"
                    >
                        #{{ tag.name }}
                    </Link>
                </div>
            </template>


            <div class="mt-auto flex items-center text-sm font-medium text-[#f53003] dark:text-[#FF4433]">
                Číst dál
                <ArrowRight
                    class="size-4 transition-transform group-hover:translate-x-1"
                />
            </div>
        </div>
    </article>
</template>
