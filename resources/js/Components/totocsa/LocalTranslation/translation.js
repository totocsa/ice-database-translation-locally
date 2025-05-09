import { useTranslationStore } from "@/Components/totocsa/LocalTranslation/translationStore"

export function t(category, originalSubtitle, locale, params) {
    return useTranslationStore().findTranslation(
        category,
        originalSubtitle,
        locale,
        params
    )
}

/*
Használat Vue komponensben
<script setup>
import { t } from "@/utils/translation";

const deleteText = t("ActionMenu", "Delete", "hu");
console.log(deleteText); // "Törlés"
</script>
*/
