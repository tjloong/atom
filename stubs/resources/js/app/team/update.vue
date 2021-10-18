<template>
    <div class="max-w-lg mx-auto">
        <page-header :title="team.name" back>
            <btn v-if="$can('team.manage')" color="red" inverted @click="destroy()">
                <icon name="trash" /> Delete
            </btn>
        </page-header>

        <team-form :team="team" />

        <div class="flex items-center justify-between mb-4">
            <tabs
                value="users"
                :tabs="[
                    { value: 'users', label: 'Users' },
                ]"
            />

            <user-picker @input="assignUser">
                <btn inverted>
                    <icon name="plus" /> Assign User
                </btn>
            </user-picker>
        </div>

        <user-list :users="users" @remove="removeUser" />
    </div>
</template>

<script>
import UserList from '@/app/user/list.vue'
import TeamForm from './form.vue'
import UserPicker from '@/app/user/picker.vue'

export default {
    name: 'TeamUpdate',
    props: {
        team: Object,
        users: Object,
    },
    components: {
        UserList,
        TeamForm,
        UserPicker,
    },
    metaInfo: { title: 'Update Team' },
    methods: {
        destroy () {
            this.$confirm({
                title: 'Delete Team',
                message: `Are you sure to delete team ${this.team.name}?`,
                onConfirmed: () => {
                    this.$inertia.delete(this.route('team.delete', { id: this.team.id }))
                }
            })
        },
        assignUser (user) {
            this.$inertia
                .form({ user: {
                    id: user.id,
                    join_team: this.team.id,
                }})
                .post(this.route('user.update', { id: user.id }))
        },
        removeUser (user) {
            this.$inertia
                .form({ user: {
                    id: user.id,
                    leave_team: this.team.id,
                }})
                .post(this.route('user.update', { id: user.id }))
        }
    }
}
</script>
