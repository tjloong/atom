<template>
    <div class="options-checker flex flex-wrap">
        <a
            v-for="opt in list"
            :key="opt.value"
            :class="[
                'rounded flex border bg-white m-1',
                getSize(),
                isSelected(opt) ? 'border-green-500 text-green-500 font-medium' : 'border-gray-400 text-gray-400',
            ]"
            @click="select(opt)"
        >
            <icon v-if="isSelected(opt)" name="check" class="flex-shrink-0" />
            <div class="flex-grow self-center">
                {{ opt.label }}
            </div>
        </a>
    </div>
</template>

<script>
export default {
    name: 'OptionsChecker',
    props: {
        value: {
            default: null,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        options: {
            type: Array,
            default () {
                return []
            },
        },
        size: {
            type: String,
            default: 'sm',
        },
    },
    computed: {
        list () {
            return (this.options || []).map(opt => {
                if (_.has(opt, 'value') && _.has(opt, 'label')) return opt
                else if (_.has(opt, 'value')) return { value: opt.value, label: opt.value }
                else if (_.has(opt, 'label')) return { value: opt.label, label: opt.label }
                else return { value: opt, label: opt }
            })
        },
    },
    methods: {
        isSelected (opt) {
            if (this.multiple) {
                const value = this.value || []
                return value.includes(opt.value)
            }
            else return this.value === opt.value
        },
        select (opt) {
            if (this.multiple) {
                const value = this.value || []
                const index = value.findIndex(val => (val === opt.value))

                if (index === -1) value.push(opt.value)
                else value.splice(index, 1)

                this.$emit('input', value)
            }
            else this.$emit('input', opt.value)

            this.$emit('change', opt)
        },
        getSize () {
            if (this.size === 'xs') return 'py-1 px-2 text-xs'
            else return 'py-1 px-2 text-sm'
        }
    }
}
</script>
