<template>
    <div>
        <page-header title="Dashboard">
            <daterange v-model="date" />
        </page-header>
    </div>
</template>

<script>
export default {
    name: 'Home',
    metaInfo: { title: 'Dashboard' },
    data () {
        return {
            date: null,
        }
    }
}
</script>
