<script setup lang="ts">
import { useForm, usePage, router } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import { marked } from 'marked';
import Prism from 'prismjs';
import { computed, nextTick, ref, watch } from 'vue';
import { store, update, destroy } from '@/routes/comments';

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

const props = defineProps<{
  comment: Comment;
  postId: number;
  depth?: number;
}>();

const page = usePage();
const currentUser = computed(() => page.props.auth.user as User | null);

const isReplying = ref(false);
const isEditing = ref(false);
const isPreview = ref(false);
const isEditPreview = ref(false);

watch(isPreview, (value) => {
    if (value) {
        nextTick(() => {
            Prism.highlightAll();
        });
    }
});

watch(isEditPreview, (value) => {
    if (value) {
        nextTick(() => {
            Prism.highlightAll();
        });
    }
});

const form = useForm({
  content: '',
  parent_id: props.comment.id,
});

const editForm = useForm({
  content: props.comment.content,
});

const htmlContent = computed(() => {
  const raw = marked.parse(props.comment.content || '', {
    gfm: true,
    breaks: true,
  });
  return DOMPurify.sanitize(raw as string);
});

const htmlPreview = computed(() => {
  const raw = marked.parse(form.content || '', {
    gfm: true,
    breaks: true,
  });
  return DOMPurify.sanitize(raw as string);
});

const htmlEditPreview = computed(() => {
  const raw = marked.parse(editForm.content || '', {
    gfm: true,
    breaks: true,
  });
  return DOMPurify.sanitize(raw as string);
});

const formattedDate = computed(() => props.comment.created_at_formatted);

const submitReply = () => {
  form.post(store(props.postId).url, {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      isReplying.value = false;
      isPreview.value = false;
      nextTick(() => {
        Prism.highlightAll();
      });
    },
  });
};

const updateComment = () => {
  editForm.put(update(props.comment.id).url, {
    preserveScroll: true,
    onSuccess: () => {
      isEditing.value = false;
      isEditPreview.value = false;
      nextTick(() => {
        Prism.highlightAll();
      });
    },
  });
};

const deleteComment = () => {
  if (confirm('Opravdu chcete smazat tento komentář?')) {
    router.delete(destroy(props.comment.id).url, {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <div :class="['flex gap-4 items-start', depth && depth > 0 ? 'mt-6' : '']">
    <div class="relative group mt-0.5">
      <div
        class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-500 dark:text-zinc-400 ring-1 ring-black/5 dark:ring-white/10 shrink-0"
      >
        {{ comment.user.initials }}
      </div>

      <!-- Hover Tooltip for Name -->
      <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-10">
        <div class="bg-zinc-900 text-white text-[10px] py-1 px-2 rounded shadow-lg whitespace-nowrap">
          {{ comment.user.name }}
        </div>
      </div>
    </div>

    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2 mb-1 h-8">
        <span class="text-xs text-zinc-500">{{ formattedDate }}</span>
      </div>

      <article v-if="!isEditing" class="prose prose-sm prose-zinc dark:prose-invert max-w-none prose-pre:bg-transparent prose-pre:p-0">
        <div v-html="htmlContent"></div>
      </article>

      <!-- Edit Form -->
      <div v-else class="mt-2">
        <div class="flex items-center justify-between mb-2">
           <div class="flex gap-2">
                <button
                    type="button"
                    @click="isEditPreview = false"
                    :class="['text-[10px] px-2 py-0.5 rounded', !isEditPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                >
                    Napsat
                </button>
                <button
                    type="button"
                    @click="isEditPreview = true"
                    :class="['text-[10px] px-2 py-0.5 rounded', isEditPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                >
                    Náhled
                </button>
            </div>
        </div>

        <div v-if="!isEditPreview">
          <textarea
            v-model="editForm.content"
            rows="3"
            class="w-full rounded-lg border-[#19140015] bg-white p-3 text-sm focus:border-[#f53003] focus:ring-[#f53003] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#FF4433] dark:focus:ring-[#FF4433]"
          ></textarea>
        </div>
        <div v-else class="min-h-[86px] rounded-lg border border-[#19140015] bg-[#19140005] p-3 dark:border-[#3E3E3A] dark:bg-[#ffffff05]">
           <article class="prose prose-xs prose-zinc dark:prose-invert max-w-none prose-pre:bg-transparent prose-pre:p-0">
              <div v-if="editForm.content" v-html="htmlEditPreview"></div>
              <div v-else class="text-zinc-400 italic">Nic k náhledu</div>
           </article>
        </div>

        <div class="mt-2 flex justify-end gap-2">
          <button
            @click="isEditing = false"
            class="px-3 py-1 text-xs font-medium text-zinc-500 hover:text-zinc-700"
          >
            Zrušit
          </button>
          <button
            @click="updateComment"
            :disabled="editForm.processing || !editForm.content.trim()"
            class="rounded-full bg-[#f53003] px-4 py-1 text-xs font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-[#FF4433]"
          >
            Uložit změny
          </button>
        </div>
      </div>

      <div v-if="currentUser" class="mt-2 flex items-center gap-3">
        <button
          v-if="currentUser.id !== comment.user.id"
          @click="isReplying = !isReplying; isEditing = false"
          class="text-xs font-medium text-zinc-500 hover:text-[#f53003] dark:hover:text-[#FF4433] transition-colors"
        >
          {{ isReplying ? 'Zrušit' : 'Odpovědět' }}
        </button>
        <template v-if="currentUser.id === comment.user.id && !isEditing">
          <span class="text-zinc-300 dark:text-zinc-700">•</span>
          <button
            @click="isEditing = true; isReplying = false"
            class="text-xs font-medium text-zinc-500 hover:text-[#f53003] dark:hover:text-[#FF4433] transition-colors"
          >
            Upravit
          </button>
          <span class="text-zinc-300 dark:text-zinc-700">•</span>
          <button
            @click="deleteComment"
            class="text-xs font-medium text-zinc-500 hover:text-red-600 dark:hover:text-red-400 transition-colors"
          >
            Smazat
          </button>
        </template>
      </div>

      <!-- Reply Form -->
      <div v-if="isReplying" class="mt-4">
        <div class="flex items-center justify-between mb-2">
           <div class="flex gap-2">
                <button
                    type="button"
                    @click="isPreview = false"
                    :class="['text-[10px] px-2 py-0.5 rounded', !isPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                >
                    Napsat
                </button>
                <button
                    type="button"
                    @click="isPreview = true"
                    :class="['text-[10px] px-2 py-0.5 rounded', isPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                >
                    Náhled
                </button>
            </div>
        </div>

        <div v-if="!isPreview">
          <textarea
            v-model="form.content"
            rows="3"
            placeholder="Napište odpověď..."
            class="w-full rounded-lg border-[#19140015] bg-white p-3 text-sm focus:border-[#f53003] focus:ring-[#f53003] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#FF4433] dark:focus:ring-[#FF4433]"
          ></textarea>
        </div>
        <div v-else class="min-h-[86px] rounded-lg border border-[#19140015] bg-[#19140005] p-3 dark:border-[#3E3E3A] dark:bg-[#ffffff05]">
           <article class="prose prose-xs prose-zinc dark:prose-invert max-w-none prose-pre:bg-transparent prose-pre:p-0">
              <div v-if="form.content" v-html="htmlPreview"></div>
              <div v-else class="text-zinc-400 italic">Nic k náhledu</div>
           </article>
        </div>

        <div class="mt-2 flex justify-end gap-2">
          <button
            @click="isReplying = false"
            class="px-3 py-1 text-xs font-medium text-zinc-500 hover:text-zinc-700"
          >
            Zrušit
          </button>
          <button
            @click="submitReply"
            :disabled="form.processing || !form.content.trim()"
            class="rounded-full bg-[#f53003] px-4 py-1 text-xs font-medium text-white transition-opacity hover:opacity-90 disabled:opacity-50 dark:bg-[#FF4433]"
          >
            Odeslat odpověď
          </button>
        </div>
      </div>

      <!-- Nested Replies -->
      <div v-if="comment.replies && comment.replies.length > 0" class="border-l border-[#19140015] dark:border-[#2a2a27] ml-1 pl-4">
        <CommentItem
          v-for="reply in comment.replies"
          :key="reply.id"
          :comment="reply"
          :post-id="postId"
          :depth="(depth || 0) + 1"
        />
      </div>
    </div>
  </div>
</template>
