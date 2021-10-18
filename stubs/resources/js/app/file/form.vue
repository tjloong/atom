<template>
    <modal ref="modal" title="Update File" size="md" @close="file = form = null">
        <form v-if="file" @submit.prevent="submit()">
            <div v-if="isImage" class="relative mb-4 pt-[100%]">
                <div class="absolute inset-0 border rounded-md shadow">
                    <img :src="file.url" class="w-full h-full object-contain">
                </div>
            </div>

            <div v-else-if="file.type === 'youtube'" class="relative mb-4 pt-[80%]">
                <div class="absolute inset-0">
                    <iframe :src="file.url" class="w-full h-full" />
                </div>
            </div>

            <template v-else>
                <field>
                    File Type
                    <template #input>
                        {{ file.type.toUpperCase() }}
                    </template>
                </field>
            </template>

            <field v-model="file.name" required>
                Name
            </field>

            <field v-model="file.data.description" textarea>
                Description
            </field>

            <field v-if="isImage" v-model="file.data.alt">
                Alt Text
            </field>

            <div class="flex items-center">
                <btn submit color="green" :loading="form && form.processing" class="mr-2">
                    Save File
                </btn>

                <btn v-if="isImage && (!form || !form.processing)" inverted @click="download()">
                    <icon name="download" /> Download
                </btn>
            </div>
        </form>
    </modal>
</template>

<script>
export default {
    name: 'FileForm',
    data () {
        return {
            file: null,
            form: null,
        }
    },
    computed: {
        isImage () {
            return this.file && this.file.mime.startsWith('image/')
        },
    },
    methods: {
        open (file) {
            this.file = { 
                ...file,
                data: {
                    alt: null,
                    description: null,
                    ...file.data,
                }
            }

            this.$refs.modal.open()
        },
        close () {
            this.$refs.modal.close()
        },
        download () {
            window.open(this.file.url, '_blank')
        },
        submit () {
            this.form = this.$inertia.form({ file: _.pick(this.file, ['id', 'name', 'data']) })
            this.form.post(this.route('file.store', { id: this.file.id }), {
                onSuccess: () => this.close()
            })
        }
    }
}
</script>
