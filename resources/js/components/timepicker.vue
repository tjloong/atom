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
                :value="value" 
                :placeholder="placeholder"
                @click="show = true"
            >
            <a class="absolute top-0 right-0 bottom-0 w-10 flex items-center justify-center" @click="value ? $emit('input', null) : (show = true)">
                <icon :name="value ? 'x' : 'time'" />
            </a>
        </div>

        <div v-show="show" ref="time" class="absolute right-0 mt-1 z-10">
            <div ref="timepicker" />
        </div>
    </div>
</template>

<script>
import { createPopper } from '@node/@popperjs/core'
import flatpickr from '@node/flatpickr'
import '@node/flatpickr/dist/flatpickr.css'

export default {
    name: 'Timepicker',
    props: {
        value: {
            type: String,
            default: null,
        },
        readonly: {
            type: Boolean,
            default: true,
        },
        placeholder: {
            type: String,
            default: 'Select time',
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
        }
    },
    watch: {
        show (show) {
            if (show) {
                this.$nextTick(() => {
                    this.popper = createPopper(this.$el, this.$refs.time, {
                        placement: 'bottom-end',
                    })

                    this.picker = flatpickr(this.$refs.timepicker, {
                        inline: true,
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H:i',
                        time_24hr: true,
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
        onChange (selectedDates, dateStr, instance) {
            this.$emit('input', dateStr)
        },
    }
}
</script>
