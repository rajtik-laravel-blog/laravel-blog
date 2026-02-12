<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { FileText, Eye } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface Stats {
    total_articles: number;
    total_views: number;
}

defineProps<{
    stats: Stats;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Nástěnka',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Nástěnka" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-8 p-4 md:p-8">
            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-[#19140015] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400">
                            <FileText class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500">Moje články</p>
                            <h3 class="text-2xl font-bold">{{ stats.total_articles }}</h3>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-[#19140015] bg-white p-6 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]">
                    <div class="flex items-center gap-4">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400">
                            <Eye class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-zinc-500">Celkem zobrazení</p>
                            <h3 class="text-2xl font-bold">{{ stats.total_views }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
