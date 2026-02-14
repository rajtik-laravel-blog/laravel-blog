<script lang="ts" setup>
import {Link, router, usePage} from '@inertiajs/vue3';
import {Menu, Monitor, Moon, Search, Sun, X} from 'lucide-vue-next';
import {computed, ref} from 'vue';

import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {Button} from '@/components/ui/button';
import {Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger,} from '@/components/ui/sheet';
import {useAppearance} from '@/composables/useAppearance';
import {home, tags} from '@/routes';

// eslint-disable-next-line @typescript-eslint/no-empty-object-type
defineProps<{}>();

const page = usePage();

const {appearance, updateAppearance} = useAppearance();

const isTagsActive = computed(() => page.url.startsWith('/tags'));
const isHomeActive = computed(() => page.url === '/' || (page.url.startsWith('/?') && !page.url.includes('tag=')));

const searchQuery = ref((page.props.search as string) || '');
let searchTimeout: ReturnType<typeof setTimeout>;

function cycleAppearance() {
    const modes: Array<'light' | 'dark' | 'system'> = ['light', 'dark', 'system'];
    const currentIndex = modes.indexOf(appearance.value);
    const nextIndex = (currentIndex + 1) % modes.length;
    updateAppearance(modes[nextIndex]);
}

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(home().url, {search: searchQuery.value}, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

const clearSearch = () => {
    clearTimeout(searchTimeout);
    searchQuery.value = '';
    router.get(home().url, {search: ''}, {
        preserveState: true,
        replace: true
    });
};
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b border-[#19140015] bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:border-[#2a2a27] dark:bg-[#0a0a0acc]">
        <div class="mx-auto max-w-6xl px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-8 grow">
                <Link :href="home().url" class="flex items-center gap-3 group">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-[#FF2D20] shadow-md shadow-[#FF2D20]/20 transition-transform duration-200 group-hover:scale-105">
                        <AppLogoIcon class="size-5 text-white"/>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                        <span class="text-[#FF2D20]">Laravel</span>Blog
                    </span>
                </Link>
            </div>

            <div class="flex items-center justify-between gap-8 pr-6">
                <!-- Search Input Desktop -->
                <div class="hidden lg:flex items-center relative max-w-xs w-full">
                    <Search class="absolute left-3 size-4 text-gray-400"/>
                    <input
                        v-model="searchQuery"
                        class="w-full rounded-xl bg-gray-100 dark:bg-gray-800 border-none pl-9 pr-10 py-1.5 text-sm focus:ring-2 focus:ring-[#FF2D20]/20 text-gray-900 dark:text-gray-100 placeholder:text-gray-500"
                        placeholder="Hledat články..."
                        type="text"
                        @input="handleSearch"
                    />
                    <button
                        v-if="searchQuery"
                        class="absolute right-3 p-1 rounded-full hover:bg-black/5 dark:hover:bg-white/10 text-gray-400 transition-colors"
                        @click="clearSearch"
                    >
                        <X class="size-3"/>
                    </button>
                </div>

                <!-- Main Nav Links -->
                <nav class="hidden md:flex items-center gap-4">
                    <Link
                        :class="[
                            'text-sm font-medium transition-colors',
                            isHomeActive ? 'text-[#FF2D20]' : 'text-gray-600 hover:text-[#FF2D20] dark:text-gray-400 dark:hover:text-[#FF2D20]'
                        ]"
                        :href="home().url"
                    >
                        Články
                    </Link>
                    <Link
                        :class="[
                            'text-sm font-medium transition-colors',
                            isTagsActive ? 'text-[#FF2D20]' : 'text-gray-600 hover:text-[#FF2D20] dark:text-gray-400 dark:hover:text-[#FF2D20]'
                        ]"
                        :href="tags().url"
                    >
                        Štítky
                    </Link>
                </nav>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-4">
                <!-- Theme Toggle -->
                <button
                    :title="`Theme: ${appearance}`"
                    class="relative flex size-9 items-center justify-center rounded-lg border border-gray-200 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
                    @click="cycleAppearance"
                >
                    <Sun
                        v-if="appearance === 'light'"
                        class="size-4 text-amber-500"
                    />
                    <Moon
                        v-else-if="appearance === 'dark'"
                        class="size-4 text-indigo-400"
                    />
                    <Monitor v-else class="size-4 text-gray-500"/>
                </button>
            </nav>

            <!-- Mobile Navigation -->
            <div class="flex items-center gap-2 md:hidden">
                <button
                    class="relative flex size-9 items-center justify-center rounded-lg border border-gray-200 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
                    @click="cycleAppearance"
                >
                    <Sun
                        v-if="appearance === 'light'"
                        class="size-4 text-amber-500"
                    />
                    <Moon
                        v-else-if="appearance === 'dark'"
                        class="size-4 text-indigo-400"
                    />
                    <Monitor v-else class="size-4 text-gray-500"/>
                </button>
                <Sheet>
                    <SheetTrigger as-child>
                        <Button class="md:hidden" size="icon" variant="ghost">
                            <Menu class="size-5"/>
                            <span class="sr-only">Přepnout menu</span>
                        </Button>
                    </SheetTrigger>
                    <SheetContent class="w-64 bg-white dark:bg-[#0e0e0e] border-l border-gray-200 dark:border-gray-800"
                                  side="right">
                        <SheetHeader class="px-2 text-left">
                            <SheetTitle class="flex items-center gap-3">
                                <div class="flex size-8 items-center justify-center rounded-lg bg-[#FF2D20]">
                                    <AppLogoIcon class="size-4 text-white"/>
                                </div>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">
                                    <span class="text-[#FF2D20]">Laravel</span> Blog
                                </span>
                            </SheetTitle>
                        </SheetHeader>
                        <div class="mt-6 flex flex-col gap-4 px-2">
                            <!-- Mobile Search -->
                            <div class="relative flex items-center mb-2">
                                <Search class="absolute left-3 size-4 text-gray-400"/>
                                <input
                                    v-model="searchQuery"
                                    class="w-full rounded-lg bg-gray-100 dark:bg-gray-800 border-none pl-9 pr-10 py-2 text-sm focus:ring-2 focus:ring-[#FF2D20]/20 text-gray-900 dark:text-gray-100"
                                    placeholder="Hledat..."
                                    type="text"
                                    @input="handleSearch"
                                />
                                <button
                                    v-if="searchQuery"
                                    class="absolute right-3 p-1 rounded-full hover:bg-black/5 dark:hover:bg-white/10 text-gray-400 transition-colors"
                                    @click="clearSearch"
                                >
                                    <X class="size-3"/>
                                </button>
                            </div>

                            <Link
                                :class="isHomeActive ? 'text-[#FF2D20]' : 'text-gray-700 dark:text-gray-300 hover:text-[#FF2D20]'"
                                :href="home().url"
                                class="text-sm font-medium transition-colors"
                            >
                                Články
                            </Link>
                            <Link
                                :class="isTagsActive ? 'text-[#FF2D20]' : 'text-gray-700 dark:text-gray-300 hover:text-[#FF2D20]'"
                                :href="tags().url"
                                class="text-sm font-medium transition-colors"
                            >
                                Štítky
                            </Link>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>
        </div>
    </header>
</template>
