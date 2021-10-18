<template>
    <div>
        <div class="bg-gray-200 rounded-md px-2 py-1.5 flex items-center mb-6">
            <icon name="search" class="text-gray-500" size="18px" />

            <input 
                type="text"
                ref="input"
                v-model="text"
                placeholder="Search"
                class="flex-grow w-full appearance-none text-sm p-0 border-0 bg-transparent"
                @input="search()"
            >

            <a 
                v-if="text !== null"
                class="px-2 flex items-center justify-center" 
                @click="text = null; fetch()" 
            >
                <icon name="x" />
            </a>
        </div>

        <div v-if="options.length" class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <file-card 
                small 
                v-for="opt in options"
                :key="opt.id" 
                :file="opt" 
                :checked="checked.some(v => (v.id === opt.id))"
                @click="select(opt)"
            />
        </div>

        <div v-if="loading" class="flex items-center justify-center text-theme">
            <icon name="radio-circle" size="64px" animation="burst" />
        </div>

        <cta v-else-if="!options.length" small />

        <div v-else-if="paginate" class="px-4 pt-4 text-center">
            <btn inverted @click="page++; fetch()">
                Load more <icon name="down-arrow-alt" />
            </btn>
        </div>

        <btn v-if="checked.length" color="green" class="mt-4" @click="submit()">
            Select {{ checked.length }} {{ checked.length | pluralize('File') }}
        </btn>
    </div>
</template>

<script>
import FileCard from './card.vue'

export default {
    name: 'FileLibrary',
    props: {
        multiple: Boolean,
        imageOnly: Boolean,
    },
    components: {
        FileCard,
    },
    data () {
        return {
            page: 1,
            text: null,
            results: null,
            options: [],
            checked: [],
            loading: false,
        }
    },
    computed: {
        paginate () {
            if (!this.results) return false
            return this.results.meta?.last_page > this.results.meta?.current_page
        }
    },
    created () {
        this.fetch()
    },
    methods: {
        search: _.debounce(function () {
            this.fetch()
        }, 300),

        select (file) {
            const index = this.checked.findIndex(val => (val.id === file.id))

            if (index === -1) {
                if (!this.multiple) this.checked = []
                this.checked.push(file)
            }
            else this.checked.splice(index, 1)
        },
        fetch () {
            this.loading = true

            const filters = {
                search: this.text || null,
                page: this.page,
                order_by: 'created_at__desc',
            }

            if (this.imageOnly) filters.is_image = true

            this.$inertia
                .form(filters)
                .post(this.route('file.list'), {
                    onSuccess: () => {
                        this.results = this.$page.props.async
                        this.options = this.page === 1 
                            ? this.results.data 
                            : this.options.concat(this.results.data)
                    },
                    onFinish: () => this.loading = false,
                })
        },
        submit () {
            this.$emit('submit', { library: this.checked })
        }
    }
}
</script>
