<template>
    <div class="shadow rounded-lg border-b border-gray-200 w-full bg-white">
        <template v-if="!config.disableHeader">
            <div class="rounded-t-md">
                <div class="pl-4 pr-3 md:flex md:justify-between md:items-center">
                    <div class="text-sm text-gray-700 mb-2 md:mb-0">
                        <slot name="meta">
                            <div class="flex items-center">
                                <div>
                                    Total <span class="font-semibold">{{ meta.total || 0 }}</span> {{ meta.total | pluralize('record') }}
                                </div>

                                <div
                                    v-if="totalFilters > 0"
                                    class="bg-yellow-100 text-yellow-800 shadow font-semibold text-xs ml-2 py-0.5 rounded-full flex items-center"
                                >
                                    <a class="ml-2 mr-0.5">
                                        {{ totalFilters }} {{ totalFilters | pluralize('filter') }}
                                    </a>
                                    <a class="flex justify-center items-center" @click="reset()">
                                        <icon name="x" size="16px" />
                                    </a>
                                </div>
                            </div>
                        </slot>
                    </div>

                    <div class="flex flex-col md:flex-row">
                        <div class="py-1.5" v-if="checked && checked.length">
                            <slot name="checked" />
                        </div>

                        <template v-else>
                            <slot name="toolbar">
                                <form class="my-2 mx-1" @submit.prevent="search()" v-if="!config.disableSearch">
                                    <div class="flex items-center bg-gray-100 px-2 py-1.5 rounded-md">
                                        <icon name="search" size="18px" class="text-gray-400" />
                                        <input
                                            type="text"
                                            v-model="searchText"
                                            class="w-full appearance-none text-sm p-0 border-0 bg-transparent"
                                            placeholder="Search"
                                        >
                                        <a class="flex items-center justify-center" @click="searchText = null; search()" v-if="searchText">
                                            <icon name="x" size="20px" class="text-gray-400" />
                                        </a>
                                    </div>
                                </form>

                                <div class="flex items-center">
                                    <a
                                        v-tooltip="'Export'"
                                        class="text-lg p-1.5 m-1 rounded flex items-center justify-center hover:bg-gray-100"
                                        @click="extract"
                                        v-if="extractUrl"
                                    >
                                        <icon name="download" />
                                    </a>

                                    <a
                                        v-tooltip="'Filters'"
                                        class="text-lg p-1.5 m-1 rounded flex items-center justify-center hover:bg-gray-100"
                                        @click="openDrawer()"
                                        v-if="$slots.filters"
                                    >
                                        <icon name="slider" />
                                    </a>
                                </div>
                            </slot>
                        </template>
                    </div>
                </div>

                <div class="border-t p-3" v-if="tabs.length">
                    <tabs :tabs="tabs" />
                </div>
            </div>

            <div class="fixed top-0 bottom-0 -right-80 w-80" ref="drawer">
                <slot name="filters" />
            </div>
        </template>

        <template v-if="!data.length">
            <slot name="empty">
                <cta />
            </slot>
        </template>

        <template v-else>
            <div class="w-full overflow-auto">
                <table>
                    <thead>
                        <tr>
                            <th class="px-3" width="30" v-if="checked">
                                <checkbox 
                                    :value="checked.length === data.length"
                                    @input="check($event ? 'all' : null)" 
                                />
                            </th>
                            <th v-for="field in fields" :key="field.key">
                                <a
                                    v-if="field.sort"
                                    :class="[
                                        'flex items-center hover:underline',
                                        field.align === 'right' && 'flex items-center justify-end',
                                        field.align === 'center' && 'flex items-center justify-center',
                                    ]"
                                    @click="sort(field)"
                                >
                                    {{ getLabel(field) }}
                                    
                                    <icon v-if="orderBy === `${field.sort}__asc`" name="down-arrow-alt" size="16px" />
                                    <icon v-if="orderBy === `${field.sort}__desc`" name="up-arrow-alt" size="16px" />
                                </a>

                                <div 
                                    v-else
                                    :class="[
                                        'text-gray-500',
                                        field.align === 'right' && 'text-right',
                                        field.align === 'center' && 'text-center',
                                    ]"
                                >
                                    {{ getLabel(field) }}
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(row, i) in data"
                            :key="i"
                            :class="['hover:bg-gray-50', i < data.length - 1 && 'border-b']"
                        >
                            <td class="px-3 align-top" v-if="checked">
                                <checkbox
                                    :value="checked.some(val => (val[trackby] === row[trackby]))"
                                    @input="check(row)"
                                />
                            </td>

                            <td v-for="field in fields" :key="field.key" :class="['align-top', getTableCellClass(field)]">
                                <template v-if="field.actions">
                                    <template v-if="getActions(field, row)">
                                        <template v-if="Array.isArray(getActions(field, row))">
                                            <dropdown>
                                                <a class="p-0.5 flex justify-center items-center">
                                                    <icon name="dots-horizontal" size="16px" />
                                                </a>

                                                <template #items>
                                                    <a
                                                        v-for="action in getActions(field, row)"
                                                        :key="action.name"
                                                        @click="action.action(row)"
                                                    >
                                                        <icon :name="action.icon" /> {{ action.name }}
                                                    </a>
                                                </template>
                                            </dropdown>
                                        </template>

                                        <template v-else>
                                            <a
                                                :class="[
                                                    'px-1.5 flex justify-center items-center',
                                                    ['remove', 'delete'].includes(getActions(field, row).name.toLowerCase()) && 'text-red-500',
                                                ]"
                                                v-tooltip="getActions(field, row).name"
                                                @click="getActions(field, row).action(row)"
                                            >
                                                <icon :name="getActions(field, row).icon" size="16px" />
                                            </a>
                                        </template>
                                    </template>

                                    <template v-else />
                                </template>

                                <template v-else-if="field.status">
                                    <badge
                                        v-for="status in getStatus(field, row)"
                                        :key="status"
                                        :status="status"
                                    />
                                </template>

                                <template v-else-if="field.isImage">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden">
                                        <img
                                            :src="getValue(field, row)"
                                            class="object-cover h-full w-full"
                                            v-if="getValue(field, row)"
                                        >
                                    </div>
                                </template>

                                <template v-else-if="field.isProgress">
                                    <progress-bar :progress="parseInt(getValue(field, row))" />
                                </template>

                                <template v-else>
                                    <div v-if="field.tag" class="float-right">
                                        <span
                                            v-for="tag in getTags(field, row)"
                                            :key="tag"
                                            class="text-xs py-1 px-2 m-0.5 bg-gray-100 rounded"
                                        >
                                            {{ tag }}
                                        </span>
                                    </div>

                                    <template v-if="field.link && field.link(row)">
                                        <inertia-link 
                                            v-if="getValue(field, row)"
                                            :href="field.link(row)" 
                                            class="font-medium text-blue-400 hover:text-blue-500"
                                        >
                                            {{ getValue(field, row) }}
                                        </inertia-link>

                                        <span v-else>--</span>
                                    </template>

                                    <template v-else-if="field.click">
                                        <a 
                                            v-if="getValue(field, row)"
                                            class="font-medium text-blue-400 hover:text-blue-500" 
                                            @click="field.click(row)"
                                        >
                                            {{ getValue(field, row) }}
                                        </a>

                                        <span v-else>--</span>
                                    </template>

                                    <template v-else>
                                        <div v-html="getValue(field, row) || '--'" />
                                    </template>

                                    <template v-if="field.small">
                                        <div 
                                            :class="['text-gray-400 text-xs', field.status && 'mt-2']" 
                                            v-html="field.small(row)" 
                                        />
                                    </template>
                                </template>
                            </td>
                        </tr>

                        <slot name="footer" />
                    </tbody>
                </table>
            </div>

            <div class="px-4 border-t" v-if="meta && meta.last_page > 1">
                <pagination :meta="meta" />
            </div>
        </template>
    </div>
</template>

<script>
import Cta from '../cta.vue'
import Tabs from './tabs.vue'
import Pagination from '../pagination.vue'

export default {
    name: 'DataTable',
    props: {
        checked: Array,
        extractUrl: [String, Boolean],
        trackby: {
            type: String,
            default: 'id',
        },
        data: {
            type: Array,
            default () {
                return []
            }
        },
        fields: {
            type: Array,
            default () {
                return []
            }
        },
        meta: {
            type: Object,
            default () {
                return {}
            }
        },
        config: {
            type: Object,
            default () {
                return {}
            }
        },
        tabs: {
            type: Array,
            default () {
                return []
            },
        },
    },
    components: {
        Cta,
        Tabs,
        Pagination,
    },
    data () {
        return {
            orderBy: this.route().params?.order_by || null,
            searchText: this.route().params?.search || null,
        }
    },
    computed: {
        totalFilters () {
            return this.meta?.filters ? Object.keys(this.meta.filters).length : 0
        },
    },
    watch: {
        data: {
            deep: true,
            handler: function() {
                this.check()
            },
        },
    },
    methods: {
        sort (field) {
            this.orderBy = this.orderBy === `${field.sort}__asc`
                ? `${field.sort}__desc`
                : `${field.sort}__asc`

            const route = this.route().current()
            const params = { ...this.route().params, order_by: this.orderBy }

            this.$inertia.visit(this.route(route, params), { preserveState: true })
        },
        check (row = null) {
            if (row === null) this.$emit('update:checked', [])
            else if (row === 'all') this.$emit('update:checked', [].concat(this.data))
            else {
                const checked = this.checked || []
                const index = this.checked.findIndex(val => (val[this.trackby] === row[this.trackby]))
                
                if (index === -1) checked.push(row)
                else checked.splice(index, 1)

                this.$emit('update:checked', checked)
            }
        },
        search () {
            const route = this.route().current()
            const params = { 
                ...this.route().params, 
                search: this.searchText,
                page: null,
            }

            this.$inertia.visit(this.route(route, params), { preserveState: true })
        },
        reset () {
            this.$inertia.visit(window.location.href.split('?')[0])
        },
        getLabel (field) {
            if (field.label) return field.label
            if (field.status || field.isImage || field.actions) return ''

            return _.startCase(_.toLower(field.key));
        },
        getValue (field, row) {
            const value = _.get(row, field.key)
            const computed = field.computed ? field.computed(row) : value
            const { filters } = this.$options

            if (field.isDate) return filters.dateFormat(computed)
            if (field.isDatetime) return filters.dateFormat(computed, 'datetime', field.isUtcToLocal || false)
            if (field.isFromNow) return filters.dateFormat(computed, 'fromNow', field.isUtcToLocal || false)
            if (field.truncate) return _.truncate(computed, { length: field.truncate })
            if (field.currency) {
                if (field.currency === true) return filters.currency(computed)
                else if (typeof field.currency === 'string') return filters.currency(computed, field.currency)
                else return filters.currency(computed, field.currency(row))
            }

            return computed
        },
        getTableCellClass (field) {
            if (field.align === 'center') return 'text-center'
            if (field.align === 'right') return 'text-right'
            if (field.isImage) return 'w-20'
            if (field.actions) return 'w-10'
            if (field.status) return 'w-20'
        },
        getTableHeaderClass (field) {
            if (field.align === 'center') return `flex items-center justify-center`
            if (field.align === 'right') return `flex items-center justify-end`
        },
        getStatus (field, row) {
            const status = field.status(row)

            return _.flatten([status]).filter(Boolean)
        },
        getTags (field, row) {
            const tags = field.tag(row)

            return _.flatten([tags]).filter(Boolean)
        },
        getActions (field, row) {
            const actions = field.actions(row)

            if (actions.length === 1) return _.head(actions)
            else if (!actions.length) return null

            return actions
        },
        extract () {
            this.$confirm({
                title: 'Extract Data',
                message: `This will export ${this.meta.total} ${this.$options.filters.pluralize(this.meta.total, 'record')}. Continue?`,
                onConfirmed: () => {
                    const qs = (new URLSearchParams(this.meta.filters || {})).toString()
                    window.location = `${this.extractUrl}?${qs}`
                }
            })
        }
    }
}
</script>
