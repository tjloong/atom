<template>
    <div class="form-input flex">
        <div class="text-gray-500 flex-shrink-0 mr-1">
            https://...{{ prefix }}
        </div>

        <div class="flex-grow">
            <input 
                type="text" 
                :value="value" 
                class="w-full border-0 p-0 text-sm focus:outline-none focus:border-0" 
                @input="$emit('input', $event.target.value)"
            >
        </div>
    </div>
</template>

<script>
export default {
    name: 'UrlPathInput',
    props: {
        value: {
            type: String,
            default: null,
        },
        prefix: {
            type: String,
            default: null,
        },
    },
}
</script>
