<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Sun, Moon, Monitor, ArrowRight, Calendar, Clock, Tag, Menu, X, ChevronUp } from 'lucide-vue-next';
import { ref, onMounted, onUnmounted } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const mobileMenuOpen = ref(false);
const scrolled = ref(false);
const showScrollTop = ref(false);

function handleScroll() {
    scrolled.value = window.scrollY > 20;
    showScrollTop.value = window.scrollY > 600;
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

function cycleAppearance() {
    const modes: Array<'light' | 'dark' | 'system'> = ['light', 'dark', 'system'];
    const currentIndex = modes.indexOf(appearance.value);
    const nextIndex = (currentIndex + 1) % modes.length;
    updateAppearance(modes[nextIndex]);
}

const featuredPost = {
    title: 'Building Modern Web Applications with Laravel 12',
    excerpt:
        'Explore the latest features in Laravel 12, from improved routing to the new Blade components that make building web applications a breeze. We dive deep into the architecture decisions that make Laravel the framework of choice for modern PHP development.',
    date: 'Feb 10, 2026',
    readTime: '8 min read',
    category: 'Laravel',
    image: 'https://picsum.photos/seed/laravel-hero/800/450',
};

const posts = [
    {
        title: 'Mastering Vue 3 Composition API',
        excerpt:
            'A comprehensive guide to leveraging the Composition API for cleaner, more maintainable Vue components.',
        date: 'Feb 8, 2026',
        readTime: '6 min read',
        category: 'Vue.js',
        image: 'https://picsum.photos/seed/vue-comp/600/400',
    },
    {
        title: 'TailwindCSS 4: What\'s New',
        excerpt:
            'Discover the groundbreaking features in TailwindCSS 4, including the new engine and CSS-first configuration.',
        date: 'Feb 5, 2026',
        readTime: '5 min read',
        category: 'CSS',
        image: 'https://picsum.photos/seed/tailwind4/600/400',
    },
    {
        title: 'Deploying Laravel Apps with Zero Downtime',
        excerpt:
            'Learn proven strategies for deploying Laravel applications without any interruption to your users.',
        date: 'Feb 1, 2026',
        readTime: '7 min read',
        category: 'DevOps',
        image: 'https://picsum.photos/seed/deploy-lara/600/400',
    },
    {
        title: 'Real-time Features with Laravel Reverb',
        excerpt:
            'Build scalable WebSocket-powered features using Laravel Reverb and Vue 3 for live user experiences.',
        date: 'Jan 28, 2026',
        readTime: '9 min read',
        category: 'Laravel',
        image: 'https://picsum.photos/seed/reverb/600/400',
    },
    {
        title: 'Type-Safe Inertia.js with TypeScript',
        excerpt:
            'Bridge the gap between your Laravel backend and Vue frontend with full type safety using Inertia.js.',
        date: 'Jan 24, 2026',
        readTime: '6 min read',
        category: 'TypeScript',
        image: 'https://picsum.photos/seed/inertia-ts/600/400',
    },
    {
        title: 'The Art of Database Optimization',
        excerpt:
            'From indexing strategies to query optimization — make your Laravel Eloquent queries lightning fast.',
        date: 'Jan 20, 2026',
        readTime: '10 min read',
        category: 'Database',
        image: 'https://picsum.photos/seed/db-optim/600/400',
    },
];

const categories = ['All', 'Laravel', 'Vue.js', 'CSS', 'DevOps', 'TypeScript', 'Database'];
const activeCategory = ref('All');

const filteredPosts = ref(posts);

function filterByCategory(category: string) {
    activeCategory.value = category;
    if (category === 'All') {
        filteredPosts.value = posts;
    } else {
        filteredPosts.value = posts.filter((p) => p.category === category);
    }
}
</script>

<template>
    <Head title="Home">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-white text-gray-900 transition-colors duration-300 dark:bg-[#0e0e0e] dark:text-gray-100">
        <!-- Navbar -->
        <nav
            :class="[
                'fixed top-0 right-0 left-0 z-50 transition-all duration-300',
                scrolled
                    ? 'bg-white/80 shadow-sm backdrop-blur-xl dark:bg-[#0e0e0e]/80'
                    : 'bg-transparent',
            ]"
        >
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <!-- Logo -->
                <Link href="/" class="group flex items-center gap-3">
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-[#FF2D20] shadow-md shadow-[#FF2D20]/20 transition-transform duration-200 group-hover:scale-105"
                    >
                        <AppLogoIcon class="size-5 text-white" />
                    </div>
                    <span class="text-lg font-bold tracking-tight">
                        <span class="text-[#FF2D20]">Laravel</span>
                        <span class="text-gray-900 dark:text-white">Blog</span>
                    </span>
                </Link>

                <!-- Desktop Nav -->
                <div class="hidden items-center gap-8 md:flex">
                    <a
                        href="#"
                        class="text-sm font-medium text-gray-600 transition-colors hover:text-[#FF2D20] dark:text-gray-400 dark:hover:text-[#FF2D20]"
                    >Articles</a>
                    <a
                        href="#"
                        class="text-sm font-medium text-gray-600 transition-colors hover:text-[#FF2D20] dark:text-gray-400 dark:hover:text-[#FF2D20]"
                    >Categories</a>
                    <a
                        href="#"
                        class="text-sm font-medium text-gray-600 transition-colors hover:text-[#FF2D20] dark:text-gray-400 dark:hover:text-[#FF2D20]"
                    >About</a>

                    <!-- Theme Toggle -->
                    <button
                        @click="cycleAppearance"
                        class="relative flex size-9 items-center justify-center rounded-lg border border-gray-200 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
                        :title="`Theme: ${appearance}`"
                    >
                        <Sun
                            v-if="appearance === 'light'"
                            class="size-4 text-amber-500"
                        />
                        <Moon
                            v-else-if="appearance === 'dark'"
                            class="size-4 text-indigo-400"
                        />
                        <Monitor v-else class="size-4 text-gray-500" />
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="flex size-9 items-center justify-center rounded-lg border border-gray-200 md:hidden dark:border-gray-700"
                >
                    <X v-if="mobileMenuOpen" class="size-4" />
                    <Menu v-else class="size-4" />
                </button>
            </div>

            <!-- Mobile Menu -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="mobileMenuOpen"
                    class="border-b border-gray-200 bg-white/95 px-6 pb-4 backdrop-blur-xl md:hidden dark:border-gray-800 dark:bg-[#0e0e0e]/95"
                >
                    <div class="flex flex-col gap-3">
                        <a
                            href="#"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        >Articles</a>
                        <a
                            href="#"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        >Categories</a>
                        <a
                            href="#"
                            class="rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                        >About</a>
                        <div class="flex items-center gap-2 border-t border-gray-200 pt-3 dark:border-gray-800">
                            <button
                                @click="updateAppearance('light')"
                                :class="[
                                    'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                                    appearance === 'light'
                                        ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-white'
                                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                                ]"
                            >
                                <Sun class="size-4" />Light
                            </button>
                            <button
                                @click="updateAppearance('dark')"
                                :class="[
                                    'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                                    appearance === 'dark'
                                        ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-white'
                                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                                ]"
                            >
                                <Moon class="size-4" />Dark
                            </button>
                            <button
                                @click="updateAppearance('system')"
                                :class="[
                                    'flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors',
                                    appearance === 'system'
                                        ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-white'
                                        : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300',
                                ]"
                            >
                                <Monitor class="size-4" />System
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </nav>

        <!-- Hero Section -->
        <section class="relative overflow-hidden pt-32 pb-20 lg:pt-40 lg:pb-28">
            <!-- Background decorations -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-40 -right-40 size-[500px] rounded-full bg-[#FF2D20]/5 blur-3xl dark:bg-[#FF2D20]/10"
                />
                <div
                    class="absolute -bottom-40 -left-40 size-[400px] rounded-full bg-[#FF2D20]/3 blur-3xl dark:bg-[#FF2D20]/5"
                />
            </div>

            <div class="relative mx-auto max-w-6xl px-6">
                <div class="mx-auto max-w-3xl text-center">
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#FF2D20]/20 bg-[#FF2D20]/5 px-4 py-1.5 text-sm font-medium text-[#FF2D20]"
                    >
                        <span class="relative flex size-2">
                            <span
                                class="absolute inline-flex size-full animate-ping rounded-full bg-[#FF2D20] opacity-75"
                            />
                            <span class="relative inline-flex size-2 rounded-full bg-[#FF2D20]" />
                        </span>
                        Fresh articles every week
                    </div>

                    <h1
                        class="mb-6 text-4xl leading-tight font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl dark:text-white"
                    >
                        Thoughts on
                        <span
                            class="bg-gradient-to-r from-[#FF2D20] to-[#FF6F61] bg-clip-text text-transparent"
                        >
                            Modern Web
                        </span>
                        Development
                    </h1>
                    <p
                        class="mx-auto mb-10 max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-gray-400"
                    >
                        Exploring Laravel, Vue.js, and the evolving web ecosystem. Practical guides,
                        deep dives, and real-world insights for developers who build.
                    </p>

                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <a
                            href="#articles"
                            class="group inline-flex items-center gap-2 rounded-xl bg-[#FF2D20] px-7 py-3.5 text-sm font-semibold text-white shadow-lg shadow-[#FF2D20]/25 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#e52a1e] hover:shadow-xl hover:shadow-[#FF2D20]/30"
                        >
                            Browse Articles
                            <ArrowRight
                                class="size-4 transition-transform group-hover:translate-x-0.5"
                            />
                        </a>
                        <a
                            href="#"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 px-7 py-3.5 text-sm font-semibold text-gray-700 transition-all duration-200 hover:-translate-y-0.5 hover:border-gray-400 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:border-gray-600 dark:hover:bg-gray-800/50"
                        >
                            Subscribe to Newsletter
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Post -->
        <section class="mx-auto max-w-6xl px-6 pb-20">
            <div
                class="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:shadow-lg dark:border-gray-800 dark:bg-[#161616]"
            >
                <div class="grid gap-0 lg:grid-cols-2">
                    <div class="relative overflow-hidden">
                        <img
                            :src="featuredPost.image"
                            :alt="featuredPost.title"
                            class="h-64 w-full object-cover transition-transform duration-500 group-hover:scale-105 lg:h-full"
                        />
                        <div class="absolute top-4 left-4">
                            <span
                                class="rounded-lg bg-[#FF2D20] px-3 py-1 text-xs font-bold tracking-wide text-white uppercase"
                            >
                                Featured
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center p-8 lg:p-12">
                        <div class="mb-4 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1.5">
                                <Calendar class="size-3.5" />
                                {{ featuredPost.date }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <Clock class="size-3.5" />
                                {{ featuredPost.readTime }}
                            </span>
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-[#FF2D20]/10 px-2.5 py-0.5 text-xs font-medium text-[#FF2D20]"
                            >
                                <Tag class="size-3" />
                                {{ featuredPost.category }}
                            </span>
                        </div>
                        <h2
                            class="mb-4 text-2xl leading-tight font-bold text-gray-900 transition-colors group-hover:text-[#FF2D20] lg:text-3xl dark:text-white"
                        >
                            {{ featuredPost.title }}
                        </h2>
                        <p class="mb-6 leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ featuredPost.excerpt }}
                        </p>
                        <a
                            href="#"
                            class="group/link inline-flex items-center gap-2 text-sm font-semibold text-[#FF2D20] transition-colors hover:text-[#e52a1e]"
                        >
                            Read full article
                            <ArrowRight
                                class="size-4 transition-transform group-hover/link:translate-x-1"
                            />
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Articles Grid -->
        <section id="articles" class="mx-auto max-w-6xl px-6 pb-24">
            <div class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold tracking-tight text-gray-900 lg:text-3xl dark:text-white">
                    Latest Articles
                </h2>

                <!-- Category Filter -->
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="category in categories"
                        :key="category"
                        @click="filterByCategory(category)"
                        :class="[
                            'rounded-lg px-3.5 py-1.5 text-sm font-medium transition-all duration-200',
                            activeCategory === category
                                ? 'bg-[#FF2D20] text-white shadow-sm shadow-[#FF2D20]/25'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700',
                        ]"
                    >
                        {{ category }}
                    </button>
                </div>
            </div>

            <!-- Posts Grid -->
            <TransitionGroup
                tag="div"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
                move-class="transition-all duration-300 ease-in-out"
            >
                <article
                    v-for="post in filteredPosts"
                    :key="post.title"
                    class="group overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-[#161616]"
                >
                    <div class="relative overflow-hidden">
                        <img
                            :src="post.image"
                            :alt="post.title"
                            class="h-48 w-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 transition-opacity group-hover:opacity-100" />
                    </div>
                    <div class="p-6">
                        <div class="mb-3 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1">
                                <Calendar class="size-3" />
                                {{ post.date }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Clock class="size-3" />
                                {{ post.readTime }}
                            </span>
                        </div>

                        <span
                            class="mb-3 inline-block rounded-md bg-[#FF2D20]/10 px-2.5 py-0.5 text-xs font-medium text-[#FF2D20]"
                        >
                            {{ post.category }}
                        </span>

                        <h3
                            class="mb-2 text-lg leading-snug font-bold text-gray-900 transition-colors group-hover:text-[#FF2D20] dark:text-white"
                        >
                            {{ post.title }}
                        </h3>
                        <p class="mb-4 line-clamp-2 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ post.excerpt }}
                        </p>
                        <a
                            href="#"
                            class="group/link inline-flex items-center gap-1.5 text-sm font-semibold text-[#FF2D20] transition-colors hover:text-[#e52a1e]"
                        >
                            Read more
                            <ArrowRight
                                class="size-3.5 transition-transform group-hover/link:translate-x-0.5"
                            />
                        </a>
                    </div>
                </article>
            </TransitionGroup>

            <!-- Empty State -->
            <div
                v-if="filteredPosts.length === 0"
                class="py-20 text-center"
            >
                <p class="text-lg text-gray-500 dark:text-gray-400">
                    No articles in this category yet. Check back soon!
                </p>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-[#111111]">
            <div class="mx-auto max-w-6xl px-6 py-20">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 lg:text-3xl dark:text-white">
                        Stay in the loop
                    </h2>
                    <p class="mb-8 text-gray-600 dark:text-gray-400">
                        Get notified when new articles are published. No spam, unsubscribe anytime.
                    </p>
                    <form @submit.prevent class="flex flex-col gap-3 sm:flex-row">
                        <input
                            type="email"
                            placeholder="you@example.com"
                            class="flex-1 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm text-gray-900 placeholder-gray-400 outline-none transition-all focus:border-[#FF2D20] focus:ring-2 focus:ring-[#FF2D20]/20 dark:border-gray-700 dark:bg-[#1a1a1a] dark:text-white dark:placeholder-gray-500 dark:focus:border-[#FF2D20]"
                        />
                        <button
                            type="submit"
                            class="rounded-xl bg-[#FF2D20] px-7 py-3 text-sm font-semibold text-white shadow-md shadow-[#FF2D20]/20 transition-all hover:-translate-y-0.5 hover:bg-[#e52a1e] hover:shadow-lg hover:shadow-[#FF2D20]/25"
                        >
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-200 bg-white dark:border-gray-800 dark:bg-[#0e0e0e]">
            <div class="mx-auto max-w-6xl px-6 py-12">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Brand -->
                    <div class="lg:col-span-2">
                        <div class="mb-4 flex items-center gap-3">
                            <div
                                class="flex size-8 items-center justify-center rounded-lg bg-[#FF2D20]"
                            >
                                <AppLogoIcon class="size-4 text-white" />
                            </div>
                            <span class="text-lg font-bold">
                                <span class="text-[#FF2D20]">Laravel</span><span class="dark:text-white">Blog</span>
                            </span>
                        </div>
                        <p class="max-w-sm text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                            A personal blog exploring modern web development with Laravel, Vue.js,
                            and the tools that power the web.
                        </p>
                    </div>

                    <!-- Links -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Content</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Articles</a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Categories</a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Archive</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Social -->
                    <div>
                        <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Connect</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">GitHub</a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Twitter / X</a>
                            </li>
                            <li>
                                <a href="#" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">RSS Feed</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-gray-200 pt-8 sm:flex-row dark:border-gray-800">
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        &copy; 2026 LaravelBlog. Built with Laravel, Vue & TailwindCSS.
                    </p>
                    <div class="flex gap-6">
                        <a href="#" class="text-sm text-gray-400 transition-colors hover:text-[#FF2D20] dark:text-gray-500">Privacy</a>
                        <a href="#" class="text-sm text-gray-400 transition-colors hover:text-[#FF2D20] dark:text-gray-500">Terms</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scroll to Top -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-90"
        >
            <button
                v-if="showScrollTop"
                @click="scrollToTop"
                class="fixed right-6 bottom-6 z-40 flex size-11 items-center justify-center rounded-full bg-[#FF2D20] text-white shadow-lg shadow-[#FF2D20]/25 transition-all hover:-translate-y-0.5 hover:bg-[#e52a1e]"
            >
                <ChevronUp class="size-5" />
            </button>
        </Transition>
    </div>
</template>
