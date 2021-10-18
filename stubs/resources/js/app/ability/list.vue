<template>
    <div>
        <box v-for="group in groups" :key="group.name" >
            <div class="font-semibold p-4 mb-4">
                {{ group.name }}
            </div>

            <a 
                v-for="(mod, i) in group.mods"
                :key="mod.name"
                :class="[
                    'py-2 px-4 text-sm flex', 
                    i > 0 && 'border-t',
                ]"
                @click="$emit('select', mod)"
            >
                <div class="font-medium w-40 flex-shrink-0 cursor-pointer text-blue-500">
                    {{ mod.name | titlecase }}
                </div>

                <div class="flex flex-wrap items-center">
                    <template v-for="(ability, i) in mod.abilities">
                        <div class="flex items-center" :key="ability.id">
                            <icon v-if="ability.enabled" name="check" class="text-green-500 mr-1" />
                            <icon v-else name="x" class="mr-1 text-red-400" />

                            <div :class="['text-xs', ability.enabled ? 'font-medium text-gray-500' : 'text-gray-400']">
                                {{ ability.name | titlecase }}
                            </div>
                            
                            <div class="mx-2" v-if="i < mod.abilities.length - 1">
                                &bull;
                            </div>
                        </div>
                    </template>
                </div>
            </a>
        </box>
    </div>
</template>

<script>
export default {
    name: 'AbilityList',
    props: {
        abilities: Array,
    },
    computed: {
        groups () {
            const groups = [
                { 
                    name: 'Modules', 
                    abilities: this.abilities.filter(val => ([
                        //
                    ].includes(val.module))),
                },
                {
                    name: 'Settings',
                    abilities: this.abilities
                        .filter(val => ([
                            'team', 'role', 'user',
                        ].includes(val.module)))
                        .map(val => ({ ...val, module: val.module })),
                }
            ]

            return groups
                .map(group => ({
                    name: group.name,
                    mods: _.chain(group.abilities)
                        .groupBy('module')
                        .map((val, key) => ({ name: key, abilities: val }))
                        .value()
                }))
                .filter(group => (group.mods.length > 0))
        },
    },
}
</script>