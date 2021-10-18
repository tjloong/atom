<template>
    <div class="max-w-lg mx-auto">
        <page-header :title="role.name" back>
            <btn v-if="$can('role.manage') && !role.is_system" color="red" inverted @click="destroy()">
                <icon name="trash" /> Delete
            </btn>
        </page-header>

        <role-form :role="role" />

        <div class="flex items-center justify-between mb-4">
            <tabs
                :tabs="tabs"
                :value="tab"
                @input="$inertia.visit(
                    route('role.update', { id: role.id, tab: $event }), 
                    { replace: true, preserveScroll: true }
                )"
            />

            <slot name="action" />
        </div>

        <slot />
    </div>
</template>

<script>
import RoleForm from '../form.vue'

export default {
    name: 'RoleUpdateLayout',
    props: {
        tab: String,
        tabs: Array,
        role: Object,
    },
    components: {
        RoleForm,
    },
    metaInfo: { title: 'Update Role' },
    methods: {
        destroy () {
            this.$confirm({
                title: 'Delete Role',
                message: `Are you sure to delete role ${this.role.name}?`,
                onConfirmed: () => {
                    this.$inertia.delete(
                        this.route('role.delete', { 
                            id: this.role.id,
                            redirect: this.$page.props.referer,
                        }),
                        { replace: true }
                    )
                }
            })
        },
    }
}
</script>
