<template>
    <layout tab="abilities" v-bind="$props">
        <div v-if="role.is_root" class="bg-blue-50 text-blue-800 text-sm p-4 flex">
            <icon name="info-circle" class="mr-1 flex-shrink-0" />
            Root can access everything in the system.
        </div>

        <div v-else-if="role.is_admin" class="bg-blue-50 text-blue-800 text-sm p-4 flex">
            <icon name="info-circle" class="mr-1 flex-shrink-0" />
            Administrator can access all modules in the system.
        </div>

        <template v-else>
            <ability-list :abilities="abilities" @select="$refs.form.open($event)" />
            <ability-form ref="form" :role="role" @submit="submit" />
        </template>
    </layout>
</template>

<script>
import Layout from './layout.vue'
import AbilityList from '@/app/ability/list.vue'
import AbilityForm from '@/app/ability/form.vue'

export default {
    name: 'RoleUpdateAbilities',
    props: {
        tab: String,
        tabs: Array,
        role: Object,
        abilities: Array,
    },
    components: {
        Layout,
        AbilityList,
        AbilityForm,
    },
    methods: {
        submit (value) {
            const abilities = _.map(this.abilities, (ability) => {
                return value.find(val => (val.id === ability.id)) || ability
            })

            this.$inertia
                .form({ role: {
                    id: this.role.id,
                    abilities: _.map(abilities.filter(val => (val.enabled)), 'id'),
                }})
                .post(
                    this.route('role.update', { id: this.role.id }),
                    { replace: true }
                )
        },
    }
}
</script>
