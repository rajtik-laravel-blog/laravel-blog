<script setup lang="ts">
import { ref, watch, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const DURATION = 5000;

const visible = ref(false);
const message = ref('');
const progress = ref(100);

let progressInterval: ReturnType<typeof setInterval> | null = null;
let dismissTimeout: ReturnType<typeof setTimeout> | null = null;

const clearTimers = () => {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    if (dismissTimeout) {
        clearTimeout(dismissTimeout);
        dismissTimeout = null;
    }
};

const show = (text: string) => {
    clearTimers();
    message.value = text;
    progress.value = 100;
    visible.value = true;

    const step = 100 / (DURATION / 50);
    progressInterval = setInterval(() => {
        progress.value = Math.max(0, progress.value - step);
    }, 50);

    dismissTimeout = setTimeout(() => {
        visible.value = false;
        clearTimers();
    }, DURATION);
};

const page = usePage<{ flash: { success: string | null; nonce: string | null } }>();

// Watch the nonce — it's a unique value generated per flash, so the watch
// fires reliably even when the message text is identical to the previous save.
watch(
    () => page.props.flash?.nonce,
    () => {
        const text = page.props.flash?.success;
        if (text) {
            show(text);
        }
    },
    { immediate: true },
);

onUnmounted(() => {
    clearTimers();
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-full opacity-0"
        >
            <div
                v-if="visible"
                class="fixed right-4 top-4 z-50 w-80 overflow-hidden rounded-xl border border-[#19140015] bg-white shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]"
                role="alert"
            >
                <div class="flex items-start gap-3 p-4">
                    <div class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg class="size-3 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="flex-1 text-sm text-zinc-800 dark:text-zinc-200">{{ message }}</p>
                    <button
                        type="button"
                        class="-mr-1 -mt-1 rounded p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                        @click="visible = false"
                    >
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="h-1 bg-zinc-100 dark:bg-zinc-800">
                    <div
                        class="h-full bg-green-500 transition-none dark:bg-green-400"
                        :style="{ width: `${progress}%` }"
                    />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
