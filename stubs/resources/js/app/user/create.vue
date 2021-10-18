<template>
    <div class="max-w-lg mx-auto">
        <page-header title="Create User" back />
        <user-form :user="{}" :roles="roles" />
    </div>
</template>

<script>
import UserForm from './form.vue'

export default {
    name: 'UserCreate',
    props: {
        roles: Array,
    },
    components: {
        UserForm,
    },
    metaInfo: { title: 'Create User' },
}
</script>
