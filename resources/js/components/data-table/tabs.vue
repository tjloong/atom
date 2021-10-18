<template>
    <div class="inline-flex flex-wrap items-center text-sm">
        <template v-for="(item, i) in tabs">
            <dropdown
                v-if="item.dropdown"
                :key="`dd-${i}`"
            >
                <div :class="getStyles(item)">
                    <div :class="['flex items-center', isActive(item) && 'text-xs leading-tight -mb-0.5']">
                        {{ item.label }} <icon name="chevron-down" />
                    </div>

                    <div v-if="isActive(item)" class="text-gray-500 leading-tight" style="font-size: 0.65rem">
                        {{ item.dropdown.find(dd => (route().params[dd.key] === dd.value)).label }}
                    </div>
                </div>

                <template #items>
                    <a 
                        v-for="dd in item.dropdown"
                        :key="dd.value"
                        @click="select(dd)"
                    >
                        {{ dd.label }}
                    </a>
                </template>
            </dropdown>

            <a 
                v-else
                :key="item.value" 
                :class="getStyles(item)"
                @click="select(item)"
            >
                {{ item.label }}
            </a>
        </template>
    </div>
</template>

<script>
export default {
    name: 'DataTableTabs',
    props: {
        tabs: Array,
    },
    methods: {
        getStyles (tab) {
            const styles = ['flex-shrink-0 p-1 mx-2']

            if (this.isActive(tab)) styles.push('text-theme border-b-2 border-theme font-medium')

            return styles
        },
        isActive (tab) {
            if (tab.dropdown) return tab.dropdown.some(dd => (this.route().params[dd.key] === dd.value))
            else if (tab.value) return this.route().params[tab.key] === tab.value
            else return !this.route().params[tab.key]
        },
        select (tab) {
            const route = this.route().current()
            const params = {
                ...this.route().params,
                [tab.key]: tab.value,
            }

            this.$inertia.visit(this.route(route, params), { replace: true, preserveState: true })
        },
    }
}
</script>
