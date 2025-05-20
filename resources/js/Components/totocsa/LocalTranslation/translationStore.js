import { markRaw, reactive } from "vue"
import { router, usePage } from "@inertiajs/vue3"
import { defineStore } from "pinia"
import LocalTranslationForm from "@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslationForm.vue"
import { useModalLiFoStore } from "@IceModalLiFo/Components/totocsa/ModalLiFo/ModalLiFoStore.js"

const page = usePage()

export const useTranslationStore = defineStore("translation", {
    state: () => ({
        translationMap: {},
        beforeRequestUrl: null,
        isIcons: false,
        isLocalTranslationForm: false,
        localTranslationForm: {
            original: {
                category: null,
                subtitle: null,
            },
            category: null,
            subtitle: null,
            translations: localTranslationFormTranslations(),
            locale: null,
            subtitle: null,
            errors: clearErrors(),
        },
    }),
    actions: {
        toggleIsIcons() {
            this.isIcons = !this.isIcons
        },
        buildNestedTranslationMap(data) {
            buildNestedTranslationMap(data)
        },
        findTranslation(category, subtitle, locale, params = {}) {
            return findTranslation(category, subtitle, locale, params)
        },
        localTranslationFormShow(category, subtitle) {
            localTranslationFormShow(category, subtitle)
            this.isLocalTranslationForm = true
        },
        localTranslationFormAddToStack(category, subtitle) {
            localTranslationFormAddToStack(category, subtitle)
        },
        closeLocalTranslationForm() {
            this.isLocalTranslationForm = false
        },
        clearErrors() {
            return clearErrors()
        },
        localTranslationFormSubmit(formData) {
            return localTranslationFormSubmit(formData)
        },
    },
})

const buildNestedTranslationMap = (data) => {
    const map = {}
    data.forEach(
        ({ category, original_subtitle, locale, translated_subtitle }) => {
            if (!map[category]) map[category] = {}
            if (!map[category][original_subtitle])
                map[category][original_subtitle] = {}
            map[category][original_subtitle][locale] = translated_subtitle
        }
    )

    useTranslationStore().translationMap = map
}

const findTranslation = (category, subtitle, locale, params = {}) => {
    locale = locale ?? page.props.locale.current

    let translation =
        useTranslationStore().translationMap?.[category]?.[subtitle]?.[
        locale
        ] ?? null

    if (translation <= "") {
        translation = subtitle

        useTranslationStore().translationMap[category] ??= {}
        useTranslationStore().translationMap[category][subtitle] ??= {}
        useTranslationStore().translationMap[category][subtitle][locale] =
            translation
    }

    translation = Object.keys(params).reduce(
        (str, key) => str.replace(new RegExp(key, "g"), params[key]),
        translation
    )

    return translation
}

const localTranslationFormShow = (category, subtitle) => {
    useTranslationStore().localTranslationForm.original = {
        category: category,
        subtitle: subtitle,
    }

    for (let i in page.props.locale.supported) {
        useTranslationStore().localTranslationForm.translations[
            i
        ].locales_configname = i
        useTranslationStore().localTranslationForm.translations[i].subtitle =
            useTranslationStore().translationMap[category][subtitle][i]
    }

    useTranslationStore().localTranslationForm.errors = clearErrors()

    useTranslationStore().isLocalTranslationForm = true
}

const localTranslationFormAddToStack = (category, subtitle) => {
    const translations = {}
    for (let i in page.props.locale.supported) {
        translations[i] = {
            locales_configname: i,
            subtitle:
                useTranslationStore().translationMap[category][subtitle][i],
        }
    }

    const itemId = location.protocol === 'https:' ? crypto.randomUUID() : 'x' + Date.now()
    useModalLiFoStore().addToStack(itemId, markRaw(LocalTranslationForm), {
        itemId: itemId,
        formData: reactive({
            original: {
                category: category,
                subtitle: subtitle,
            },
            translations: translations,
        }),
    })
}

const clearErrors = () => {
    let items = {
        original: {
            category: [""],
            subtitle: [""],
        },
        translations: {},
    }

    for (let i in page.props.locale.supported) {
        items.translations[i] = {
            locales_configname: [""],
            subtitle: [""],
        }
    }

    return items
}

const localTranslationFormTranslations = () => {
    let items = {}

    for (let i in page.props.locale.supported) {
        items[i] = {
            locales_configname: "",
            subtitle: "",
        }
    }

    return items
}

const localTranslationFormSubmit = (formData) => {
    return new Promise((resolve, reject) => {
        useTranslationStore().beforeRequestUrl = location.href
        router.post(
            route("translations.save") + location.search,
            {
                component: page.component,
                original: formData.original,
                translations: formData.translations,
            },
            {
                preserveUrl: true,
                preserveState: true,
                preserveScroll: true,
                only: ["translationResults"],
                onSuccess: (response) => {
                    localTranslationFormSubmitSuccess(response)
                    /*const errors =
                        useTranslationStore().localTranslationForm.errors
                    useTranslationStore().clearErrors()
                    resolve(errors)*/
                    resolve(response.props?.translationResults.errors ?? [])
                },
                onError: (error) => {
                    console.log("onError", error)
                    reject(error)
                },
            }
        )
    })
}

const localTranslationFormHasError = (resultsErrors) => {
    let oE = useTranslationStore().localTranslationForm.errors.original
    let nE = resultsErrors.original ?? {}

    useTranslationStore().localTranslationForm.errors.original =
        { ...oE, ...nE } ?? oE

    for (let i in useTranslationStore().localTranslationForm.errors
        .translations) {
        oE = useTranslationStore().localTranslationForm.errors.translations[i]
        nE = resultsErrors?.translations?.[i] || {}

        useTranslationStore().localTranslationForm.errors.translations[i] =
            { ...oE, ...nE } ?? oE
    }
}

const localTranslationFormSubmitSuccess = (data) => {
    const results = data.props?.translationResults ?? false

    if (results === false) {
        return
    } else {
        const isError = typeof results?.errors !== "undefined"

        if (isError) {
            localTranslationFormHasError(results.errors)
        } else if (results.exception === "1") {
            useTranslationStore().localTranslationForm.errors.subtitle =
                "Exception"
        } else {
            let i1
            let t1, t2, t3

            for (i1 in results.translations) {
                t1 = results.original.category
                t2 = results.original.subtitle
                t3 = results.translations[i1].subtitle

                useTranslationStore().translationMap[t1] ??= {}
                useTranslationStore().translationMap[t1][t2] ??= {}
                useTranslationStore().translationMap[t1][t2][i1] = t3

                for (i1 in useTranslationStore().localTranslationForm
                    .original) {
                    useTranslationStore().localTranslationForm.original[i1] = ""
                }

                useTranslationStore().localTranslationForm.translations =
                    localTranslationFormTranslations()
            }

            useTranslationStore().localTranslationForm.errors = clearErrors()
            //            router.visit(useTranslationStore().beforeRequestUrl)
            useTranslationStore().isLocalTranslationForm = false
        }
    }
}
