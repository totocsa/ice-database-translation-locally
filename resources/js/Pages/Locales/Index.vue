<script setup>
import { Link } from '@inertiajs/vue3'
import IceLayout from '@IceDatabaseTranslationLocally/Layouts/IceLayout.vue';
import IcseusdIndex from '@IceIcseusd/Components/totocsa/Icseusd/Index.vue';
import ControllerMenu from "@IceIcseusd/Components/totocsa/Icseusd/ControllerMenu.vue"
import ActionMenu from '@IceIcseusd/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue';
import LocalTranslation from '@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslation.vue';

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    routeParameterName: [String, Object],
    modelClassName: String,
    items: Object,
    filters: Object,
    orders: Object,
    sort: Object,
    fields: Object,
    per_pages: {
        type: [Array, Object],
        default: () => [10, 20, 50, 100],
    },
    itemButtons: Object,
    routes: Object,
    editableResults: Object,
    additionalData: Object,
    paramNames: Object,
})

const titleArray = ['Users', 'Users', 'ActionMenu', 'Index']

const controllerMenuLink = ["inline-block", "m-1", "first:ml-0", "last:mr-0", "px-2", "py-1", "rounded"]
const controllerMenuLinkActive = controllerMenuLink.concat(["bg-gray-200"])

const actionMenuConfig = {
    index: {
        label: 'Index',
        attributes: {
            href: route(`${props.routeController}.index`),
        }
    },
    create: {
        label: 'Create',
        attributes: {
            href: route(`${props.routeController}.create`),
        }
    },
}
</script>

<template>
    <IceLayout :title="titleArray" :authUser="$page.props.auth.user">
        <template #header>
            <ControllerMenu :userRoles="props.userRoles" groupName="administration" active="locales-index">
                <Link id="users-index" :href="route('users.index')" :class="controllerMenuLink">
                <LocalTranslation category="ControllerMenu-item" subtitle="Users" />
                </Link>

                <Link :href="route('locales.index')" :class="controllerMenuLinkActive">
                <LocalTranslation category="ControllerMenu-item" subtitle="Locales" />
                </Link>

                <Link :href="route('translations.index')" :class="controllerMenuLink">
                <LocalTranslation category="ControllerMenu-item" subtitle="Translations" />
                </Link>
            </ControllerMenu>

            <div class="text-right w-[100%]">
                <ActionMenu :config="actionMenuConfig" active="index" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <IcseusdIndex :config="props" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
