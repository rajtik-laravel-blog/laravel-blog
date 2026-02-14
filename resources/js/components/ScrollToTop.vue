<script setup lang="ts">
import { ChevronUp } from 'lucide-vue-next';
import { useScrollToTop } from '@/composables/useScrollToTop';

const { showScrollTop, scrollToTop } = useScrollToTop();
</script>

<template>
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
</template>
