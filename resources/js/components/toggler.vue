<template>
    <a class="inline-flex" @click="$emit('input', !value)">
        <div
            :class="[
                'inline-block w-10 h-5 border-2 m-1 rounded-full flex-shrink-0 flex items-center',
                value ? 'bg-theme border-theme justify-end' : 'bg-gray-300 border-gray-300 justify-start',
            ]"
        >
            <div class="w-4 h-4 bg-white rounded-full shadow flex items-center justify-center">
                <icon name="check" class="text-theme" size="20px" v-if="value" />
            </div>
        </div>

        <div :class="['text-sm', !isEmpty && 'ml-1']">
            <div class="flex items-center h-full">
                <slot />
            </div>
        </div>
    </a>
</template>

<script>
export default {
    name: 'Toggler',
    props: {
        value: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        isEmpty () {
            return _.isEmpty(this.$slots)
        }
    },
}
</script>
