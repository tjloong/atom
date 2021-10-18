<template>
    <div>
        <div class="inline-block bg-theme-light text-theme-dark py-2 px-4 rounded font-semibold md:hidden">
            <a class="flex items-center" @click="open()">
                <icon name="menu" />
                <template v-if="title">
                    {{ title }} <icon name="chevron-down" />
                </template>
            </a>
        </div>

        <div 
            ref="dropdown" 
            class="
                bg-white shadow-lg rounded-md border p-4 w-60 z-10 hidden md:p-0
                md:bg-transparent md:shadow-none md:border-0 md:block md:w-auto
            "
        >
            <div
                v-for="(items, group) in getTabs()"
                :key="group"
                class="mb-4"
            >
                <p class="text-xs text-gray-400 font-medium uppercase mb-1 px-2">
                    {{ group | titlecase }}
                </p>

                <div class="flex flex-col">
                    <a
                        v-for="item in items"
                        :key="item.label"
                        :class="[
                            'py-1.5 px-4 text-sm font-medium text-gray-500 rounded-l md:px-2', 
                            item.value === value
                                ? 'font-bold text-gray-800 bg-gray-200 md:border-r-8 md:border-theme' 
                                : 'hover:text-gray-800 hover:bg-gray-100',
                        ]"
                        @click="$emit('input', item.value)"
                    >
                        {{ item.label }}
                    </a>
                </div>
            </div>
        </div>
    </div>    
</template>

<script>
import { createPopper } from '@node/@popperjs/core'

export default {
    name: 'SideTabs',
    props: {
        tabs: [Object, Array],
        value: [String, Number],
    },
    data () {
        return {
            show: false,
            popper: null,
        }
    },
    computed: {
        title () {
            const tabs = _.flatten(Object.values(this.getTabs()))
            const active = tabs.find(tab => (tab.value === this.value))

            return active?.label || 'Navigations'
        },
    },
    methods: {
        getTabs () {
            if (Array.isArray(this.tabs)) return { 'navigation': this.tabs }
            else return this.tabs
        },
        open () {
            this.$refs.dropdown.classList.remove('hidden')

            this.popper = createPopper(this.$el, this.$refs.dropdown, {
                placement: 'bottom-start',
            })

            window.addEventListener('click', this.dismiss)
        },
        dismiss (e) {
            if (this.$el.contains(e.target)) return
			this.$refs.dropdown.classList.add('hidden')
            setTimeout(() => this.popper.destroy(), 100)
        },
    }
}
</script>
