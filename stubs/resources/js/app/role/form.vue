<template>
    <form @submit.prevent="submit()">
        <box v-if="readonly">
            <div class="p-5">
                <field>
                    Role Name
                    <template #input>
                        {{ role.name }}
                    </template>
                </field>

                <field>
                    Scope
                    <template #input>
                        <scope-input :value="role.access" readonly />
                    </template>
                </field>
            </div>
        </box>

        <box v-else>
            <div class="p-5">
                <field v-model="form.role.name" :error="form.errors['role.name']" required>
                    Role Name
                </field>

                <field 
                    v-if="!form.role.id"
                    v-model="form.role.clone_from_id" 
                    :options="clonables.map(val => ({
                        value: val.id,
                        label: $options.filters.titlecase(val.name),
                    }))"
                >
                    Clone From Role
                </field>

                <field>
                    Scope
                    <template #input>
                        <scope-input v-model="form.role.access" />
                    </template>
                </field>
            </div>

            <template #buttons>
                <btn submit color="green" :loading="form.processing">
                    Save Role
                </btn>
            </template>
        </box>
    </form>
</template>

<script>
import ScopeInput from './scope-input.vue'

export default {
    name: 'RoleForm',
    props: {
        role: Object,
        clonables: Array,
    },
    components: {
        ScopeInput,
    },
    data () {
        return {
            form: this.$inertia.form({ role: {
                name: null,
                access: 'global',
                clone_from_id: null,
                ..._.pick(this.role, [
                    'id',
                    'name',
                    'access',
                ]),
            }})
        }
    },
    computed: {
        readonly () {
            return this.role?.id && this.role.is_system
        },
    },
    methods: {
        submit () {
            this.form.post(
                this.role?.id
                    ? this.route('role.update', { id: this.role.id })
                    : this.route('role.create'),
                { replace: true }
            )
        },
    }
}
</script>
