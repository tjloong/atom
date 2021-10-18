<template>
    <div class="max-w-lg mx-auto">
        <page-header v-if="route().current() === 'role.list'" title="Roles">
            <btn v-if="$can('role.manage')" :href="route('role.create')">
                <icon name="plus" /> New Role
            </btn>
        </page-header>

        <data-table
            :data="roles.data"
            :meta="roles.meta"
            :fields="fields"
        />
    </div>
</template>

<script>
export default {
    name: 'RoleList',
    props: {
        can: Object,
        roles: Object,
    },
    metaInfo: { title: 'Roles' },
    computed: {
        fields () {
            return [
                {
                    key: 'name',
                    sort: 'name',
                    link: (role) => (this.$can('role.manage') && this.route('role.update', { id: role.id })),
                },
                {
                    key: 'access',
                    sort: 'access',
                    align: 'right',
                    tag: (role) => (this.$options.filters.titlecase(`${role.access}-access`)),
                },
            ].filter(Boolean)
        },
    }
}
</script>
