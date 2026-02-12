<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, FileText } from 'lucide-vue-next';
import { computed } from 'vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard, home } from '@/routes';
import { index as authorPosts } from '@/routes/author/posts';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const mainNavItems = computed((): NavItem[] => {
    return [
        {
            title: 'Nástěnka',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Moje články',
            href: authorPosts(),
            icon: FileText,
        },
    ];
});

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repozitář na GitHubu',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Dokumentace',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
