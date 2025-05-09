import { useTranslationStore } from "@/Components/totocsa/LocalTranslation/translationStore"

export function useT() {
    const store = useTranslationStore()
    return (category, originalSubtitle, locale, params) =>
        store.findTranslation(category, originalSubtitle, locale, params)
}

/**
 Használat Vue komponensben:
 <script setup>
import { useT } from "@/composables/useTranslation";

const t = useT();

console.log(t("ActionMenu", "Delete", "hu")); // "Törlés"
</script>
 */
