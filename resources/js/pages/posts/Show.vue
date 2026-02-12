<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, watch } from 'vue';
import BlogFooter from '@/components/BlogFooter.vue';
import BlogNavigation from '@/components/BlogNavigation.vue';
import { useMarkdown } from '@/composables/useMarkdown';
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

const props = defineProps<{
  post: Post;
}>();

const { renderMarkdown, highlightCode } = useMarkdown();

onMounted(() => {
    highlightCode();
});

// Markdown-to-HTML converter
const htmlContent = computed(() => {
    return renderMarkdown(props.post.content ?? '');
});

watch(htmlContent, () => {
    highlightCode();
});

const publishedDate = computed(() => props.post.created_at_human);
const contactEmail = import.meta.env.VITE_CONTACT_EMAIL;
</script>

<template>
  <Head :title="post.title" />

  <div class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
    <!-- Top Bar -->
    <BlogNavigation />

    <!-- Back button -->
    <div class="mx-auto max-w-5xl px-4 pt-6 sm:px-6 lg:px-8">
      <Link :href="home().url" class="inline-flex items-center gap-2 text-sm text-[#f53003] hover:text-[#f53003]/80 transition-colors dark:text-[#FF4433] dark:hover:text-[#FF4433]/80">
        ← Zpět na všechny články
      </Link>
    </div>

    <!-- Header Image -->
    <section class="relative mt-6">
      <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl shadow-xl dark:shadow-red-900/10 border border-[#19140015] dark:border-[#3E3E3A] bg-[#FDFDFC] dark:bg-[#0a0a0a]">
          <img
            :src="post.image_url || '/images/placeholder.jpg'"
            :alt="post.title"
            class="aspect-[21/9] w-full object-cover"
          />
        </div>
      </div>
    </section>

    <!-- Article Content -->
    <main class="mx-auto w-full max-w-4xl px-4 pb-24 pt-8 sm:px-6 lg:px-8">
      <!-- Title -->
      <h1 class="mb-6 text-balance text-3xl font-bold tracking-tight sm:text-6xl">{{ post.title }}</h1>
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
      <article class="mt-12 max-w-none">
        <div v-html="htmlContent" />
      </article>

      <!-- Feedback -->
      <div class="mt-16 rounded-2xl border border-[#19140015] dark:border-[#3E3E3A] bg-[#fffdfc] dark:bg-[#111111] p-6 text-sm text-zinc-600 dark:text-zinc-400">
        <p>
          Našli jste v článku chybu nebo máte dotaz?
          <a
            :href="`mailto:${contactEmail}`"
            class="font-medium text-[#f53003] hover:text-[#f53003]/80 dark:text-[#FF4433] dark:hover:text-[#FF4433]/80 transition-colors"
          >
            Napište mi e‑mail
          </a>
          a rád se na to podívám.
        </p>
      </div>
    </main>

    <!-- Footer -->
    <BlogFooter />
  </div>
</template>
