<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import DOMPurify from 'dompurify';
import { Edit2, Eye, Trash2 } from 'lucide-vue-next';
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
import { ref, nextTick, onMounted, watch, computed } from 'vue';

import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { update, destroy } from '@/routes/comments';
import { show as showPost } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';

interface Post {
    id: number;
    title: string;
    slug: string;
}

interface Comment {
    id: number;
    content: string;
    created_at: string;
    created_at_human: string;
    created_at_formatted: string;
    post: Post;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedComments {
    data: Comment[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
    meta?: any;
}

defineProps<{
    comments: PaginatedComments;
}>();

onMounted(() => {
    Prism.highlightAll();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nástěnka',
        href: dashboard().url,
    },
    {
        title: 'Moje komentáře',
        href: '#',
    },
];

const editingId = ref<number | null>(null);
const editForm = useForm({
    content: '',
});

const startEdit = (comment: Comment) => {
    editingId.value = comment.id;
    editForm.content = comment.content;
    isEditPreview.value = false;
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
    isEditPreview.value = false;
};

const isEditPreview = ref(false);

watch(isEditPreview, (value) => {
    if (value) {
        nextTick(() => {
            Prism.highlightAll();
        });
    }
});

const htmlEditPreview = computed(() => {
    const raw = marked.parse(editForm.content || '', {
        gfm: true,
        breaks: true,
    });
    return DOMPurify.sanitize(raw as string);
});

const updateComment = (id: number) => {
    editForm.put(update(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            isEditPreview.value = false;
            nextTick(() => {
                Prism.highlightAll();
            });
        },
    });
};

const deleteComment = (id: number) => {
    if (confirm('Opravdu chcete smazat tento komentář?')) {
        router.delete(destroy(id).url, {
            preserveScroll: true,
        });
    }
};

const renderMarkdown = (content: string) => {
    const raw = marked.parse(content, {
        gfm: true,
        breaks: true,
    });
    return DOMPurify.sanitize(raw as string);
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('cs-CZ', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Moje komentáře" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight">Moje komentáře</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Přehled a správa všech vašich komentářů.</p>
            </div>

            <div class="overflow-hidden rounded-xl border border-[#19140015] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-[#19140015] bg-[#19140005] dark:border-[#3E3E3A] dark:bg-[#ffffff05]">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Komentář</th>
                                <th class="hidden px-6 py-4 font-semibold sm:table-cell">U článku</th>
                                <th class="hidden px-6 py-4 font-semibold md:table-cell">Datum</th>
                                <th class="px-6 py-4 text-right font-semibold">Akce</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#19140015] dark:divide-[#3E3E3A]">
                            <tr v-for="comment in comments.data" :key="comment.id" class="group hover:bg-[#19140003] dark:hover:bg-[#ffffff03]">
                                <td class="px-6 py-4">
                                    <div v-if="editingId === comment.id" class="min-w-[300px]">
                                        <div class="mb-2 flex gap-2">
                                            <button
                                                type="button"
                                                @click="isEditPreview = false"
                                                :class="['rounded px-2 py-0.5 text-[10px]', !isEditPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                                            >
                                                Napsat
                                            </button>
                                            <button
                                                type="button"
                                                @click="isEditPreview = true"
                                                :class="['rounded px-2 py-0.5 text-[10px]', isEditPreview ? 'bg-[#f53003] text-white' : 'text-zinc-500 hover:text-zinc-700']"
                                            >
                                                Náhled
                                            </button>
                                        </div>

                                        <div v-if="!isEditPreview">
                                            <textarea
                                                v-model="editForm.content"
                                                rows="3"
                                                class="w-full rounded-lg border-zinc-200 bg-transparent p-3 text-sm focus:border-[#f53003] focus:ring-[#f53003] dark:border-zinc-800"
                                            ></textarea>
                                        </div>
                                        <div v-else class="min-h-[86px] rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900/50">
                                            <article class="prose prose-xs prose-zinc dark:prose-invert max-w-none prose-pre:bg-transparent prose-pre:p-0">
                                                <div v-if="editForm.content" v-html="htmlEditPreview"></div>
                                                <div v-else class="italic text-zinc-400">Nic k náhledu</div>
                                            </article>
                                        </div>

                                        <div class="mt-2 flex justify-end gap-2">
                                            <Button variant="ghost" size="sm" @click="cancelEdit">Zrušit</Button>
                                            <Button size="sm" @click="updateComment(comment.id)" :disabled="editForm.processing">Uložit</Button>
                                        </div>
                                    </div>
                                    <div v-else class="max-w-md">
                                        <article class="prose prose-xs prose-zinc dark:prose-invert max-w-none line-clamp-3 prose-pre:hidden">
                                            <div v-html="renderMarkdown(comment.content)"></div>
                                        </article>
                                    </div>
                                </td>
                                <td class="hidden px-6 py-4 sm:table-cell">
                                    <Link :href="showPost(comment.post.slug).url" class="text-[#f53003] hover:underline dark:text-[#FF4433] font-medium">
                                        {{ comment.post.title }}
                                    </Link>
                                </td>
                                <td class="hidden px-6 py-4 text-zinc-500 md:table-cell whitespace-nowrap">
                                    {{ formatDate(comment.created_at) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="icon" as-child title="Zobrazit na webu">
                                            <a :href="showPost(comment.post.slug).url">
                                                <Eye class="size-4" />
                                            </a>
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="startEdit(comment)" title="Upravit">
                                            <Edit2 class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon" @click="deleteComment(comment.id)" title="Smazat" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="comments.data.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-zinc-500">
                                    Zatím jste nenapsali žádný komentář.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="comments.links && comments.links.length > 3" class="border-t border-[#19140015] px-6 py-4 dark:border-[#3E3E3A]">
                    <nav class="flex items-center justify-center gap-2">
                        <Link
                            v-for="link in comments.links"
                            :key="link.label"
                            :href="link.url || ''"
                            class="px-3 py-1 text-sm rounded-md transition-colors"
                            :class="[
                                link.active ? 'bg-[#f53003] text-white' : 'hover:bg-zinc-100 dark:hover:bg-zinc-800',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </nav>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
