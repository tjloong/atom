<template>
    <div class="max-w-screen-xl mx-auto">
        <page-header title="Files">
            <btn v-if="checked.length" color="red" :loading="deleting" inverted @click="destroy()">
                <icon name="trash" /> Delete ({{ checked.length }})
            </btn>

            <btn v-else @click="$refs.uploader.open()">
                <icon name="upload" /> Upload
            </btn>
        </page-header>

        <template v-if="files.data.length">
            <div class="grid grid-cols-2 gap-4 md:grid-cols-6">
                <file-card 
                    v-for="file in files.data"
                    :key="file.id" 
                    :file="file" 
                    :checked="checked.some(v => (v.id === file.id))"
                    @edit="$refs.form.open(file)" 
                    @click="select(file)"
                />
            </div>

            <div class="mt-8 px-4" v-if="files.meta.last_page > 1">
                <pagination :meta="files.meta" />
            </div>
        </template>

        <cta v-else title="No File Found" subtitle="Your file list is empty" />

        <file-uploader
            ref="uploader"
            multiple
            title="Upload Files"
            :url="route('file.upload')"
            :accept="['image', 'pdf', 'youtube']"
        />

        <file-form ref="form" />
    </div>
</template>

<script>
import FileForm from './form.vue'

export default {
    name: 'FileList',
    props: {
        files: Object,
    },
    components: {
        FileForm,
    },
    metaInfo: { title: 'Files' },
    data () {
        return {
            checked: [],
            deleting: false,
        }
    },
    methods: {
        select (file) {
            const index = this.checked.findIndex(v => (v.id === file.id))

            if (index === -1) this.checked.push(file)
            else this.checked.splice(index, 1)
        },
        destroy () {
            this.$confirm({
                title: 'Delete Files?',
                message: 'Are you sure to delete the selected files?',
                onConfirmed: () => {
                    this.deleting = true
                    this.$inertia.delete(this.route('file.delete', { id: _.map(this.checked, 'id').join(',') }), {
                        onSuccess: () => {
                            this.deleting = false
                            this.checked = []
                        }
                    })
                },
            })
        }
    }
}
</script>
