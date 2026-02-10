<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
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
import { ref, nextTick, onMounted, watch, computed } from 'vue';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { update, destroy } from '@/routes/comments';
import { show } from '@/routes/posts';
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
}

defineProps<{
    comments: PaginatedComments;
}>();

onMounted(() => {
    Prism.highlightAll();
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
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

// Lokalizace štítků stránkování pro případy, kdy backend pošle anglické "Previous/Next".
const formatLinkLabel = (label: string): string => {
    return label
        .replace('&laquo; Previous', '&laquo; Předchozí')
        .replace('Previous', 'Předchozí')
        .replace('Next &raquo;', 'Další &raquo;')
        .replace('Next', 'Další');
};
</script>

<template>
    <Head title="Nástěnka" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                <h3 class="mb-1 text-lg font-semibold">Moje komentáře</h3>
                <p class="mb-6 text-xs text-zinc-500">Celkem: {{ comments.total }}</p>

                <!-- Pagination Top -->
                <div v-if="comments.links.length > 3" class="mb-6 flex items-center justify-center gap-2">
                    <template v-for="(link, index) in comments.links" :key="index">
                        <div v-if="link.url === null" class="px-3 py-1 text-sm text-zinc-400 dark:text-zinc-600" v-html="formatLinkLabel(link.label)"></div>
                        <Link
                            v-else
                            :href="link.url"
                            class="rounded-md px-3 py-1 text-sm transition-colors"
                            :class="[
                                link.active
                                    ? 'bg-[#f53003] text-white dark:bg-[#FF4433]'
                                    : 'text-zinc-600 hover:bg-[#19140008] dark:text-[#A1A09A] dark:hover:bg-[#ffffff0a]'
                            ]"
                        >
                            <span v-html="formatLinkLabel(link.label)"></span>
                        </Link>
                    </template>
                </div>

                <div v-if="comments.data.length > 0" class="space-y-6">
                    <div v-for="comment in comments.data" :key="comment.id" class="flex flex-col gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Badge variant="outline" class="text-[10px] uppercase tracking-wider">U článku</Badge>
                                <a :href="show(comment.post.slug).url" class="text-sm font-medium text-[#f53003] hover:underline dark:text-[#FF4433]">
                                    {{ comment.post.title }}
                                </a>
                            </div>
                            <span class="text-xs text-zinc-500">{{ comment.created_at_formatted }}</span>
                        </div>

                        <div v-if="editingId === comment.id" class="mt-2">
                            <div class="mb-4 flex items-center justify-between">
                                <div class="flex gap-2">
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
                                <Button size="sm" @click="updateComment(comment.id)" :disabled="editForm.processing">Uložit změny</Button>
                            </div>
                        </div>
                        <div v-else>
                            <article class="prose prose-sm prose-zinc dark:prose-invert max-w-none prose-pre:bg-zinc-100 dark:prose-pre:bg-zinc-900 prose-pre:p-2">
                                <div v-html="renderMarkdown(comment.content)"></div>
                            </article>

                            <div class="mt-3 flex justify-end gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-900">
                                <Button variant="ghost" size="sm" @click="startEdit(comment)">Upravit</Button>
                                <Button variant="destructive" size="sm" @click="deleteComment(comment.id)">Smazat</Button>
                            </div>
                        </div>
                    </div>
                    <!-- Pagination Bottom -->
                    <div v-if="comments.links.length > 3" class="mt-4 flex items-center justify-center gap-2">
                        <template v-for="(link, index) in comments.links" :key="index">
                            <div v-if="link.url === null" class="px-3 py-1 text-sm text-zinc-400 dark:text-zinc-600" v-html="formatLinkLabel(link.label)"></div>
                            <Link
                                v-else
                                :href="link.url"
                                class="rounded-md px-3 py-1 text-sm transition-colors"
                                :class="[
                                    link.active
                                        ? 'bg-[#f53003] text-white dark:bg-[#FF4433]'
                                        : 'text-zinc-600 hover:bg-[#19140008] dark:text-[#A1A09A] dark:hover:bg-[#ffffff0a]'
                                ]"
                            >
                                <span v-html="link.label"></span>
                            </Link>
                        </template>
                    </div>
                </div>
                <div v-else class="py-12 text-center text-zinc-500">
                    Zatím jste nezanechali žádné komentáře.
                </div>
            </div>
        </div>
    </AppLayout>
</template>
