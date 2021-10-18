<template>
    <div class="max-w-lg mx-auto">
        <page-header title="Create Role" back />
        <role-form :clonables="clonables.data" :errors="errors" />
    </div>
</template>

<script>
import RoleForm from './form.vue'

export default {
    name: 'RoleCreate',
    props: {
        errors: Object,
        clonables: Object,
    },
    components: {
        RoleForm,
    },
    metaInfo: { title: 'Create Role' },
}
</script>
