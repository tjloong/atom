<template>
    <layout tab="teams" v-bind="$props">
        <template #action>
            <team-picker @input="joinTeam">
                <btn inverted>
                    <icon name="plus" /> Join Team
                </btn>
            </team-picker>
        </template>

        <team-list :teams="teams" @remove="leaveTeam" />
    </layout>
</template>

<script>
import Layout from './layout.vue'
import TeamList from '@/app/team/list.vue'
import TeamPicker from '@/app/team/picker.vue'

export default {
    name: 'UserUpdateTeams',
    props: {
        tabs: Array,
        user: Object,
        roles: Array,
        teams: Object,
    },
    components: {
        Layout,
        TeamList,
        TeamPicker,
    },
    methods: {
        joinTeam (team) {
            this.$inertia
                .form({ user: {
                    id: this.user.id,
                    join_team: team.id,
                }})
                .post(this.route('user.update', { id: this.user.id }))
        },
        leaveTeam (team) {
            this.$inertia
                .form({ user: {
                    id: this.user.id,
                    leave_team: team.id,
                }})
                .post(this.route('user.update', { id: this.user.id }))
        },
    }
}
</script>
