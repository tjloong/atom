<template>
    <layout tab="abilities" v-bind="$props">
        <div v-if="user.role.access === 'root'" class="bg-blue-50 text-blue-800 text-sm p-4 flex">
            <icon name="info-circle" class="flex-shrink-0" />
            Root user can access all every aspect in the system.
        </div>

        <div v-else-if="user.is_admin" class="bg-blue-50 text-blue-800 text-sm p-4 flex">
            <icon name="info-circle" class="flex-shrink-0" />
            Administrator can access all modules in the system.
        </div>

        <template v-else>
            <div v-if="user.abilities.length" class="bg-blue-100 text-blue-800 rounded p-4 mb-4">
                <div class="text-sm flex mb-1">
                    <icon name="info-circle" class="flex-shrink-0" />
                    <div class="flex-grow self-center">
                        User's permissions were edited and is different from the role's preset.<br>
                        <a class="text-blue-500 text-xs" @click="resetAbilities()">
                            Reset to role's preset
                        </a>
                    </div>
                </div>
            </div>

            <ability-list :abilities="abilities" @select="$refs.abilityForm.open($event)" />
            <ability-form ref="abilityForm" @submit="updateAbilities" />
        </template>
    </layout>
</template>

<script>
import Layout from './layout.vue'
import AbilityList from '@/app/ability/list.vue'
import AbilityForm from '@/app/ability/form.vue'

export default {
    name: 'UserUpdateAbilities',
    props: {
        tabs: Array,
        user: Object,
        roles: Array,
        abilities: Array,
    },
    components: {
        Layout,
        AbilityList,
        AbilityForm,
    },
    methods: {
        updateAbilities (updated) {
            updated.forEach(update => {
                const index = this.user.abilities.findIndex(v => (v.id === update.id))
                const access = update.enabled ? 'grant' : 'forbid'

                if (index === -1) this.user.abilities.push({ ...update, pivot: { access }})
                else this.user.abilities[index].pivot = { access }
            })

            this.$inertia
                .form({ user: {
                    id: this.user.id,
                    abilities: _.chain(this.user.abilities)
                        .groupBy('id')
                        .mapValues(val => (_.head(val).pivot))
                        .value()
                }})
                .post(this.route('user.update', { id: this.user.id }))
        },
        resetAbilities () {
            this.$inertia
                .form({ user: {
                    id: this.user.id,
                    abilities: [],
                }})
                .post(this.route('user.update', { id: this.user.id }))
        },
    }
}
</script>
