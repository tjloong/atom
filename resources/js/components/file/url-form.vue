<template>
    <div>
        <field v-if="multiple" v-model="text" :props="{ rows: 6 }" textarea required>
            Image URL
            <template #caption>
                Insert multiple images by separating lines
            </template>
        </field>

        <field v-else v-model="text" required>
            Image URL
            <template #caption>
                Insert multiple images by separating lines
            </template>
        </field>

        <field v-if="urls.length">
            Preview
            <template #input>
                <div class="flex flex-wrap items-center">
                    <div
                        v-for="url in urls"
                        :key="url.href"
                        class="w-24 h-24 bg-gray-200 rounded-md m-1.5 overflow-hidden"
                    >
                        <img v-if="url.valid" :src="url.href" class="w-full h-full object-cover" @error="url.valid = false">
                        <div v-else class="w-full h-full flex justify-center items-center text-red-400">
                            <icon name="error-circle" size="64px" />
                        </div>
                    </div>
                </div>
            </template>
        </field>

        <btn v-if="this.urls.filter(v => (v.valid)).length" color="green" @click="submit()">
            Save Images
        </btn>
    </div>
</template>

<script>
export default {
    name: 'UrlForm',
    props: {
        multiple: Boolean,
    },
    data () {
        return {
            text: null,
            urls: [],
        }
    },
    watch: {
        text (text) {
            if (!text) this.urls = []
            else {
                this.urls = this.text
                    .split("\n")
                    .filter(Boolean)
                    .map(url => ({
                        href: url,
                        valid: true,
                    }))
            }
        },
    },
    methods: {
        submit () {
            this.$emit('submit', { 
                urls: this.urls
                    .filter(url => (url.valid))
                    .map(url => (url.href))
            })
        }
    }
}
</script>
