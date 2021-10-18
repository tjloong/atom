<template>
    <div class="max-w-lg mx-auto">
        <page-header v-if="route().current() === 'team.list'" title="Teams">
            <btn v-if="$can('team.manage')" :href="route('team.create')">
                <icon name="plus" /> New Team
            </btn>
        </page-header>

        <data-table
            :data="teams.data"
            :meta="teams.meta"
            :fields="fields"
        />
    </div>
</template>

<script>
export default {
    name: 'TeamList',
    props: {
        can: Object,
        teams: Object,
    },
    metaInfo: { title: 'Teams' },
    computed: {
        fields () {
            return [
                {
                    key: 'name',
                    sort: 'name',
                    link: (team) => (this.$can('team.manage') && this.route('team.update', { id: team.id })),
                },
                {
                    key: 'users',
                    align: 'right',
                    computed: (team) => (`${team.counter.users} ${this.$options.filters.pluralize(team.counter.users, 'user')}`),
                    link: (team) => (this.$can('user.manage') && this.route('user.list', { team_id: team.id })),
                },
                this.route().current() === 'user.update' && {
                    key: 'actions',
                    actions: (team) => ([
                        {
                            name: 'Remove',
                            icon: 'minus-circle',
                            action: (team) => this.$emit('remove', team),
                        },
                    ]),
                },
            ].filter(Boolean)
        },
    }
}
</script>
