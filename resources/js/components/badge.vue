<template>
    <span
        :class="[
            'font-semibold',
            getColor(),
            size === 'xs' && 'rounded px-2',
            size === 'sm' && 'text-xs rounded-full py-1 px-2',
            size === 'md' && 'text-base rounded-full py-1 px-3',
            size === 'lg' && 'text-xl rounded-full py-1 px-3',
        ]"
        :style="[
            size === 'xs' && { fontSize: '0.7rem', paddingTop: '0.15rem', paddingBottom: '0.15rem' },
        ]"
    >
        <template v-if="status">
            {{ status }}
        </template>

        <template v-else>
            <slot />
        </template>
    </span>
</template>

<script>
export default {
    name: 'Badge',
    props: {
        color: {
            type: String,
            default: null,
        },
        status: {
            type: String,
            default: null,
        },
        size: {
            type: String,
            default: 'sm',
        }
    },
    methods: {
        getColor () {
            let color

            if (this.color) color = this.color
            else if (this.status) {
                if (['error', 'failed', 'blocked', 'due'].includes(this.status)) color = 'red'
                if (['admin', 'queueing', 'unpaid', 'submitted', 'checked-out'].includes(this.status)) color = 'yellow'
                if (['default', 'partial', 'pending', 'feedback', 'shipped', 'processing', 'ready'].includes(this.status)) color = 'blue'
                if (['paid', 'billed', 'delivered', 'invoiced', 'active', 'closed', 'sent', 'verified', 'success', 'published'].includes(this.status)) color = 'green'
            }

            if (!color) color = 'gray'

            if (color === 'black') return 'text-white bg-black'
            else if (color === 'gray') return 'text-gray-900 bg-gray-200'
            else return `text-${color}-800 bg-${color}-100`
        },
    }
}
</script>