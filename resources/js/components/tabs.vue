<template>
    <div class="tabs">
        <div
            :class="[
                'inline-flex items-center flex-wrap text-sm', 
                !inverted && 'p-1 font-medium bg-gray-100 rounded-md',
            ]"
        >
            <template v-for="(item, i) in getTabs()">
                <dropdown 
                    v-if="item.dropdown.length"
                    :key="`tab-dd-${i}`" 
                    class="flex-shrink-0"
                >
                    <div :class="getStyles(item)">
                        <div class="flex items-center">
                            {{ 
                                isActive(item)
                                ? item.dropdown.find(val => (val.value === value)).label
                                : item.label 
                            }} 
                            <icon name="chevron-down" />
                        </div>
                    </div>

                    <template #items>
                        <a 
                            v-for="dd in item.dropdown"
                            :key="dd.value"
                            @click="select(dd.value)"
                        >
                            {{ dd.label }}
                        </a>
                    </template>
                </dropdown>

                <a
                    :key="item.value"
                    :class="getStyles(item)"
                    @click="select(item.value)"
                    v-else
                >
                    {{ item.label }}
                </a>
            </template>
        </div>
    </div>
</template>

<script>
import Dropdown from './dropdown.vue'

export default {
    name: 'Tabs',
    props: {
        value: [String, Number],
        tabs: Array,
        inverted: Boolean,
    },
    components: {
        Dropdown,
    },
    computed: {
    },
    methods: {
        getTabs () {
            return this.tabs.filter(Boolean)
                .map(tab => {
                    if (typeof tab === 'string') return { value: tab, label: tab }
                    else return tab
                })
                .map(tab => ({
                    ...tab,
                    dropdown: tab.dropdown || [],
                }))
        },
        getStyles (tab) {
            const styles = ['flex-shrink-0 py-1.5 px-3']

            if (this.inverted) {
                if (this.isActive(tab)) styles.push('text-theme border-b-2 border-theme font-medium')
                else styles.push('text-gray-400 border-b-2 border-transparent hover:text-gray-600')
            }
            else {
                if (this.isActive(tab)) styles.push('bg-white text-theme font-bold shadow rounded-md')
                else styles.push('text-gray-600 rounded hover:text-gray-800')
            }

            return styles
        },
        isActive (tab) {
            return this.value === tab.value
                || (tab.dropdown.some(dd => (dd.value === this.value)))
        },
        select (val) {
            if (this.value !== val) this.$emit('input', val)
        },
    }
}
</script>