<script setup>
import { router, usePage } from '@inertiajs/vue3'
import "/node_modules/flag-icons/css/flag-icons.min.css"

defineProps({
    //
})

const page = usePage()

function newZiggy(locale) {
    // Másoljuk az eredeti Ziggy objektumot
    const oldRootUrl = page.props.locale.current
    const oldUrlPrefix = `${page.props.locale.current}/`
    const updatedZiggy = { ...Ziggy, routes: { ...Ziggy.routes } }

    // Frissítjük az URL-eket az új nyelvi előtaggal
    Object.keys(updatedZiggy.routes).forEach(routeName => {
        const routeData = updatedZiggy.routes[routeName]
        let uri = routeData.uri

        if (uri === oldRootUrl || (uri.length > oldUrlPrefix.length && uri.substring(0, oldUrlPrefix.length) === oldUrlPrefix)) {
            uri = `${locale}${uri.substring(oldUrlPrefix.length - 1)}`
            routeData.uri = uri
        }

        updatedZiggy.routes[routeName] = routeData
    })

    // Ziggy globális cseréje
    window.Ziggy = updatedZiggy
}

const changeLanguage = async (locale) => {
    const url = location.origin + location.pathname
    const ziggyUrl = route().t.url
    const routeUrl = url.substring(ziggyUrl.length)
    let routeArray = routeUrl.split('/')
    routeArray[1] = locale
    const newUrl = ziggyUrl + routeArray.join('/') + location.search

    newZiggy(locale)
    page.props.locale.current = locale

    router.visit(newUrl, {
        preserveState: true,
        preserveScroll: true,
    })
}

function flagImageExists(code) {
    const el = document.createElement('i')
    el.className = `fi-${code}`
    el.style.display = 'none'
    document.body.prepend(el)

    const backgroundImage = window.getComputedStyle(el).getPropertyValue("background-image")

    el.remove()

    return backgroundImage !== 'none'
}
</script>

<template>
    <div class="flex">
        <div v-for="(lang, key) in $page.props.locale.supported" :key="key" :title="lang.native"
            @click.prevent="changeLanguage(key)" :class="[
                'flex flex-wrap justify-center ml-1 mr-1 first:ml-0 border-b-2 pb-1 cursor-pointer',
                'fi fi-' + (lang.flag === '' ? key : lang.flag),
                key === $page.props.locale.current ? 'border-gray-400' : 'border-transparent',
            ]">

            <span v-if="!flagImageExists(lang.flag === '' ? key : lang.flag)">
                {{ key }}
            </span>
        </div>
    </div>
</template>
