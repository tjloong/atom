<template>
    <div
        id="aside-menu"
        :class="[
            'w-56 fixed top-0 bottom-0 z-20 transition-all duration-75',
            isRevealed ? 'left-0 lg:left-0' : '-left-56 lg:-left-56',
        ]"
    >
        <a
            id="aside-menu-burger"
            :class="[
                'absolute -right-12 top-0 z-20 w-12 h-12 flex items-center justify-center bg-white',
                isRevealed && 'hidden lg:flex',
            ]" 
            @click="isRevealed = !isRevealed"
        >
            <icon name="menu" />
        </a>

        <div 
            id="aside-menu-void"
            :class="[
                'fixed inset-0 bg-gray-800 opacity-70 z-10 lg:hidden',
                isRevealed ? 'block' : 'hidden',
            ]"
            @click="isRevealed = false"
        />

        <div class="absolute inset-0 z-20 bg-gray-800 flex flex-col text-white overflow-y-auto overflow-x-hidden">
            <div class="flex items-center py-3 px-5 flex-shrink-0">
                <img src="/storage/img/logo.svg" class="w-7 mr-2">
                <div>
                    <span class="font-light">{{ brand.split('::')[0] }}</span><span class="font-bold">{{ brand.split('::')[1] }}</span>
                </div>
            </div>

            <div class="flex-grow">
                <div class="text-xs text-gray-500 py-2 px-6">
                    NAVIGATION
                </div>

                <template v-for="menu in menus">
                    <div
                        :key="menu.label" 
                        :class="[
                            'block cursor-pointer', 
                            menu.expanded && menu.dropdown && menu.dropdown.length ? 'bg-gray-900' : 'bg-gray-800'
                        ]"
                        @click="navigate(menu)"
                    >
                        <div 
                            :class="[
                                'py-2.5 px-3 ml-2 rounded-l-md flex items-center',
                                menu.active && `bg-gray-600 border-theme border-r-8`,
                                !menu.active && !menu.expanded && 'hover:bg-gray-700',
                            ]"
                        >
                            <icon v-if="menu.icon" :name="menu.icon" class="flex-shrink-0" />

                            <div class="mx-2 flex-grow text-sm">
                                {{ menu.label }}
                            </div>

                            <icon
                                v-if="menu.dropdown.length"
                                :name="menu.expanded ? 'chevron-down' : 'chevron-right'"
                                class="flex-shrink-0"
                            />
                        </div>
                    </div>

                    <template v-if="menu.dropdown && menu.dropdown.length && menu.expanded">
                        <div
                            :key="`dropdown-for-${menu.label}`"
                            class="bg-gray-900 text-gray-300 pb-2"
                        >
                            <div
                                v-for="item in menu.dropdown"
                                :key="item.label"
                                :class="[
                                    'block py-1.5 pl-7 pr-2 ml-6 rounded-l-md text-sm cursor-pointer',
                                    item.active && `bg-gray-600 border-theme border-r-8`,
                                ]"
                                @click="navigate(item)"
                            >
                                {{ item.label }}
                            </div>
                        </div>
                    </template>
                </template>
            </div>

            <div class="flex-shrink-0" v-if="support">
                <a class="py-2 px-4 ml-2 mb-2 rounded-l-full flex items-center" @click="$refs.support.open()">
                    <icon name="buoy" class="flex-shrink-0" />
                    <div class="ml-2 flex-grow text-sm hover:font-medium">
                        Support
                    </div>
                </a>
            </div>
        </div>

        <support v-if="support" ref="support" :phone="support.phone" :email="support.email" />
    </div>
</template>

<script>
import Support from './support'

export default {
    name: 'AsideMenu',
    props: {
        navs: Array,
        brand: String,
        support: Object,
    },
    components: {
        Support,
    },
    data () {
        return {
            isRevealed: true,
            menus: this.getMenus(),
        }
    },
    watch: {
        isRevealed (val) {
            if (val) this.$emit('revealed')
            else this.$emit('hidden')
        },
        navs () {
            this.menus = this.getMenus()
        },
    },
    mounted () {
        this.dismissForSmallScreen()
    },
    methods: {
        getMenus () {
            return this.navs
                .filter(menu => (menu.url || (menu.dropdown && menu.dropdown.length)))
                .map(menu => ({ ...menu, dropdown: menu.dropdown || [] }))
                .map(menu => ({
                    ...menu,
                    expanded: menu.active || menu.dropdown.some(child => (child.active)),
                }))
        },
        dismissForSmallScreen () {
            if (window.innerWidth < 1024) this.isRevealed = false
        },
        navigate (menu) {
            if (menu.url) this.$inertia.visit(menu.url)

            if (menu.dropdown && menu.dropdown.length) {
                this.menus = this.menus.map(val => ({
                    ...val,
                    expanded: val.label === menu.label,
                }))
            }
            else this.dismissForSmallScreen()
        },
    }
}
</script>
