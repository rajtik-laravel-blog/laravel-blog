<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { Menu, Search, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppearanceDropdown from '@/components/AppearanceDropdown.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { dashboard, login, register, home, tags } from '@/routes';

defineProps<{
    canRegister?: boolean;
}>();

const page = usePage();

const isTagsActive = computed(() => page.url.startsWith('/tags'));
const isHomeActive = computed(() => page.url === '/' || (page.url.startsWith('/?') && !page.url.includes('tag=')));

const searchQuery = ref((page.props.search as string) || '');
let searchTimeout: ReturnType<typeof setTimeout>;

const handleSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(home().url, { search: searchQuery.value }, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

const clearSearch = () => {
    clearTimeout(searchTimeout);
    searchQuery.value = '';
    router.get(home().url, { search: '' }, {
        preserveState: true,
        replace: true
    });
};
</script>

<template>
    <header class="sticky top-0 z-40 border-b border-[#19140015] bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:border-[#2a2a27] dark:bg-[#0a0a0acc]">
        <div class="mx-auto max-w-7xl px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <Link :href="home().url" class="flex items-center gap-3 group">
                    <div class="flex size-9 items-center justify-center rounded-md bg-[#f53003] text-white dark:bg-[#FF4433] transition-transform group-hover:scale-105">
                        <AppLogoIcon class="size-5" />
                    </div>
                    <span class="hidden text-sm font-semibold leading-none sm:block text-[#1b1b18] dark:text-[#EDEDEC] text-nowrap">Laravel Blog</span>
                </Link>

                <!-- Main Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link
                        :href="home().url"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-md transition-colors',
                            isHomeActive ? 'text-[#f53003] dark:text-[#FF4433] bg-[#f53003]/5 dark:bg-[#FF4433]/10' : 'text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]'
                        ]"
                    >
                        Články
                    </Link>
                    <Link
                        :href="tags().url"
                        :class="[
                            'px-3 py-1.5 text-sm font-medium rounded-md transition-colors',
                            isTagsActive ? 'text-[#f53003] dark:text-[#FF4433] bg-[#f53003]/5 dark:bg-[#FF4433]/10' : 'text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]'
                        ]"
                    >
                        Štítky
                    </Link>
                </nav>

                <!-- Search Input Desktop -->
                <div class="hidden lg:flex items-center relative max-w-xs w-full">
                    <Search class="absolute left-3 size-4 text-[#706f6c] dark:text-[#A1A09A]" />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Hledat články..."
                        class="w-full rounded-full bg-[#19140008] dark:bg-[#ffffff0a] border-none pl-9 pr-10 py-1.5 text-sm focus:ring-1 focus:ring-[#f53003] dark:focus:ring-[#FF4433] text-[#1b1b18] dark:text-[#EDEDEC] placeholder:text-[#706f6c]/60 dark:placeholder:text-[#A1A09A]/60"
                        @input="handleSearch"
                    />
                    <button
                        v-if="searchQuery"
                        class="absolute right-3 p-1 rounded-full hover:bg-black/5 dark:hover:bg-white/10 text-[#706f6c] dark:text-[#A1A09A] transition-colors"
                        @click="clearSearch"
                    >
                        <X class="size-3" />
                    </button>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-2 text-sm">
                <AppearanceDropdown />
                <template v-if="page.props.auth.user">
                    <Link
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-md border border-transparent px-3 py-1.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#19140035] dark:hover:border-[#3E3E3A]"
                    >
                        <div class="size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-500 dark:text-zinc-400 ring-1 ring-black/5 dark:ring-white/10">
                            {{ page.props.auth.user.initials }}
                        </div>
                        <span>Nástěnka</span>
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-flex items-center rounded-md border border-transparent px-3 py-1.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#19140035] dark:hover:border-[#3E3E3A]"
                    >
                        Přihlásit se
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-flex items-center rounded-md border border-[#19140035] px-3 py-1.5 text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                    >
                        Registrovat se
                    </Link>
                </template>
            </nav>

            <!-- Mobile Navigation -->
            <div class="flex items-center gap-2 md:hidden">
                <AppearanceDropdown />
                <Sheet>
                    <SheetTrigger as-child>
                        <Button variant="ghost" size="icon" class="md:hidden">
                            <Menu class="size-5" />
                            <span class="sr-only">Přepnout menu</span>
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="right" class="w-64 bg-white dark:bg-[#0a0a0a] border-l border-[#19140015] dark:border-[#2a2a27]">
                        <SheetHeader class="px-2 text-left">
                            <SheetTitle class="flex items-center gap-3">
                                <div class="flex size-8 items-center justify-center rounded-md bg-[#f53003] text-white dark:bg-[#FF4433]">
                                    <AppLogoIcon class="size-4" />
                                </div>
                                <span class="text-sm font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Laravel Blog</span>
                            </SheetTitle>
                        </SheetHeader>
                        <div class="mt-6 flex flex-col gap-4 px-2">
                            <!-- Mobile Search -->
                            <div class="relative flex items-center mb-2">
                                <Search class="absolute left-3 size-4 text-[#706f6c] dark:text-[#A1A09A]" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Hledat..."
                                    class="w-full rounded-lg bg-[#19140008] dark:bg-[#ffffff0a] border-none pl-9 pr-10 py-2 text-sm focus:ring-1 focus:ring-[#f53003] dark:focus:ring-[#FF4433] text-[#1b1b18] dark:text-[#EDEDEC]"
                                    @input="handleSearch"
                                />
                                <button
                                    v-if="searchQuery"
                                    class="absolute right-3 p-1 rounded-full hover:bg-black/5 dark:hover:bg-white/10 text-[#706f6c] dark:text-[#A1A09A] transition-colors"
                                    @click="clearSearch"
                                >
                                    <X class="size-3" />
                                </button>
                            </div>

                            <Link
                                :href="home().url"
                                class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#f53003] dark:hover:text-[#FF4433]"
                                :class="{ 'text-[#f53003] dark:text-[#FF4433]': isHomeActive }"
                            >
                                Články
                            </Link>
                            <Link
                                :href="tags().url"
                                class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#f53003] dark:hover:text-[#FF4433]"
                                :class="{ 'text-[#f53003] dark:text-[#FF4433]': isTagsActive }"
                            >
                                Štítky
                            </Link>
                            <hr class="border-[#19140015] dark:border-[#2a2a27]" />
                            <Link
                                v-if="page.props.auth.user"
                                :href="dashboard()"
                                class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#f53003] dark:hover:text-[#FF4433]"
                            >
                                Nástěnka
                            </Link>
                            <template v-else>
                                <Link
                                    :href="login()"
                                    class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#f53003] dark:hover:text-[#FF4433]"
>
                                Přihlásit se
                                </Link>
                                <Link
                                    v-if="canRegister"
                                    :href="register()"
                                    class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#f53003] dark:hover:text-[#FF4433]"
>
                                Registrovat se
                                </Link>
                            </template>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>
        </div>
    </header>
</template>
