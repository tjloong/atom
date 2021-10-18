<template>
    <div v-if="small" class="w-full pt-[100%] bg-gray-200 relative rounded-md shadow overflow-hidden">
        <div class="absolute inset-0 flex items-center justify-center cursor-pointer">
            <template v-if="file.mime === 'youtube'">
                <img v-if="file.data && file.data.vid" :src="`https://img.youtube.com/vi/${file.data.vid}/default.jpg`" class="w-full h-full object-cover">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-4 h-4 bg-white" />
                </div>
                <div class="absolute inset-0 flex items-center justify-center text-red-500">
                    <icon name="youtube" type="logo" size="32px" />
                </div>
            </template>
            <img v-else-if="file.mime.startsWith('image/')" :src="file.url" class="w-full h-full object-contain">
            <icon v-else-if="file.type === 'pdf'" name="file-pdf" type="solid" size="64px" />
            <icon v-else name="file" type="solid" size="64px" />

            <div :class="['absolute inset-0 bg-gray-800', checked ? 'opacity-60' : 'opacity-0']" />

            <div class="absolute top-0 left-0 m-1.5">
                <checkbox :value="checked" />
            </div>

            <div class="absolute inset-0" @click="$emit('click')" />
        </div>


        <div class="absolute bottom-0 left-0 right-0 px-2 pb-2 pt-4 text-white bg-gradient-to-t from-black to-transparent opacity-80 overflow-hidden">
            <div class="flex flex-wrap">
                <div class="flex-grow self-center mr-1.5 whitespace-nowrap overflow-hidden overflow-ellipsis text-xs">
                    {{ file.name }}
                </div>

                <div class="flex-shrink-0" v-if="$listeners.edit">
                    <button type="button" @click="$emit('edit', file)">
                        <icon name="edit" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="bg-white rounded-md shadow overflow-hidden">
        <div class="w-full pt-[100%] bg-gray-200 relative cursor-pointer">
            <div class="absolute inset-0 flex items-center justify-center">
                <template v-if="file.mime === 'youtube'">
                    <img v-if="file.data && file.data.vid" :src="`https://img.youtube.com/vi/${file.data.vid}/default.jpg`" class="w-full h-full object-cover">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-8 h-8 bg-white" />
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center text-red-500">
                        <icon name="youtube" type="logo" size="64px" />
                    </div>
                </template>
                <img v-else-if="file.mime.startsWith('image/')" :src="file.url" class="w-full h-full object-contain">
                <icon v-else-if="file.type === 'pdf'" name="file-pdf" type="solid" size="64px" />
                <icon v-else name="file" type="solid" size="64px" />
            </div>

            <div :class="['absolute inset-0 bg-gray-800', checked ? 'opacity-60' : 'opacity-0']" />

            <div class="absolute top-0 left-0 m-1.5">
                <checkbox :value="checked" />
            </div>

            <div class="absolute inset-0" @click="$emit('click')" />
        </div>

        <div class="py-3 px-4 border">
            <div 
                :class="[
                    'font-semibold text-sm mb-1.5 whitespace-nowrap overflow-hidden overflow-ellipsis',
                    $listeners.edit && 'text-blue-500 cursor-pointer',
                ]"
                @click="$listeners.edit && $emit('edit', file)"
            >
                {{ file.name }}
            </div>

            <div class="text-xs">
                <span v-if="file.type">{{ file.type }}</span>
                <span v-if="file.size">/ {{ file.size }}</span>
                <span v-if="file.data && file.data.dimension">/ {{ file.data.dimension }}</span>
            </div>

            <div v-if="file.owner" class="text-xs text-gray-500">
                By {{ file.owner.name }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'FileCard',
    props: {
        file: Object,
        small: Boolean,
        checked: Boolean,
    },
}
</script>
