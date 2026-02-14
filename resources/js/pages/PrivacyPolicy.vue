<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import BlogFooter from '@/components/BlogFooter.vue';
import BlogNavigation from '@/components/BlogNavigation.vue';

defineProps<{
    [key: string]: never;
}>();
const contactEmail = import.meta.env.VITE_CONTACT_EMAIL;
</script>

<template>
    <Head title="Ochrana soukromí" />

    <div class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <!-- Top Bar -->
        <BlogNavigation />

        <main class="mx-auto max-w-3xl px-6 py-16 sm:py-20 lg:py-24">
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl mb-8">
                Zásady ochrany osobních údajů
            </h1>

            <div class="prose prose-zinc dark:prose-invert max-w-none">
                <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] mb-6">
                    Tyto zásady ochrany osobních údajů vysvětlují, jak jsou na tomto webu zpracovávány osobní údaje v souladu s nařízením GDPR.
                </p>

                <h2 class="text-xl font-semibold mt-8 mb-4">1. Správce osobních údajů</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Správcem osobních údajů je <span class="font-semibold text-zinc-800 dark:text-zinc-300">Vladislav Rajtmajer</span>, e-mail: <a class="text-red-500" :href="`mailto:${contactEmail}`">{{ contactEmail }}</a>
                </p>

                <h2 class="text-xl font-semibold mt-8 mb-4">2. Jaké údaje zpracovávám</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Na tomto webu standardně nezpracovávám žádné osobní údaje za účelem registrace nebo uživatelských účtů.
                </p>
                <ul class="list-disc pl-6 mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    <li>technické údaje nutné pro fungování webu (například IP adresa v serverových logách a technicky nezbytné cookies),</li>
                    <li>údaje, které mi dobrovolně pošlete e-mailem (například váš e-mail a obsah zprávy).</li>
                </ul>

                <h2 class="text-xl font-semibold mt-8 mb-4">3. Účel zpracování</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Údaje zpracovávám pouze za účelem technického provozu webu a případné odpovědi na váš e-mail.
                </p>

                <h2 class="text-xl font-semibold mt-8 mb-4">4. Předávání údajů třetím stranám</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Pro doručení e-mailů může být využívána služba Brevo (SMTP). Tato služba může zpracovávat e-mailové adresy a obsah zprávy pouze za účelem doručení komunikace. Jiné služby třetích stran pro zpracování osobních údajů nepoužívám.
                </p>

                <h2 class="text-xl font-semibold mt-8 mb-4">5. Doba uchovávání údajů</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    E-mailovou komunikaci uchovávám pouze po dobu nezbytnou k vyřízení vašeho dotazu. Technické logy jsou uchovávány po dobu obvyklou pro provoz a zabezpečení webu.
                </p>

                <h2 class="text-xl font-semibold mt-8 mb-4">6. Cookies</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Web používá pouze technicky nezbytné cookies (například pro zabezpečení a správné fungování aplikace). Tyto cookies nevyžadují aktivní souhlas uživatele.
                </p>

                <h2 class="text-xl font-semibold mt-8 mb-4">7. Vaše práva</h2>
                <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                    Máte právo požadovat přístup ke svým osobním údajům, jejich opravu nebo výmaz, případně vznést námitku proti zpracování.
                    V případě dotazů mě můžete kontaktovat na uvedeném e-mailu.
                </p>
                <p class="mt-12 text-sm text-[#9b9a96] dark:text-[#6f6e69]">
                    Poslední aktualizace: 12. 2. 2026
                </p>
            </div>
        </main>

        <!-- Footer -->
        <BlogFooter />
    </div>
</template>
