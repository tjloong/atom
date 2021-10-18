const mixins = {
    methods: {
        $auth (prop) {
            return _.get(this.$page.props.auth, prop)
        },
        $can (ability) {
            return _.get(this.$page.props.auth?.can, ability)
        },
        $reload () {
            this.$inertia.reload({ preserveState: false, preserveScroll: false })
            window.removeEventListener('popstate', this.$reload)
        },
        $back () {
            window.addEventListener('popstate', this.$reload)
            window.history.back()
        },
    },
}

if (window.route !== undefined) mixins.methods.route = route

export default mixins