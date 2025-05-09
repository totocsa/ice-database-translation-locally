<script setup>
import { ref, onBeforeMount } from "vue"
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { LanguageIcon, UserIcon } from '@heroicons/vue/20/solid'
import { useIndex } from '@/Components/totocsa/Icseusd/js/useIndex.js'
import { useTranslationStore } from "@/Components/totocsa/LocalTranslation/translationStore"
import ApplicationMark from '@/Components/ApplicationMark.vue'
import Banner from '@/Components/Banner.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import ModalLiFo from "@/Components/totocsa/ModalLiFo/ModalLiFo.vue"
import LanguageSwitcher from '@/Components/totocsa/LanguageSwitcher.vue'

const props = defineProps({
    title: [String, Array],
    authUser: [Object],
})

const page = usePage()
const { setTitleText } = useIndex(props)

onBeforeMount(() => {
    const { props } = usePage()
    if (props.translations !== undefined) {
        const a = useTranslationStore().translationMap
        useTranslationStore().buildNestedTranslationMap(props.translations)
    }
})

const showingNavigationDropdown = ref(false)

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    })
}

const logout = () => {
    router.post(route('logout'))
}

const isAuthUser = !(props.authUser === null || props.authUser === undefined)
const menuItems = [
    { route: '/', routeCurrent: '/', subtitle: 'Welcome', visible: true },
    { route: 'dashboard', routeCurrent: 'dashboard', subtitle: 'Dashboard', visible: isAuthUser },
    { route: 'translations.index', routeCurrent: 'translations.*', subtitle: 'Translations', visible: isAuthUser },
    { route: 'users.index', routeCurrent: 'users.*', subtitle: 'Users', visible: isAuthUser },
    { route: 'authorization.index', routeCurrent: 'authorization.*', subtitle: 'Authorizations', visible: isAuthUser },
    { route: 'icseusd.generics.index', routeParams: { configName: 'users' }, routeCurrent: 'icseusd.generics.*', subtitle: 'Generic', visible: isAuthUser },
]
</script>

<template>
    <div>

        <Head :title="setTitleText(title)" />
        <Banner />
        <ModalLiFo />

        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('/')">
                                <ApplicationMark class="block h-9 w-auto" />
                                </Link>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <LanguageSwitcher />

                            <LanguageIcon v-if="$page.props.userRoles.Administrator || $page.props.userRoles.Translator"
                                class="bg-indigo-700 border-2 border-fuchsia-500 border-solid cursor-pointer inline-block mx-1 rounded-md text-gray-200 w-5"
                                @click="useTranslationStore().toggleIsIcons()" />

                            <div v-if="$page.props.auth.user !== null" class="relative ml-1">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="size-8 rounded-full object-cover"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                                <UserIcon class="w-5" />
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                            :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow whitespace-nowrap">
                <div
                    class="flex flex-row flex-wrap items-center justify-between max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
</script>
