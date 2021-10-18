import _ from 'lodash'
import axios from 'axios'
import Vue from 'vue'
import VueMeta from 'vue-meta'
import { Inertia } from '@inertiajs/inertia'
import { InertiaProgress } from '@inertiajs/progress'
import { createInertiaApp, Link } from '@inertiajs/inertia-vue'
import Atom from '@atom/resources/js'
import Layout from '@/app/layout'

Vue.config.productionTip = false

Vue.use(Atom)
Vue.use(VueMeta)
Vue.component('inertia-link', Link)

InertiaProgress.init()

window._ = _
window.dd = console.log.bind(console)
window.axios = axios

createInertiaApp({
    resolve: name => {
        const page = require(`./${name}`).default

        if (page.layout === undefined) page.layout = Layout

        return page
    },
    setup({ el, app, props }) {
        window.app = new Vue({
            metaInfo: {
                titleTemplate: title => (title ? `${title} | App` : 'App'),
            },
            render: h => h(app, props),
        }).$mount(el)
    },
})