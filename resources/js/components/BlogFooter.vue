<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home, privacyPolicy, tags } from '@/routes';
</script>

<template>
    <footer class="border-t border-gray-200 bg-white dark:border-gray-800 dark:bg-[#0e0e0e]">
        <div class="mx-auto max-w-6xl px-6 py-12">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Brand -->
                <div class="lg:col-span-2">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex size-8 items-center justify-center rounded-lg bg-[#FF2D20]">
                            <AppLogoIcon class="size-4 text-white" />
                        </div>
                        <span class="text-lg font-bold">
                            <span class="text-[#FF2D20]">Laravel</span><span class="dark:text-white">Blog</span>
                        </span>
                    </div>
                    <p class="max-w-sm text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                        Neoficiální blog o Laravelu – návody, praktické tipy.<br>
                        Deep dive do funkcí a zkušenosti z reálného vývoje.
                    </p>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Obsah</h4>
                    <ul class="space-y-2">
                        <li>
                            <Link :href="home().url" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Články</Link>
                        </li>
                        <li>
                            <Link :href="tags().url" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Štítky</Link>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Kontakt</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="https://github.com/rajtmajer" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">GitHub</a>
                        </li>
                        <li>
                            <a href="https://x.com/VRajtmajer" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">X</a>
                        </li>
                        <li>
                            <a href="mailto:rajtik.laravel.blog@gmail.com" class="text-sm text-gray-500 transition-colors hover:text-[#FF2D20] dark:text-gray-400">Email</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-gray-200 pt-8 sm:flex-row dark:border-gray-800">
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    &copy; {{ new Date().getFullYear() }} Laravel Blog. Vytvořeno v <span class="text-[#FF2D20]">Laravelu</span> by Vladislav Rajtmajer.
                </p>
                <div class="flex gap-6">
                    <Link :href="privacyPolicy().url" class="text-sm text-gray-400 transition-colors hover:text-[#FF2D20] dark:text-gray-500">
                        Ochrana soukromí
                    </Link>
                </div>
            </div>
        </div>
    </footer>
</template>
