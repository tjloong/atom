<template>
    <div>
        <div class="cursor-pointer" @click="$refs.picker.open()">
            <slot>
                <div class="inline-flex items-center text-blue-500 font-medium">
                    <template v-if="value">
                        {{ value.name }} 
                        <a class="text-red-500 flex items-center justify-center" @click.stop="$emit('clear')">
                            <icon name="x" />
                        </a>
                    </template>

                    <template v-else>
                        {{ placeholder }} <icon name="chevron-down" />
                    </template>
                </div>
            </slot>
        </div>

        <async-picker 
            ref="picker" 
            :placeholder="placeholder" 
            :url="route('user.list')" 
            :payload="{ order_by: 'name__asc' }"
            @input="$emit('input', $event)" 
        >
            <template #option="val">
                <div class="font-medium">
                    {{ val.option.name }}
                </div>
                <div class="text-sm text-gray-500">
                    {{ val.option.email }}
                </div>
            </template>
        </async-picker>
    </div>
</template>

<script>
export default {
    name: 'UserPicker',
    props: {
        value: Object,
        placeholder: {
            type: String,
            default: 'Select User',
        },
    }
}
</script>
