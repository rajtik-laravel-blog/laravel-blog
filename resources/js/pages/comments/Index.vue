<script setup lang="ts">
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { Edit2, Eye, Trash2, Calendar, FileText } from 'lucide-vue-next';
import { ref, watch, computed } from 'vue';

import { Button } from '@/components/ui/button';
import { useMarkdown } from '@/composables/useMarkdown';
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

const { renderMarkdown, highlightCode } = useMarkdown();

defineProps<{
    comments: PaginatedComments;
}>();

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
        highlightCode();
    }
});

const htmlEditPreview = computed(() => renderMarkdown(editForm.content || ''));

const updateComment = (id: number) => {
    editForm.put(update(id).url, {
        preserveScroll: true,
        onSuccess: () => {
            editingId.value = null;
            isEditPreview.value = false;
            highlightCode();
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
        <main class="mx-auto max-w-7xl py-8">
            <div class="mb-8 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight">Moje komentáře</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Přehled a správa všech vašich komentářů.</p>
            </div>

            <div class="mx-4 overflow-hidden rounded-xl border border-[#19140015] bg-white shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615] sm:mx-6 lg:mx-8">
                <!-- Desktop Table View -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-[#19140015] bg-[#19140005] dark:border-[#3E3E3A] dark:bg-[#ffffff05]">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Komentář</th>
                                <th class="px-6 py-4 font-semibold">U článku</th>
                                <th class="px-6 py-4 font-semibold md:table-cell hidden">Datum</th>
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
                                <td class="px-6 py-4">
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
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="block sm:hidden divide-y divide-[#19140015] dark:divide-[#3E3E3A]">
                    <div v-for="comment in comments.data" :key="comment.id" class="p-4 space-y-4">
                        <div v-if="editingId === comment.id">
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
                                    rows="4"
                                    class="w-full rounded-lg border-zinc-200 bg-transparent p-3 text-sm focus:border-[#f53003] focus:ring-[#f53003] dark:border-zinc-800"
                                ></textarea>
                            </div>
                            <div v-else class="min-h-[86px] rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-900/50">
                                <article class="prose prose-xs prose-zinc dark:prose-invert max-w-none prose-pre:bg-transparent prose-pre:p-0">
                                    <div v-if="editForm.content" v-html="htmlEditPreview"></div>
                                    <div v-else class="italic text-zinc-400">Nic k náhledu</div>
                                </article>
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <Button variant="ghost" size="sm" @click="cancelEdit">Zrušit</Button>
                                <Button size="sm" @click="updateComment(comment.id)" :disabled="editForm.processing">Uložit</Button>
                            </div>
                        </div>
                        <div v-else class="space-y-3">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            <FileText class="size-3" />
                                            <span>Článek</span>
                                        </div>
                                        <Link :href="showPost(comment.post.slug).url" class="text-sm font-semibold text-[#f53003] hover:underline dark:text-[#FF4433] line-clamp-1">
                                            {{ comment.post.title }}
                                        </Link>
                                    </div>
                                    <div class="h-8 w-px bg-zinc-200 dark:bg-zinc-800 self-end mb-0.5"></div>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5 text-[11px] font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                            <Calendar class="size-3" />
                                            <span>Datum</span>
                                        </div>
                                        <div class="text-[12px] text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                                            {{ formatDate(comment.created_at) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-lg bg-[#19140003] p-3 dark:bg-[#ffffff03] border border-[#19140008] dark:border-[#ffffff08]">
                                <article class="prose prose-xs prose-zinc dark:prose-invert max-w-none line-clamp-4 prose-pre:hidden">
                                    <div v-html="renderMarkdown(comment.content)"></div>
                                </article>
                            </div>

                            <div class="flex items-center justify-start gap-1">
                                <Button variant="ghost" size="sm" as-child class="h-8 px-2">
                                    <a :href="showPost(comment.post.slug).url" class="flex items-center gap-2 text-xs">
                                        <Eye class="size-3.5" />
                                        <span>Zobrazit</span>
                                    </a>
                                </Button>
                                <Button variant="ghost" size="sm" @click="startEdit(comment)" class="h-8 px-2 text-zinc-600 dark:text-zinc-400">
                                    <Edit2 class="size-3.5" />
                                    <span class="ml-2 text-xs">Upravit</span>
                                </Button>
                                <Button variant="ghost" size="sm" @click="deleteComment(comment.id)" class="h-8 px-2 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <Trash2 class="size-3.5" />
                                    <span class="ml-2 text-xs">Smazat</span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="comments.data.length === 0" class="px-6 py-12 text-center text-zinc-500 border-b border-[#19140015] dark:border-[#3E3E3A]">
                    Zatím jste nenapsali žádný komentář.
                </div>

                <!-- Pagination -->
                <div v-if="comments.links && comments.links.length > 3" class="border-t border-[#19140015] px-6 py-4 dark:border-[#3E3E3A]">
                    <!-- Desktop Pagination -->
                    <nav class="hidden sm:flex items-center justify-center gap-2">
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

                    <!-- Mobile Pagination -->
                    <nav class="flex sm:hidden items-center justify-between gap-2">
                        <Link
                            :href="comments.links[0].url || ''"
                            class="flex-1 px-4 py-2 text-center text-sm font-medium rounded-lg border border-[#19140015] dark:border-[#3E3E3A] transition-colors"
                            :class="[
                                !comments.links[0].url ? 'opacity-50 cursor-not-allowed bg-zinc-50 dark:bg-zinc-900/50' : 'hover:bg-zinc-50 dark:hover:bg-zinc-900'
                            ]"
                        >
                            <span v-html="comments.links[0].label"></span>
                        </Link>
                        <div class="px-4 text-xs font-medium text-zinc-500">
                            {{ comments.current_page }} / {{ comments.last_page }}
                        </div>
                        <Link
                            :href="comments.links[comments.links.length - 1].url || ''"
                            class="flex-1 px-4 py-2 text-center text-sm font-medium rounded-lg border border-[#19140015] dark:border-[#3E3E3A] transition-colors"
                            :class="[
                                !comments.links[comments.links.length - 1].url ? 'opacity-50 cursor-not-allowed bg-zinc-50 dark:bg-zinc-900/50' : 'hover:bg-zinc-50 dark:hover:bg-zinc-900'
                            ]"
                        >
                            <span v-html="comments.links[comments.links.length - 1].label"></span>
                        </Link>
                    </nav>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
