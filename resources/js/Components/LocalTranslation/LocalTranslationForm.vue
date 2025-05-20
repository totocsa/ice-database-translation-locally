<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid'
import { shallowRef } from "vue"
import { usePage } from "@inertiajs/vue3"
import { useTranslationStore } from "@IceDatabaseTranslationLocally/Components/LocalTranslation/translationStore"
import { useModalLiFoStore } from '@IceModalLiFo/Components/ModalLiFo/ModalLiFoStore.js'
import CountItemsAndTranslationIcon from "@IceModalLiFo/Components/ModalLiFo/CountItemsAndTranslationIcon.vue"
import Modal from "@IceModalLiFo/Components/ModalLiFo/Modal.vue"
import LocalTranslation from "./LocalTranslation.vue"

const props = defineProps({
    title: {
        type: Object,
        default: () => ({
            category: 'Translations',
            subtitle: 'Translation',
        })
    },
    itemId: String,
    formData: Object,
})

const page = usePage()
const translationStore = useTranslationStore()
const modalLiFoStore = useModalLiFoStore()

const closeModal = (event) => {
    modalLiFoStore.removeLast()
}

const errors = shallowRef({})
const submit = async (event) => {
    try {
        errors.value = await translationStore.localTranslationFormSubmit(props.formData)
        if ((typeof errors.value === 'object') && Object.keys(errors.value).length === 0) {
            closeModal()
        }
    } catch (error) {
        console.error('Error during submit:', error)
    }
}
</script>

<template>
    <Modal>
        <form @submit.prevent="submit">
            <!-- Fejléc -->
            <div class="flex justify-between rounded-t-lg p-3 bg-blue-500 text-gray-100">
                <div class="text-lg">
                    <LocalTranslation :category="props.title.category" :subtitle="props.title.subtitle" />
                </div>

                <div class="flex items-start ml-2">
                    <CountItemsAndTranslationIcon />

                    <XMarkIcon @click="closeModal"
                        class="bg-blue-700 cursor-pointer inline-flex rounded hover:bg-blue-800 w-5" />
                </div>
            </div>

            <!-- Tartalom -->
            <div class="bg-gray-100">
                <div class="bg-gray-300 pb-1 pl-3 pr-3 pt-1">
                    <div>
                        <span class="font-bold mr-2">
                            <LocalTranslation category="App\\Models\\Translationoriginal" subtitle="category" />:
                        </span>

                        <span>{{ props.formData.original.category }}</span>
                    </div>

                    <div class="text-red-600">
                        {{ errors?.original?.category[0] }}
                    </div>
                </div>

                <div class="bg-gray-300 pb-1 pl-3 pr-3 pt-1">
                    <div>
                        <span class="font-bold mr-2">
                            <LocalTranslation category="App\\Models\\Translationoriginal" subtitle="subtitle" />:
                        </span>
                        <span>{{ props.formData.original.subtitle }}</span>
                    </div>

                    <div class="text-red-600">
                        {{ errors?.original?.subtitle[0] }}
                    </div>
                </div>

                <div class="overflow-y-auto h-80">
                    <div v-for="(i, index) in page.props.locale.supported" :key="i" class="inline-flex flex-col">
                        <div class="p-3">
                            <label :for="`App\\Models\\Translationvariant_${props.itemId}_${index}_subtitle`"
                                class="font-bold">
                                <LocalTranslation category="App\\Models\\Translationvariant" subtitle="subtitle" />
                                -
                                {{ props.formData.translations[index].locales_configname }}
                            </label>

                            <div>
                                <input v-model="props.formData.translations[index].subtitle" type="text"
                                    :id="`App\\Models\\Translationvariant_${props.itemId}_${index}_subtitle`" />

                                <div class="text-red-600">
                                    {{ errors?.translations?.[index]?.subtitle[0] }}
                                </div>

                                <div class="text-red-600">
                                    {{ errors?.translations?.[index]?.locales_configname[0] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gombok -->
            <div class="flex justify-end space-x-3 bg-gray-100 border-t border-gray-400 p-3 rounded-b-lg">
                <button @click="closeModal" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                    <LocalTranslation category="form" subtitle="Close" />
                </button>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded">
                    <LocalTranslation category="form" subtitle="Save" />
                </button>
            </div>
        </form>
    </Modal>
</template>
