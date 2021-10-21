<template>
    <div class="w-full h-full overflow-auto relative">
        <div
            v-if="state.unsupported"
            class="file-dropzone border-4 border-red-500 bg-red-100 text-red-500"
            @dragleave.prevent="state.hovering = state.unsupported = false"
            @dragend.prevent="state.hovering = state.unsupported = false"
            @drop.prevent="state.hovering = state.unsupported = false"
            @dragover.prevent
            @dragenter.prevent
        >
            <icon name="error-circle" size="2xl" class="mb-2" />
            <div class="font-medium">
                File type is not supported
            </div>
        </div>

        <div
            v-else-if="state.hovering"
            class="file-dropzone border-4 border-green-500 bg-green-100 text-green-500"
            @dragleave.prevent="state.hovering = state.unsupported = false"
            @dragend.prevent="state.hovering = state.unsupported = false"
            @drop.prevent="read($event.dataTransfer.files)"
            @dragover.prevent
            @dragenter.prevent
        >
            <icon name="download" size="2xl" class="mb-2" />
            <div class="font-medium">
                Drop here to upload
            </div>
        </div>

        <template v-else>
            <div
                ref="dropzone"
                class="file-dropzone border-2 border-gray-500 bg-white cursor-pointer"
                @dragover.prevent="scan"
                @dragenter.prevent="scan"
                @click="browse()"
            >
                <icon name="cloud-upload" size="2xl" class="mb-2" />
                <div class="font-medium">
                    Add file
                </div>
                <div class="text-sm">
                    Or drop file to upload
                </div>
            </div>
        </template>

        <input 
            ref="input"
            type="file" 
            class="hidden" 
            :accept="accept"
            :multiple="multiple"
            @change="read($event.target.files)"
        >
    </div>
</template>

<script>
export default {
    name: 'Dropzone',
    props: {
        multiple: Boolean,
        accept: {
            type: Array,
            default () {
                return [
                    'image/png',
                    'image/jpeg',
                    'application/pdf',
                ]
            },
        },
    },
    data () {
        return {
            files: [],
            state: {
                reading: false,
                hovering: false,
                unsupported: false,
            },
        }
    },
    methods: {
        browse () {
            this.$refs.input.click()
        },
        scan (e) {
            const items = Array.from(e.dataTransfer.items).filter(item => item.kind === 'file')

            if (items.length) {
                this.state.unsupported = items.some(item => (!this.accept.includes(item.type)))
                this.state.hovering = !this.state.unsupported
            }
        },
        read (files) {
            this.state.reading = true
            this.state.hovering = false
            this.state.unsupported = false

            Array.from(files).forEach((file, i) => {
                if (!this.multiple && i > 0) return

                // filter system files
                if (/\.(php5?|html?|jsx?)$/i.test(file.name)) return
                if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(file.name)) return

                file.blob = URL.createObjectURL(file)

                this.files.push(file)
            })

            this.state.reading = false
            this.$emit('input', { files: this.files })
        },
    }
}
</script>

<style>
.file-dropzone {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-style: dashed;
    border-radius: 0.75rem;
}
</style>
