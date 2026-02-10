<script setup lang="ts">
import { useForm, usePage, Link } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import Prism from 'prismjs';
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { login, register } from '@/routes';
import { store } from '@/routes/comments';
import CommentItem from './CommentItem.vue';

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

interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

interface PaginatedComments {
  data: Comment[];
  prev_page_url: string | null; // kept for backward-compat, not used in UI
  next_page_url: string | null; // kept for backward-compat, not used in UI
  current_page: number;
  total: number;
  links: PaginationLink[];
}

function formatLinkLabel(label: string): string {
  // Normalize HTML entities and translate to Czech
  const text = label
    .replace(/&laquo;|«/g, '«')
    .replace(/&raquo;|»/g, '»')
    .replace(/&amp;laquo;|&amp;raquo;/g, (m) => (m.includes('laquo') ? '«' : '»'))
    .trim();

  if (/(^|\s)Previous(\s|$)/i.test(text) || text === '«') {
    return '« Předchozí';
  }
  if (/(^|\s)Next(\s|$)/i.test(text) || text === '»') {
    return 'Další »';
  }

  return text;
}

const props = defineProps<{
  postId: number;
  comments: PaginatedComments;
  canRegister?: boolean;
}>();

const page = usePage();
const user = computed(() => page.props.auth.user as User | null);

const form = useForm({
  content: '',
  parent_id: null as number | null,
});

const isPreview = ref(false);

watch(isPreview, (value) => {
    if (value) {
        nextTick(() => {
            Prism.highlightAll();
        });
    }
});

const htmlPreview = computed(() => {
    const rawHtml = marked.parse(form.content || '', {
        gfm: true,
        breaks: true,
    });
    return DOMPurify.sanitize(rawHtml as string);
});

const submitComment = () => {
  form.post(store(props.postId).url, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      isPreview.value = false;
      nextTick(() => {
          Prism.highlightAll();
      });
    },
  });
};

onMounted(() => {
    Prism.highlightAll();
});
</script>

<template>
  <section id="comments" class="mt-16 border-t border-[#19140015] pt-10 dark:border-[#2a2a27]">
    <h2 class="text-2xl font-semibold mb-8">Komentáře ({{ comments.total }})</h2>

    <!-- Comment Form -->
    <div v-if="user" class="mb-12">
      <div class="flex items-start gap-4">
        <div
          class="size-10 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-bold text-zinc-500 dark:text-zinc-400 ring-1 ring-black/5 dark:ring-white/10 shrink-0"
        >
          {{ user.initials }}
        </div>

        <div class="flex-1">
          <div class="mb-2 flex items-center justify-end">
            <div class="flex gap-2">
                <button
                    type="button"
                    @click="isPreview = false"
                    :class="['text-xs px-2 py-1 rounded', !isPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                >
                    Napsat
                </button>
                <button
                    type="button"
                    @click="isPreview = true"
                    :class="['text-xs px-2 py-1 rounded', isPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                >
                    Náhled
                </button>
            </div>
          </div>

          <div v-if="!isPreview">
            <textarea
                v-model="form.content"
                rows="4"
                placeholder="Připojte se k diskuzi... (Markdown podporován)"
                class="w-full rounded-xl border-[#19140015] bg-white p-4 text-sm focus:border-[#f53003] focus:ring-[#f53003] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#FF4433] dark:focus:ring-[#FF4433]"
                :disabled="form.processing"
            ></textarea>
            <div v-if="form.errors.content" class="mt-1 text-xs text-red-500">{{ form.errors.content }}</div>
          </div>
          <div v-else class="min-h-[114px] rounded-xl border border-[#19140015] bg-[#19140005] p-4 dark:border-[#3E3E3A] dark:bg-[#ffffff05]">
             <article class="prose prose-sm prose-zinc dark:prose-invert max-w-none prose-pre:bg-transparent prose-pre:p-0">
                <div v-if="form.content" v-html="htmlPreview"></div>
                <div v-else class="text-zinc-400 italic">Nic k náhledu</div>
             </article>
          </div>

          <div class="mt-3 flex justify-end">
            <button
                @click="submitComment"
                :disabled="form.processing || !form.content.trim()"
                class="rounded-full bg-[#f53003] px-6 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-[#FF4433]"
            >
              {{ form.processing ? 'Odesílám...' : 'Odeslat komentář' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="mb-12 rounded-2xl bg-[#fff2f2] p-8 text-center dark:bg-[#1D0002]">
      <p class="mb-4 text-zinc-700 dark:text-zinc-300">Přihlaste se, abyste se mohli zapojit do diskuze a zanechat komentář.</p>
      <div class="flex items-center justify-center gap-4">
        <Link :href="login()" class="font-medium text-[#f53003] hover:underline dark:text-[#FF4433]">Přihlásit se</Link>
        <span v-if="canRegister" class="text-zinc-400">nebo</span>
        <Link v-if="canRegister" :href="register()" class="font-medium text-[#f53003] hover:underline dark:text-[#FF4433]">Registrovat se</Link>
      </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-8">
      <CommentItem
        v-for="comment in comments.data"
        :key="comment.id"
        :comment="comment"
        :post-id="postId"
      />
      <div v-if="comments.data.length === 0" class="py-10 text-center text-zinc-500">
        Zatím žádné komentáře. Buďte první, kdo se podělí o své myšlenky!
      </div>

      <!-- Pagination Bottom (standard) -->
      <nav v-if="comments.links && comments.links.length > 0" class="mt-8 flex items-center justify-center gap-1" aria-label="Stránkování komentářů">
        <template v-for="(link, i) in comments.links" :key="i">
          <Link
            v-if="link.url"
            :href="link.url + '#comments'"
            preserve-scroll
            :class="[
              'rounded-md px-3 py-1.5 text-sm transition-colors',
              link.active
                ? 'bg-[#f53003] text-white dark:bg-[#FF4433]'
                : 'text-zinc-600 hover:bg-[#19140008] dark:text-[#A1A09A] dark:hover:bg-[#ffffff0a]'
            ]"
          >
            <span v-html="formatLinkLabel(link.label)" />
          </Link>
          <span
            v-else
            class="rounded-md px-3 py-1.5 text-sm text-zinc-400 dark:text-zinc-600"
            v-html="formatLinkLabel(link.label)"
          />
        </template>
      </nav>
    </div>
  </section>
</template>
