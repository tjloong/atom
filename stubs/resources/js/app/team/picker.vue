<template>
    <div>
        <div class="cursor-pointer" @click="$refs.picker.open()">
            <slot />
        </div>

        <async-picker 
            ref="picker" 
            :placeholder="placeholder" 
            :url="route('team.list')" 
            @input="$emit('input', $event)" 
        />
    </div>
</template>

<script>
export default {
    name: 'TeamPicker',
    props: {
        value: [Number, String],
        placeholder: {
            type: String,
            default: 'Select Team',
        },
    }
}
</script>
