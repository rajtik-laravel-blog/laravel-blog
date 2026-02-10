<script setup lang="ts">
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="size-9">
                <Sun
                    v-if="appearance === 'light'"
                    class="size-4 rotate-0 scale-100 transition-all"
                />
                <Moon
                    v-else-if="appearance === 'dark'"
                    class="size-4 rotate-0 scale-100 transition-all"
                />
                <Monitor v-else class="size-4 rotate-0 scale-100 transition-all" />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="updateAppearance('light')">
                <Sun class="mr-2 size-4" />
                <span>Light</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="updateAppearance('dark')">
                <Moon class="mr-2 size-4" />
                <span>Dark</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="updateAppearance('system')">
                <Monitor class="mr-2 size-4" />
                <span>System</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
