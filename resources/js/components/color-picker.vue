<template>
    <div class="p-2 bg-gray-200 inline-block rounded">
        <div class="color-picker" />
    </div>
</template>

<script>
import Pickr from '@node/@simonwep/pickr'
import '@node/@simonwep/pickr/dist/themes/monolith.min.css'

export default {
    name: 'ColorPicker',
    props: {
        value: {
            type: String,
            default: null,
        },
        config: {
            type: Object,
            default () {
                return {}
            }
        },
    },
    mounted () {
        this.load()
    },
    methods: {
        load () {
            const defaultConfig = {
                el: '.color-picker',
                theme: 'monolith',
                default: this.value,
                components: {
                    preview: true,
                    opacity: false,
                    hue: true,
                    interaction: {
                        hex: false,
                        rgba: false,
                        hsla: false,
                        hsva: false,
                        cmyk: false,
                        input: false,
                        clear: false,
                        save: false,
                    },
                },
            }

            const pickr = Pickr.create({
                ...defaultConfig,
                ...this.config,
            })

            pickr.on('change', (color, instance) => {
                const hex = color.toHEXA().toString()

                this.$emit('input', hex)
                pickr.setColor(hex)
            })
        }
    }
}
</script>