<script setup>
import { useAttrs } from "vue"
import { useTranslationStore } from "@/Components/totocsa/LocalTranslation/translationStore"
import { t } from "@/Components/totocsa/LocalTranslation/translation"
import { LanguageIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    category: { type: String },
    subtitle: { type: String },
    locale: { type: String },
    params: { type: Object },
    showText: { type: Boolean, default: true }
})

const attrs = useAttrs()
const translationStore = useTranslationStore();

</script>

<template>
    <span v-if="translationStore.isIcons" class="relative inline-block" v-bind="attrs">
        <span v-if="showText">{{ t(category, subtitle, locale, params) }}</span>

        <LanguageIcon v-if="$page.props.userRoles.Administrator || $page.props.userRoles.Translator"
            @click.prevent.stop="translationStore.localTranslationFormAddToStack(props.category, props.subtitle)"
            class="show-translation-modal absolute cursor-pointer right-0 top-1/2 -translate-y-1/2 bg-indigo-700 border-2 border-fuchsia-500 border-solid inline-block opacity-25 hover:opacity-100 rounded-md text-gray-200 z-[100] w-5" />
    </span>

    <span v-else-if="showText" v-bind="attrs">
        {{ t(category, subtitle, locale, params) }}
    </span>
</template>
