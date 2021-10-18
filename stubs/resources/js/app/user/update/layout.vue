<template>
    <div class="max-w-lg mx-auto">
        <page-header :title="user.name" back>
            <btn v-if="$can('user.manage') && user.id !== $auth('user.id')" color="red" inverted @click="destroy()">
                <icon name="trash" /> Delete
            </btn>
        </page-header>

        <user-form :user="user" :roles="roles" />

        <div class="flex items-center justify-between mb-4">
            <tabs
                :tabs="tabs"
                :value="tab"
                @input="$inertia.visit(
                    route('user.update', { id: user.id, tab: $event }), 
                    { replace: true, preserveScroll: true }
                )"
            />

            <slot name="action" />
        </div>

        <slot />
    </div>
</template>

<script>
import UserForm from '../form.vue'

export default {
    name: 'UserUpdate',
    props: {
        tab: String,
        tabs: Array,
        user: Object,
        roles: Array,
    },
    components: {
        UserForm,
    },
    metaInfo: { title: 'Update User' },
    methods: {
        destroy () {
            this.$confirm({
                title: 'Delete User',
                message: `Are you sure to delete ${this.user.name}?`,
                onConfirmed: () => {
                    this.$inertia.delete(this.route('user.delete', { id: this.user.id }))
                }
            })
        },
    }
}
</script>
