<template>
    <div class="max-w-lg mx-auto">
        <page-header v-if="route().current() === 'user.list'" title="Users">
            <btn v-if="$can('user.manage')" :href="route('user.create')">
                <icon name="plus" /> New User
            </btn>
        </page-header>

        <data-table
            :data="users.data"
            :meta="users.meta"
            :fields="fields"
        />
    </div>
</template>

<script>
export default {
    name: 'UserList',
    props: {
        users: Object,
    },
    metaInfo: { title: 'Users' },
    computed: {
        fields () {
            return [
                {
                    key: 'name',
                    sort: 'name',
                    link: (user) => (this.$can('user.manage') && this.route('user.update', { id: user.id })),
                    small: (user) => (user.email),
                },
                {
                    key: 'role.name',
                    label: 'Role',
                    align: 'right',
                },
                this.route().current() === 'team.update' && {
                    key: 'actions',
                    actions: (user) => ([
                        {
                            name: 'Remove',
                            icon: 'minus-circle',
                            action: (user) => this.$emit('remove', user)
                        }
                    ]),
                },
            ].filter(Boolean)
        },
    }
}
</script>
