<script setup lang="ts">
import { computed, ref, watch, onMounted, onUnmounted } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useMarkdown } from '@/composables/useMarkdown';

interface Tag {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    form: any;
    allTags: Tag[];
    submitLabel: string;
}>();

const emit = defineEmits(['submit']);

const { renderMarkdown } = useMarkdown();

const imagePreview = ref<string | null>(props.form.image_url || null);

watch(() => props.form.image_url, (newUrl) => {
    if (!props.form.image) {
        imagePreview.value = newUrl || null;
    }
});
const showPreview = ref(false);
const imageInput = ref<HTMLInputElement | null>(null);

const handleImageChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        emit('update:image', file);
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    imagePreview.value = null;
    emit('update:image', null);
};

const tagInput = ref('');
const isDropdownOpen = ref(false);
const dropdownRef = ref<HTMLElement | null>(null);

const maybeAddPendingTag = () => {
    if (tagInput.value.trim()) {
        addTag(tagInput.value);
    }
};

const filteredTags = computed(() => {
    const input = tagInput.value.toLowerCase().trim();
    if (!input) {
        return props.allTags.filter(t => !props.form.tags.includes(t.name)).slice(0, 10);
    }
    return props.allTags.filter(t =>
        t.name.toLowerCase().includes(input) && !props.form.tags.includes(t.name)
    );
});

const addTag = (tagName: string) => {
    const tag = tagName.trim().replace(/^#/, '');
    if (tag && !props.form.tags.includes(tag)) {
        const newTags = [...props.form.tags, tag];
        emit('update:tags', newTags);
        tagInput.value = '';
        isDropdownOpen.value = false;
    }
};

const removeTag = (index: number) => {
    const newTags = [...props.form.tags];
    newTags.splice(index, 1);
    emit('update:tags', newTags);
};

const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter' || e.key === 'Tab' || e.key === ',') {
        e.preventDefault();
        maybeAddPendingTag();
    } else if (e.key === 'Backspace' && !tagInput.value && props.form.tags.length > 0) {
        removeTag(props.form.tags.length - 1);
    } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        isDropdownOpen.value = true;
    } else if (e.key === 'Escape') {
        isDropdownOpen.value = false;
    }
};

const handleClickOutside = (e: MouseEvent) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
        maybeAddPendingTag();
        isDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});

const htmlContent = ref('');

const updateHtmlContent = async () => {
    htmlContent.value = await renderMarkdown(props.form.content || '');
};

watch(() => props.form.content, async () => {
    if (showPreview.value) {
        await updateHtmlContent();
    }
});

const togglePreview = async () => {
    showPreview.value = !showPreview.value;
    if (showPreview.value) {
        await updateHtmlContent();
    }
};
</script>

<template>
    <div class="min-w-5xl mb-8 flex items-center justify-between">
        <div>
            <slot name="header"></slot>
        </div>
        <div class="flex gap-3">
            <Button variant="outline" @click="togglePreview">
                {{ showPreview ? 'Upravit' : 'Náhled' }}
            </Button>
            <Button @click="$emit('submit')" :disabled="form.processing">
                {{ submitLabel }}
            </Button>
        </div>
    </div>

    <div v-if="!showPreview" class="grid gap-8">
        <!-- Title & Excerpt -->
        <div class="rounded-xl border border-[#19140015] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="title">Nadpis</Label>
                    <Input
                        id="title"
                        :model-value="form.title"
                        @update:model-value="$emit('update:title', $event)"
                        placeholder="Zadejte název článku..."
                        :class="{ 'border-red-500': form.errors.title }"
                    />
                    <p v-if="form.errors.title" class="text-xs text-red-500">{{ form.errors.title }}</p>
                </div>

                <div class="grid gap-2">
                    <Label for="excerpt">Stručný výtah (nepovinné)</Label>
                    <Textarea
                        id="excerpt"
                        :model-value="form.excerpt"
                        @update:model-value="$emit('update:excerpt', $event)"
                        placeholder="Krátký popis, který se zobrazí v seznamu článků..."
                        rows="2"
                        :class="{ 'border-red-500': form.errors.excerpt }"
                    />
                    <p v-if="form.errors.excerpt" class="text-xs text-red-500">{{ form.errors.excerpt }}</p>
                </div>
            </div>
        </div>

        <!-- Image Upload -->
        <div class="rounded-xl border border-[#19140015] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
            <Label class="mb-4 block">Hlavní obrázek</Label>
            <div class="flex flex-col items-center gap-6 sm:flex-row">
                <div v-if="imagePreview" class="relative aspect-video w-full overflow-hidden rounded-lg border border-[#19140015] dark:border-[#3E3E3A] sm:w-48">
                    <img :src="imagePreview" class="h-full w-full object-cover" />
                    <button
                        type="button"
                        @click="removeImage"
                        class="absolute right-2 top-2 rounded-full bg-black/50 p-1 text-white hover:bg-black/70"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div v-else class="flex aspect-video w-full items-center justify-center rounded-lg border-2 border-dashed border-[#19140015] dark:border-[#3E3E3A] sm:w-48">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <input
                        ref="imageInput"
                        type="file"
                        id="image"
                        class="hidden"
                        accept="image/*"
                        @change="handleImageChange"
                    />
                    <Button variant="outline" type="button" @click="imageInput?.click()">
                        {{ imagePreview ? 'Změnit obrázek' : 'Vybrat obrázek' }}
                    </Button>
                    <p class="mt-2 text-xs text-zinc-500">Doporučený poměr stran 21:9. Maximálně 2MB.</p>
                    <p v-if="form.errors.image" class="mt-1 text-xs text-red-500">{{ form.errors.image }}</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="rounded-xl border border-[#19140015] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="content">Obsah článku (Markdown)</Label>
                    <span class="text-[10px] text-zinc-400 uppercase tracking-widest">Podporuje Markdown</span>
                </div>
                <Textarea
                    id="content"
                    :model-value="form.content"
                    @update:model-value="$emit('update:content', $event)"
                    placeholder="Zde napište svůj článek..."
                    rows="15"
                    class="font-mono text-sm leading-relaxed"
                    :class="{ 'border-red-500': form.errors.content }"
                />
                <p v-if="form.errors.content" class="text-xs text-red-500">{{ form.errors.content }}</p>
            </div>
        </div>

        <!-- Tags -->
        <div class="rounded-xl border border-[#19140015] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
            <Label class="mb-4 block">Štítky</Label>

            <div
                ref="dropdownRef"
                class="relative flex flex-wrap gap-2 rounded-md border border-[#19140015] bg-transparent p-2 focus-within:ring-2 focus-within:ring-[#f53003] dark:border-[#3E3E3A]"
            >
                <Badge
                    v-for="(tag, index) in form.tags"
                    :key="index"
                    variant="secondary"
                    class="flex items-center gap-1 bg-[#f53003]/10 text-[#f53003] dark:bg-[#FF4433]/10 dark:text-[#FF4433]"
                >
                    #{{ tag }}
                    <button type="button" @click="removeTag(index)" class="ml-1 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </Badge>

                <input
                    v-model="tagInput"
                    placeholder="Přidat štítek..."
                    class="flex-1 min-w-[120px] bg-transparent text-sm outline-none placeholder:text-zinc-400"
                    @keydown="handleKeydown"
                    @focus="isDropdownOpen = true"
                    @blur="maybeAddPendingTag"
                />

                <!-- Autocomplete Dropdown -->
                <div
                    v-if="isDropdownOpen && filteredTags.length > 0"
                    class="absolute left-0 top-full z-50 mt-1 w-full rounded-md border border-[#19140015] bg-white p-1 shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]"
                >
                    <button
                        v-for="tag in filteredTags"
                        :key="tag.id"
                        type="button"
                        class="w-full rounded-sm px-3 py-2 text-left text-sm hover:bg-zinc-100 dark:hover:bg-zinc-800"
                        @click="addTag(tag.name)"
                    >
                        #{{ tag.name }}
                    </button>
                </div>
            </div>
            <p class="mt-2 text-[10px] text-zinc-400">Zadejte název štítku a stiskněte Enter, nebo vyberte z existujících.</p>
        </div>
    </div>

    <!-- Preview -->
    <div v-else class="animate-in fade-in slide-in-from-bottom-4 duration-300">
        <div class="mx-auto w-full max-w-7xl overflow-hidden rounded-2xl border border-[#19140015] bg-white shadow-xl dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:shadow-red-900/10">
            <img
                :src="imagePreview || '/images/placeholder.jpg'"
                class="aspect-[21/9] w-full object-cover"
            />

            <div class="px-6 py-12 sm:px-12">
                <h1 class="text-3xl font-bold sm:text-4xl">{{ form.title || 'Bez názvu' }}</h1>

                <div class="mt-4 flex flex-wrap gap-2">
                    <span v-for="(tag, index) in form.tags" :key="index" class="text-xs text-[#f53003] dark:text-[#FF4433]">
                        #{{ tag }}
                    </span>
                </div>

                <article class="mt-8 max-w-none">
                    <div v-html="htmlContent" />
                </article>
            </div>
        </div>
    </div>
</template>
