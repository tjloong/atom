<template>
    <form @submit.prevent="submit()">
        <box>
            <div class="p-5">
                <field v-model="form.user.name" :error="form.errors['user.name']" required>
                    Name
                </field>

                <field v-model="form.user.email" :error="form.errors['user.email']" required email>
                    Login Email
                </field>

                <field 
                    required 
                    v-model="form.user.role_id" 
                    :error="form.errors['user.role_id']" 
                    :options="roles.map(val => ({
                        value: val.id,
                        label: val.name,
                    }))"
                >
                    Role
                </field>

                <field v-if="user.id">
                    Role Scope
                    <template #input>
                        <scope-input :value="user.role.access" readonly />
                    </template>
                </field>

                <div v-else class="bg-blue-100 text-blue-800 p-4 rounded text-sm flex">
                    <icon name="info-circle" class="flex-shrink-0" />
                    <div class="flex-grow self-center">
                        User will receive an invitation email to activate their account once created.
                    </div>
                </div>

                <inertia-link
                    v-if="user.is_pending_activate"
                    :href="route('user.invite', { id: user.id })"
                    method="post"
                    as="button"
                    class="text-blue-500 text-sm inline-flex items-center"
                >
                    <icon name="mail-send" /> Resend invitation email
                </inertia-link>
            </div>

            <template #buttons>
                <btn submit color="green" :loading="form.processing">
                    Save User
                </btn>
            </template>
        </box>
    </form>
</template>

<script>
import ScopeInput from '@/app/role/scope-input.vue'

export default {
    name: 'UserForm',
    props: {
        user: Object,
        roles: Array,
    },
    components: {
        ScopeInput,
    },
    data () {
        return {
            form: this.$inertia.form({ user: {
                name: null,
                email: null,
                role_id: null,
                ..._.pick(this.user, [
                    'id', 'name', 'email', 'role_id',
                ]),
            }})
        }
    },
    methods: {
        submit () {
            this.form.post(
                this.user?.id
                    ? this.route('user.update', { id: this.user.id })
                    : this.route('user.create'),
                { replace: true }
            )
        },
    }
}
</script>
