<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import Prism from 'prismjs';
import 'prismjs/themes/prism-tomorrow.css';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-typescript';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-css';
import { computed, onMounted, watch, nextTick } from 'vue';
import BlogNavigation from '@/components/BlogNavigation.vue';
import CommentSection from '@/components/CommentSection.vue';
import { home } from '@/routes';

interface Tag {
  id: number;
  name: string;
  slug: string;
}

interface Author {
  id: number;
  name: string;
  initials?: string;
}

interface User {
  id: number;
  name: string;
  initials?: string;
}

interface Comment {
  id: number;
  content: string;
  created_at: string;
  created_at_human: string;
  created_at_formatted: string;
  user: User;
  replies?: Comment[];
}

interface Post {
  id: number;
  title: string;
  slug: string;
  excerpt?: string | null;
  content: string;
  image_url?: string | null;
  created_at: string;
  created_at_human: string;
  author?: Author | null;
  tags?: Tag[];
}

interface PaginatedComments {
  data: Comment[];
  prev_page_url: string | null;
  next_page_url: string | null;
  current_page: number;
  total: number;
}

const props = defineProps<{
  post: Post;
  comments: PaginatedComments;
  canRegister?: boolean;
}>();

onMounted(() => {
    Prism.highlightAll();
});

// Markdown-to-HTML converter
const htmlContent = computed(() => {
  const raw = marked.parse(props.post.content ?? '', {
    gfm: true,
    breaks: false,
  });
  return DOMPurify.sanitize(raw as string);
});

watch(htmlContent, () => {
    nextTick(() => {
        Prism.highlightAll();
    });
});

const publishedDate = computed(() => props.post.created_at_human);
</script>

<template>
  <Head :title="post.title" />

  <div class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
    <!-- Top Bar -->
    <BlogNavigation :can-register="canRegister" />

    <!-- Back button -->
    <div class="mx-auto max-w-5xl px-4 pt-6 sm:px-6 lg:px-8">
      <Link :href="home().url" class="inline-flex items-center gap-2 text-sm text-[#f53003] hover:text-[#f53003]/80 transition-colors dark:text-[#FF4433] dark:hover:text-[#FF4433]/80">
        ← Zpět na všechny články
      </Link>
    </div>

    <!-- Header Image -->
    <section class="relative mt-6">
      <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl shadow-xl dark:shadow-red-900/10 border border-[#19140015] dark:border-[#3E3E3A]">
          <img
            v-if="post.image_url"
            :src="post.image_url"
            :alt="post.title"
            class="aspect-[21/9] w-full object-cover"
          />
          <div v-else class="aspect-[21/9] w-full bg-linear-to-br from-[#fff2f2] to-white dark:from-[#1D0002] dark:to-[#0a0a0a]" />
        </div>
      </div>
    </section>

    <!-- Article Content -->
    <main class="mx-auto w-full max-w-3xl px-4 pb-24 pt-8 sm:px-6 lg:px-8">
      <!-- Title -->
      <h1 class="mb-3 text-balance text-3xl font-semibold tracking-tight sm:text-4xl">{{ post.title }}</h1>

      <!-- Meta: author, date, tags -->
      <div class="mb-8 flex flex-wrap items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400">
        <span v-if="post.author" class="inline-flex items-center gap-2">
          <div class="size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-500 dark:text-zinc-400 ring-1 ring-black/5 dark:ring-white/10 shrink-0">
            {{ post.author.initials }}
          </div>
          <span class="font-medium">{{ post.author.name }}</span>
        </span>
        <span class="h-1 w-1 rounded-full bg-zinc-400/70" />
        <time :datetime="post.created_at">{{ publishedDate }}</time>
        <template v-if="post.tags?.length">
          <span class="h-1 w-1 rounded-full bg-zinc-400/70" />
          <ul class="flex flex-wrap items-center gap-2">
            <li v-for="tag in post.tags" :key="tag.id">
              <Link :href="home({ query: { tag: tag.slug } }).url" class="inline-flex items-center rounded-full border border-[#19140035] px-2.5 py-0.5 text-xs text-zinc-700 dark:border-[#3E3E3A] dark:text-zinc-300 hover:border-[#f53003] hover:text-[#f53003] dark:hover:border-[#FF4433] dark:hover:text-[#FF4433] transition-colors">
                #{{ tag.name }}
              </Link>
            </li>
          </ul>
        </template>
      </div>

      <!-- Content -->
      <article class="prose prose-zinc max-w-none dark:prose-invert prose-headings:text-[#1b1b18] dark:prose-headings:text-[#EDEDEC] prose-a:text-[#f53003] dark:prose-a:text-[#FF4433] prose-pre:bg-transparent prose-pre:p-0">
        <div v-html="htmlContent" />
      </article>

      <!-- Comments Section -->
      <CommentSection
        :post-id="post.id"
        :comments="comments"
        :can-register="canRegister"
      />
    </main>

    <!-- Footer -->
    <footer class="mt-10 border-t border-[#1914001f] py-8 text-center text-sm text-zinc-500 dark:border-[#2a2a28] dark:text-zinc-400">
      Vytvořeno pomocí Laravel + Inertia + Vue. Stylizováno v barvách Laravelu.
    </footer>
  </div>
</template>
