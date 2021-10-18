<template>
    <div class="relative">
        <a v-if="$slots.default" @click="value ? $emit('input', null) : (show = true)">
            <slot />
        </a>

        <div v-else class="relative">
            <input 
                readonly
                type="text" 
                class="w-full form-input pr-10" 
                :value="$options.filters.dateFormat(value)" 
                :placeholder="placeholder"
                @click="show = true"
            >
            <a class="absolute top-0 right-0 bottom-0 w-10 flex items-center justify-center" @click="value ? $emit('input', null) : (show = true)">
                <icon :name="value ? 'x' : 'calendar'" />
            </a>
        </div>

        <div v-show="show" ref="calendar" class="absolute mt-1 z-10">
            <div ref="datepicker" />
        </div>
    </div>
</template>

<script>
import { createPopper } from '@node/@popperjs/core'
import flatpickr from '@node/flatpickr'
import '@node/flatpickr/dist/flatpickr.css'

export default {
    name: 'Datepicker',
    props: {
        value: {
            type: String,
            default: null,
        },
        readonly: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String,
            default: 'Select date',
        },
        config: {
            type: Object,
            default () {
                return {}
            },
        },
    },
    data () {
        return {
            show: false,
            picker: null,
            popper: null,
        }
    },
    watch: {
        show (show) {
            if (show) {
                this.$nextTick(() => {
                    this.popper = createPopper(this.$el, this.$refs.calendar, {
                        placement: 'bottom-end',
                    })

                    this.picker = flatpickr(this.$refs.datepicker, {
                        inline: true,
                        dateFormat: 'Y-m-d',
                        defaultDate: this.value,
                        ...this.config,

                        onChange: this.onChange,
                    })

                    window.addEventListener('click',this.dismiss)
                })
            }
            else {
                if (this.popper) setTimeout(() => this.popper.destroy(), 100)
                if (this.picker) setTimeout(() => this.picker.destroy(), 100)
            }
        },
    },
    methods: {
        dismiss (e) {
            if (this.$el.contains(e.target)) return
            this.show = false
        },
        onChange (selectedDates, dateStr) {
            this.$emit('input', dateStr)
            this.show = false
        },
    }
}
</script>

<style>
.flatpickr-day.selected {
    background: #bdf6eb !important;
    border-color: #bdf6eb !important;
    color: #10715e !important;
}
</style>
