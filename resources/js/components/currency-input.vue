<template>
    <div class="relative">
        <div class="absolute top-0 left-0 px-3 h-full flex items-center justify-center font-medium text-sm text-gray-400">
            {{ currency }}
        </div>

        <input 
            type="text" 
            class="w-full form-input" 
            style="padding-left: 3.5rem;"
            :value="formatted" 
            @input="emitValue"
        >
    </div>
</template>

<script>
export default {
    name: 'CurrencyInput',
    props: {
        value: [String, Number],
        currency: String,
    },
    computed: {
        formatted () {
            if (Number.isFinite(parseFloat(this.value))) return this.value.toLocaleString('en-US')
            else return null
        },
    },
    methods: {
        emitValue (e) {
            let val = e.target.value

            val = val.replace(/[^\d\.]+/g, '')
            val = val.replace(/(\..*)\./g, '$1')
            val = parseFloat(val)

            if (!val || !Number.isFinite(val)) this.$emit('input', null)
            else this.$emit('input', val)
        },
    }
}
</script>
