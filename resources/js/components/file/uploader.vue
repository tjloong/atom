<template>
    <modal ref="modal" :title="title" size="2xl" @close="tab = null">
        <div v-if="form && form.processing" class="h-32 flex items-center justify-center">
            <div class="py-2 px-4 bg-gray-900 opacity-80 rounded-md text-white relative overflow-hidden">
                <div v-if="form.progress" class="absolute inset-0 bg-green-500" :style="{ width: `${form.progress.percentage}%` }" />

                <div class="flex items-center relative">
                    <icon name="radio-circle" size="md" class="mr-1.5" animation="burst" />
                    <div class="font-medium">
                        <template v-if="tab === 'device' && form.progress && form.progress.percentage >= 100">
                            Processing...
                        </template>

                        <template v-else-if="tab === 'device'">
                            Uploading
                            <span v-if="form.progress">{{ form.progress.percentage }}%</span>
                        </template>

                        <template v-else>
                            Saving...
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <template v-else>
            <div class="mb-4">
                <tabs
                    :value="tab"
                    :tabs="tabs"
                    @input="tab = $event"
                />
            </div>

            <div v-if="tab === 'device'" class="h-64">
                <dropzone 
                    :multiple="multiple"
                    :accept="types"
                    @input="submit" 
                />
            </div>

            <url-form v-else-if="tab === 'urls'" :multiple="multiple" @submit="submit" />
            <youtube-form v-else-if="tab === 'youtube'" :multiple="multiple" @submit="submit" />
            <library v-else-if="tab === 'library'" :multiple="multiple" @submit="submit" />
            <library v-else-if="tab === 'library-image'" :multiple="multiple" @submit="submit" image-only />
        </template>
    </modal>
</template>

<script>
import Tabs from '../tabs.vue'
import Library from './library.vue'
import UrlForm from './url-form.vue'
import Dropzone from './dropzone.vue'
import YoutubeForm from './youtube-form.vue'

export default {
    name: 'FileUploader',
    props: {
        url: String,
        title: String,
        payload: Object,
        multiple: Boolean,
        accept: {
            type: Array,
            default () {
                return [
                    'image',
                    'pdf',
                    'msoffice',
                    'youtube',
                    'library',
                ]
            },
        },
    },
    components: {
        Tabs,
        Library,
        UrlForm,
        Dropzone,
        YoutubeForm,
    },
    data () {
        return {
            tab: null,
            form: null,
        }
    },
    computed: {
        types () {
            let types = []

            if (this.accept.includes('image')) types = types.concat(['image/png', 'image/jpg', 'image/jpeg', 'image/webp'])
            if (this.accept.includes('pdf')) types = types.concat(['application/pdf'])
            if (this.accept.includes('msoffice')) {
                types = types.concat([
                    'application/msword', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ])
            }

            return types
        },
        tabs () {
            let tabs = []

            if (this.accept.includes('image')) {
                tabs.push({ value: 'device', label: this.$device === 'mobile' ? 'From Phone' : 'From Computer' })
                tabs.push({ value: 'urls', label: 'Web Images' })
            }

            if (this.accept.includes('youtube')) tabs.push({ value: 'youtube', label: 'Youtube' })

            if (this.accept.includes('library')) tabs.push({ value: 'library', label: 'Library' })
            else if (this.accept.includes('library-image')) tabs.push({ value: 'library-image', label: 'Library' })

            return tabs
        },
    },
    methods: {
        open () {
            this.tab = this.tabs[0].value
            this.$refs.modal.open()
        },
        close () {
            this.$refs.modal.close()
        },
        submit (data) {
            if (data.library) {
                this.$emit('completed', this.multiple ? data.library : data.library[0])
                this.close()
            }
            else {
                if (data.files) {
                    const size = data.files.reduce((acc, file) => (acc + file.size), 0)
                    const mb = size/1024/1024

                    if (mb >= 8) {
                        this.close()
                        return this.$alert('Upload file size too large. Please reduce your file size to below 10MB.', 'alert')
                    }
                }

                this.form = this.$inertia.form({ upload: data })
                this.form.post(this.url, {
                    onSuccess: () => {
                        const result = this.$page.props.async
                        this.$emit('completed', this.multiple ? result : result[0])
                        this.close()
                    },
                })
            }
        },
    }
}
</script>
