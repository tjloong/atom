import Alert from './plugins/alert.js'
import Device from './plugins/device.js'

import Box from './components/box.vue'
import Btn from './components/btn.vue'
import Cta from './components/cta.vue'
import Tabs from './components/tabs.vue'
import Icon from './components/icon.vue'
import Badge from './components/badge.vue'
import Field from './components/field.vue'
import Modal from './components/modal.vue'
import Toggler from './components/toggler.vue'
import Checkbox from './components/checkbox.vue'
import Dropdown from './components/dropdown.vue'
import SideTabs from './components/side-tabs.vue'
import FileCard from './components/file/card.vue'
import Daterange from './components/daterange.vue'
import DataTable from './components/data-table/main.vue'
import Datepicker from './components/datepicker.vue'
import Timepicker from './components/timepicker.vue'
import Pagination from './components/pagination.vue'
import AdminPanel from './components/admin-panel/layout.vue'
import PageHeader from './components/page-header.vue'
import AsyncPicker from './components/async-picker.vue'
import FileUploader from './components/file/uploader.vue'
import OptionsChecker from './components/options-checker.vue'

import Nl2br from './filters/nl2br.js'
import Gender from './filters/gender.js'
import Address from './filters/address.js'
import Slugify from './filters/slugify.js'
import Currency from './filters/currency.js'
import Truncate from './filters/truncate.js'
import Pluralize from './filters/pluralize.js'
import Titlecase from './filters/titlecase.js'
import DateRange from './filters/date-range.js'
import DateFormat from './filters/date-format.js'

import Tooltip from './directives/tooltip.js'
import Mixin from './mixin.js'

export default {
    install (Vue, options = {}) {
        Vue.use(Alert)
        Vue.use(Device)

        Vue.component('box', Box)
        Vue.component('btn', Btn)
        Vue.component('cta', Cta)
        Vue.component('tabs', Tabs)
        Vue.component('icon', Icon)
        Vue.component('badge', Badge)
        Vue.component('field', Field)
        Vue.component('modal', Modal)
        Vue.component('toggler', Toggler)
        Vue.component('checkbox', Checkbox)
        Vue.component('dropdown', Dropdown)
        Vue.component('side-tabs', SideTabs)
        Vue.component('daterange', Daterange)
        Vue.component('file-card', FileCard)
        Vue.component('data-table', DataTable)
        Vue.component('datepicker', Datepicker)
        Vue.component('timepicker', Timepicker)
        Vue.component('pagination', Pagination)
        Vue.component('admin-panel', AdminPanel)
        Vue.component('page-header', PageHeader)
        Vue.component('async-picker', AsyncPicker)
        Vue.component('file-uploader', FileUploader)
        Vue.component('options-checker', OptionsChecker)

        Vue.filter('nl2br', Nl2br)
        Vue.filter('gender', Gender)
        Vue.filter('address', Address)
        Vue.filter('slugify', Slugify)
        Vue.filter('currency', Currency)
        Vue.filter('truncate', Truncate)
        Vue.filter('pluralize', Pluralize)
        Vue.filter('titlecase', Titlecase)
        Vue.filter('dateRange', DateRange)
        Vue.filter('dateFormat', DateFormat)

        Vue.directive('tooltip', Tooltip)

        Vue.mixin(Mixin)
    }
}