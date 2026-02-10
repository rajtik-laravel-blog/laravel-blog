<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, FileText, MessageSquare } from 'lucide-vue-next';
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
import { index as userComments } from '@/routes/comments';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const page = usePage();

const mainNavItems = computed((): NavItem[] => {
    const items: NavItem[] = [
        {
            title: 'Nástěnka',
            href: dashboard(),
            icon: LayoutGrid,
        },
        {
            title: 'Moje komentáře',
            href: userComments(),
            icon: MessageSquare,
        },
    ];

    if (page.props.auth.user?.is_author) {
        items.push({
            title: 'Moje články',
            href: authorPosts(),
            icon: FileText,
        });
    }

    return items;
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
