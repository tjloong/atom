<template>
    <div
        :class="[
            'fixed inset-0 z-20',
            !active && 'hidden',
            fullscreen && 'bg-white',
        ]"
    >
        <div
            class="fixed inset-0 bg-gray-800 opacity-75 z-30"
            @click="bgClose && close()"
            v-if="!fullscreen"
        />

        <div class="relative w-full h-full overflow-auto">
            <div class="absolute z-30 w-full px-4 py-6 md:py-20">
                <slot name="container">
                    <div 
                        :class="[
                            'bg-white rounded mx-auto w-full',
                            contentWidth,
                        ]"
                    >
                        <div class="flex items-center justify-between pt-5 px-6">
                            <div class="text-xl font-bold flex-grow">
                                <slot name="title">
                                    {{ title }}
                                </slot>
                            </div>

                            <button type="button" class="text-xl" @click="close()">
                                <icon name="x" size="md" />
                            </button>
                        </div>

                        <div class="p-6">
                            <slot />
                        </div>
                    </div>
                </slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Modal',
    props: {
        title: String,
        size: String,
        bgClose: Boolean,
        fullscreen: Boolean,
    },
    data () {
        return {
            active: false,
        }
    },
    computed: {
        contentWidth () {
            const size = this.size || 'md'

            if (size === 'sm') return 'max-w-sm'
            if (size === 'md') return 'max-w-md'
            if (size === 'lg') return 'max-w-lg'
            if (size === 'xl') return 'max-w-xl'
            if (size === '2xl') return 'max-w-2xl'
            if (size === '3xl') return 'max-w-3xl'
            if (size === '4xl') return 'max-w-4xl'
            if (size === '5xl') return 'max-w-5xl'

            return size
        },
    },
    methods: {
        open () {
            document.querySelector('html').classList.add('overflow-hidden')
            this.active = true
        },
        close () {
            document.querySelector('html').classList.remove('overflow-hidden')
            this.active = false
            this.$emit('close')
        },
    }
}
</script>