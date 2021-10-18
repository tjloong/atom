<template>
    <div class="box bg-white rounded-md shadow border mb-6">
        <div v-if="$slots.default" class="p-1.5">
            <slot />
        </div>

        <div v-if="$slots.buttons" class="py-3 px-5 bg-gray-100">
            <slot name="buttons" />
        </div>
    </div>
</template>

<script>
export default {
    name: 'Box',
    props: {
        title: {
            type: String,
            default: null,
        },
    },
}
</script>
