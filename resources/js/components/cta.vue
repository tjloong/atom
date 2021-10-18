<template>
    <div :class="['w-10/12 mx-auto flex flex-col justify-center items-center', small ? 'my-8' : 'my-12']">
        <div :class="small ? 'w-20' : 'w-40'">
            <slot name="img">
                <cart-svg v-if="type === 'cart'" class="w-full h-full" />
                <empty-svg v-if="type === 'empty'" class="w-full h-full" />
                <close-svg v-if="type === 'close'" class="w-full h-full" />
            </slot>
        </div>

        <div class="mt-4 mb-4">
            <div :class="['font-bold text-gray-600 text-center', small ? 'text-md' : 'text-lg']">
                {{ title }}
            </div>
            <div :class="['text-md text-center text-gray-500', small ? 'text-sm' : 'text-md']">
                {{ subtitle }}
            </div>
        </div>

        <slot />
    </div>
</template>

<script>
import CartSvg from './svg/cart-svg.vue'
import EmptySvg from './svg/empty-svg.vue'
import CloseSvg from './svg/close-svg.vue'

export default {
    name: 'Cta',
    props: {
        type: {
            type: String,
            default: 'empty',
        },
        title: {
            type: String,
            default: 'Sorry, we couldn\'t find any matches',
        },
        subtitle: {
            type: String,
            default: 'Please try with another search criteria',
        },
        small: {
            type: Boolean,
            default: false,
        }
    },
    components: {
        CartSvg,
        EmptySvg,
        CloseSvg,
    }
}
</script>