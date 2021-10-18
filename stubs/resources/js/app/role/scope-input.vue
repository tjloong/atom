<template>
    <div class="role-scope-input">
        <template v-if="readonly">
            {{ selected.label }}
            <div class="text-xs text-gray-500">
                {{ selected.description }}
            </div>
        </template>

        <template v-else>
            <div
                class="my-2"
                v-for="opt in options"
                :key="opt.value"
            >
                <checkbox :value="opt.value === selected.value" @input="$emit('input', opt.value)">
                    <div>
                        <div class="font-medium">
                            {{ opt.label }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ opt.description }}
                        </div>
                    </div>
                </checkbox>
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: 'RoleScopeInput',
    props: {
        value: String,
        readonly: Boolean,
    },
    data () {
        return {
            options: [
                this.$auth('is_root') && { value: 'root', label: 'Root Access', description: 'Can access everthing as root' },
                { value: 'global', label: 'Global Access', description: 'Can access all records' },
                { value: 'restrict', label: 'Restricted Access', description: 'Can only access own records' },
            ].filter(Boolean),
        }
    },
    computed: {
        selected () {
            return this.options.find(opt => (opt.value === this.value))
        },
    }
}
</script>
