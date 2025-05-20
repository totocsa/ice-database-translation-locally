<script setup>
import LocalTranslation from './LocalTranslation.vue'

const props = defineProps({
    titleArray: Array,
})
</script>

<template>
    <h2 class="flex items-center font-semibold text-xl text-gray-800 leading-tight whitespace-nowrap">
        <span class="mr-2">
            <LocalTranslation :category="titleArray[0]" :subtitle="titleArray[1]" />
        </span>

        <span v-if="titleArray.length > 2">
            -
            <span class="ml-2">
                <LocalTranslation :category="titleArray[2]" :subtitle="titleArray[3]"></LocalTranslation>
                <template v-if="titleArray?.[4]">
                    <span>
                        {{ " - " + titleArray[4] }}
                    </span>
                </template>
            </span>
        </span>
    </h2>
</template>
