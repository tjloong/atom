<template>
    <layout tab="users" v-bind="$props">
        <template #action>
            <user-picker ref="picker" @input="assignUser">
                <btn inverted>
                    <icon name="plus" /> Assign User
                </btn>
            </user-picker>
        </template>

        <user-list :users="users" />
    </layout>
</template>

<script>
import Layout from './layout.vue'
import UserList from '@/app/user/list.vue'
import UserPicker from '@/app/user/picker.vue'

export default {
    name: 'RoleUpdateUsers',
    props: {
        tab: String,
        tabs: Array,
        role: Object,
        users: Object,
    },
    components: {
        Layout,
        UserList,
        UserPicker,
    },
    methods: {
        assignUser (user) {
            this.$inertia
                .form({ user: {
                    id: user.id,
                    role_id: this.role.id,
                }})
                .post(
                    this.route('user.update', { id: user.id }),
                    { replace: true }
                )
        },
    }
}
</script>