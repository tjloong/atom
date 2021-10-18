<template>
    <div class="max-w-lg mx-auto">
        <page-header title="Create Team" back />
        <team-form :team="{}" />
    </div>
</template>

<script>
import TeamForm from './form.vue'

export default {
    name: 'TeamCreate',
    components: {
        TeamForm,
    },
    metaInfo: { title: 'Create Team' },
}
</script>
