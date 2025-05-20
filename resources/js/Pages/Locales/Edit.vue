<script setup>
import { reactive } from "vue"
import { Link } from '@inertiajs/vue3'
import IceLayout from '@IceDatabaseTranslationLocally/Layouts/IceLayout.vue';
import IcseusdEdit from '@IceIcseusd/Components/totocsa/Icseusd/Edit.vue';
import ControllerMenu from "@IceIcseusd/Components/totocsa/Icseusd/ControllerMenu.vue"
import ActionMenu from '@IceIcseusd/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue';
import LocalTranslation from '@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslation.vue';

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    routeParameterName: String,
    modelClassName: String,
    item: Object,
    orders: Object,
    fields: Object,
    errors: Object,
    additionalData: Object,
})

let da = {}
for (let i in props.item) {
    da[i] = props.item[i]
}

const formData = reactive(da)

const titleArray = ['Users', 'Users', 'ActionMenu', 'Edit', props.item.id]

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
    edit: {
        label: 'Edit',
        attributes: {
            href: route(`${props.routeController}.edit`, { [props.routeParameterName]: props.item.id }),
        }
    },
    show: {
        label: 'Show',
        attributes: {
            href: route(`${props.routeController}.show`, { [props.routeParameterName]: props.item.id }),
        }
    },
    destroy: {
        label: 'Delete',
        attributes: {
            href: route(`${props.routeController}.destroy`, { [props.routeParameterName]: props.item.id }),
        }
    }
}
</script>

<template>
    <IceLayout :title="titleArray" :authUser="$page.props.auth.user">
        <template #header>
            <ControllerMenu :userRoles="props.userRoles" groupName="administration" active="users-index">
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
                <ActionMenu :config="actionMenuConfig" :modelClassName="props.modelClassName" :item="props.item"
                    :orders="props.orders" active="edit" />
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <IcseusdEdit :config="props" :formData="formData" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
