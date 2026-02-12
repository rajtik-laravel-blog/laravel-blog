<script lang="ts" setup>
import {Link} from '@inertiajs/vue3';
import {privacyPolicy} from '@/routes';
</script>

<template>
    <footer
        class="border-t border-[#19140015] py-10 text-center text-xs text-[#706f6c] dark:border-[#2a2a27] dark:text-[#A1A09A]"
    >
        <div class="mx-auto max-w-7xl px-6">
            <p class="flex justify-between">
                <span>
                    Vytvořeno v
                    <span class="font-medium text-[#f53003] dark:text-[#FF4433]"
                    ><a href="https://laravel.com">Laravel</a></span
                    > &copy; {{ new Date().getFullYear() }} by Vladislav Rajtmajer
                </span>
                <Link
                    :href="privacyPolicy().url"
                    class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors"
                >
                    Ochrana soukromí
                </Link>
            </p>
        </div>
    </footer>
</template>
