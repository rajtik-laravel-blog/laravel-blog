<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Calendar } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import BlogFooter from '@/components/BlogFooter.vue';
import BlogNavigation from '@/components/BlogNavigation.vue';
import ScrollToTop from '@/components/ScrollToTop.vue';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
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

const { renderMarkdown } = useMarkdown();

const htmlContent = ref('');
const isRendering = ref(true);

const updateHtmlContent = async () => {
    const source = props.post.content ?? '';

    isRendering.value = true;
    htmlContent.value = '';

    try {
        htmlContent.value = await renderMarkdown(source);
    } finally {
        isRendering.value = false;
    }
};

onMounted(() => {
    void updateHtmlContent();
});

watch(() => props.post.content, () => {
    void updateHtmlContent();
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
    <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:px-8">
      <Link :href="home().url" class="inline-flex items-center gap-2 text-sm text-[#f53003] hover:text-[#f53003]/80 transition-colors dark:text-[#FF4433] dark:hover:text-[#FF4433]/80">
        ← Zpět na všechny články
      </Link>
    </div>

    <!-- Header Image -->
    <section class="relative mt-6">
      <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">
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
    <main class="mx-auto w-full max-w-5xl px-4 pb-24 pt-8 sm:px-6 lg:px-8">
      <!-- Title -->
      <h1 class="mb-6 text-balance text-3xl font-bold tracking-tight sm:text-6xl">{{ post.title }}</h1>
      <!-- Meta: author, date, tags -->
      <div class="mb-8 flex flex-wrap items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400">
        <TooltipProvider :delay-duration="0">
          <Tooltip>
            <TooltipTrigger as-child>
              <time :datetime="post.created_at" class="flex items-center gap-1.5 cursor-default">
                <Calendar class="size-3.5" />
                {{ publishedDate }}
              </time>
            </TooltipTrigger>
            <TooltipContent>
              <span class="text-xs">{{ post.created_at_formatted }}</span>
            </TooltipContent>
          </Tooltip>
        </TooltipProvider>

        <template v-if="post.tags?.length">
          <div class="flex flex-wrap items-center gap-2">
            <Link
              v-for="tag in post.tags"
              :key="tag.id"
              :href="home({ query: { tag: tag.slug } }).url"
              class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 hover:bg-[#f53003]/10 hover:text-[#f53003] dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-[#FF4433]/10 dark:hover:text-[#FF4433] transition-colors"
            >
              #{{ tag.name }}
            </Link>
          </div>
        </template>
      </div>

      <!-- Content -->
      <article class="mt-12 max-w-none">
        <div v-if="isRendering || !htmlContent" class="space-y-3">
          <div class="text-sm text-zinc-500 dark:text-zinc-400">
            Načítám obsah…
          </div>
          <div class="h-4 w-3/4 animate-pulse rounded bg-zinc-200 dark:bg-zinc-800" />
          <div class="h-4 w-full animate-pulse rounded bg-zinc-200 dark:bg-zinc-800" />
          <div class="h-4 w-11/12 animate-pulse rounded bg-zinc-200 dark:bg-zinc-800" />
          <div class="h-4 w-4/5 animate-pulse rounded bg-zinc-200 dark:bg-zinc-800" />
          <div class="h-4 w-2/3 animate-pulse rounded bg-zinc-200 dark:bg-zinc-800" />

          <!-- fake heading -->
          <div class="mt-8 h-7 w-1/2 animate-pulse rounded bg-zinc-200 dark:bg-zinc-800" />

          <!-- fake code block skeleton -->
          <div class="mt-6 h-28 w-full animate-pulse rounded-2xl bg-zinc-900/10 dark:bg-zinc-100/10" />
        </div>

        <div v-else v-html="htmlContent" />
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

    <!-- Scroll to Top -->
    <ScrollToTop />
  </div>
</template>
