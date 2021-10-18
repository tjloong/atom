<template>
    <modal ref="modal" title="Update Permissions" size="sm" @close="mod = null">
        <form v-if="mod" @submit.prevent="submit()">
            <field>
                Module
                <template #input>
                    {{ mod.name | titlecase }}
                </template>
            </field>

            <field>
                Permissions
                <template #input>
                    <div
                        class="my-1"
                        v-for="ability in mod.abilities"
                        :key="ability.id"
                    >
                        <checkbox :value="ability.enabled" @input="toggle(ability)">
                            {{ ability.name | titlecase }}
                        </checkbox>
                    </div>
                </template>
            </field>

            <btn submit color="green">
                Save Permissions
            </btn>
        </form>
    </modal>
</template>

<script>
export default {
    name: 'AbilityForm',
    data () {
        return {
            mod: null,
        }
    },
    methods: {
        open (mod) {
            this.mod = { ...mod }
            this.$refs.modal.open()
        },
        close () {
            this.$refs.modal.close()
        },
        toggle (ability) {
            this.mod.abilities = this.mod.abilities.map(v => {
                if (v.id === ability.id) return { ...v, enabled: !v.enabled }
                else return v
            })
        },
        submit () {
            this.$emit('submit', this.mod.abilities)
            this.close()
        },
    }
}
</script>
