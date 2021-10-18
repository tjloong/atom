<template>
    <div class="cursor-pointer text-sm" @click="open()">
        <slot />

        <div 
            v-if="show" 
            ref="dropdown" 
            class="bg-white shadow-lg rounded-md border py-1 dropdown-items z-10"
            @click="show = false"
        >
            <slot name="items" />
        </div>
    </div>
</template>

<script>
import { createPopper } from '@node/@popperjs/core'

export default {
    name: 'Dropdown',
    props: {
        placement: {
            type: String,
            default: 'bottom-end',
        },
    },
    data () {
        return {
            show: false,
            popper: null,
        }
    },
    watch: {
        show (show) {
            if (show) {
                this.$nextTick(() => {
                    this.popper = createPopper(this.$el, this.$refs.dropdown, {
                        placement: this.placement,
                    })
                })
            }
            else if (this.popper) {
                setTimeout(() => this.popper.destroy(), 100)
            }
        }
    },
    methods: {
        open () {
            this.show = true
            window.addEventListener('click', this.dismiss)
        },
        dismiss (e) {
            if (this.$el.contains(e.target)) return
            this.show = false
        },
    }
}
</script>

<style>
.dropdown-items > * {
    display: flex;
    align-items: center;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    padding-left: 1rem;
    padding-right: 1.5rem;
}
.dropdown-items > *:hover {
    background-color: #eee;
    color: black;
}
.dropdown-items > * > .icon {
    margin-right: 0.5rem;
}
</style>
