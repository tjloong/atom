<template>
    <div class="min-h-screen">
        <div v-show="loading" class="fixed inset-0 z-40">
            <div class="absolute inset-0 bg-white opacity-50" />
            <div class="absolute right-10 bottom-10 p-2 text-theme">
                <icon name="radio-circle" animation="burst" size="32px" />
            </div>
        </div>

        <aside-menu
            :navs="navs"
            :brand="brand"
            :support="support"
            @hidden="onAsideHidden" 
            @revealed="onAsideRevealed" 
        />
        
        <div ref="navbar" class="fixed top-0 left-0 right-0 z-10 transition-all duration-75 lg:ml-56">
            <div class="w-full h-12 shadow bg-white pl-12 pr-4">
                <div class="flex items-center">
                    <div class="flex-grow flex items-center">
                        <slot name="navbar-left" />
                    </div>

                    <div class="flex-shrink-0">
                        <slot name="navbar-right" />
                    </div>
                </div>
            </div>
        </div>

        <div ref="container" class="min-h-screen transition-all duration-75 lg:ml-56">
            <div class="mt-12">
                <notif />

                <div class="pt-8 pb-24 px-6">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Notif from './notif'
import AsideMenu from './aside-menu'

export default {
    name: 'Layout',
    props: {
        navs: [Array, Boolean],
        brand: String,
        support: Object,
        loading: Boolean,
    },
    components: {
        Notif,
        AsideMenu,
    },
	methods: {
		onAsideHidden () {
			this.$refs.navbar.classList.remove('lg:ml-56')
			this.$refs.container.classList.remove('lg:ml-56')
		},
		onAsideRevealed () {
			this.$refs.navbar.classList.add('lg:ml-56')
			this.$refs.container.classList.add('lg:ml-56')
		},
    },
}
</script>