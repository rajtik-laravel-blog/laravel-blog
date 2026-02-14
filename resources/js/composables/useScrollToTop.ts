import { onMounted, onUnmounted, ref } from 'vue';

export function useScrollToTop(threshold = 600) {
    const showScrollTop = ref(false);

    function handleScroll() {
        showScrollTop.value = window.scrollY > threshold;
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

    return {
        showScrollTop,
        scrollToTop,
    };
}
