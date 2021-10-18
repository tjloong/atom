<template>
    <div class="my-3 flex flex-wrap justify-between items-center" v-if="meta.last_page > 1">
        <div class="text-sm leading-5 text-gray-600 my-1 mr-2 flex-shrink-0">
            SHOWING
            <span class="font-medium">{{ meta.from }}</span>
            TO
            <span class="font-medium">{{ meta.to }}</span>
            OF
            <span class="font-medium">{{ meta.total }}</span>
        </div>

        <div class="inline-flex flex-wrap text-sm self-end">
            <div
                v-for="(val, i) in links"
                :key="`${val.label}-${i}`"
                :class="[
                    '-ml-px my-0.5 text-sm border border-gray-300 flex items-center justify-center bg-white hover:bg-gray-100',
                    i === 0 && 'rounded-l-md',
                    i === links.length - 1 && 'rounded-r-md',
                    val.label !== '...' && 'cursor-pointer',
                    val.icon ? 'py-1 px-0.5' : 'py-1 px-2',
                ]"
                @click="change(val)"
            >
                <icon v-if="val.icon" :name="val.icon" />
                <span v-else :class="val.active && `font-bold text-blue-500`">
                    {{ val.label }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Pagination',
    props: {
        meta: {
            type: Object,
            default () {
                return {}
            },
        },
        preserveState: {
            type: Boolean,
            default: true,
        },
    },
    computed: {
        links () {
            const links = this.meta.links.map((val, i) => {
                if (i === 0) return { ...val, label: this.meta.current_page - 1, icon: 'chevron-left' }
                if (i === this.meta.links.length - 1) return { ...val, label: this.meta.current_page + 1, icon: 'chevron-right' }
                return val
            })

            if (this.meta.current_page === 1) links.shift()
            if (this.meta.current_page === this.meta.last_page) links.pop()

            return links
        },
    },
    methods: {
        change (link) {
            const page = link.label
            if (page === '...') return

            const urlSearchParams = new URLSearchParams(window.location.search)
            const qs = { ...Object.fromEntries(urlSearchParams.entries()), page }

            this.$inertia.reload({
                method: 'get',
                data: qs,
            })
        },
    },
}
</script>