<template>
    <div class="field w-full text-sm mb-5">
        <template v-if="$slots.default">
            <label class="block text-xs font-medium leading-5 text-gray-400 uppercase mb-1.5">
                <slot>
                    {{ label }} 
                </slot>
                <icon v-if="required" name="health" size="8px" class="text-red-400" />
            </label>
        </template>

        <input
            v-if="email" 
            type="email" 
            v-bind="props" 
            :class="['w-full form-input', error && 'form-input-error']" 
            :value="value"
            :required="required"
            @input="$emit('input', $event.target.value)"
        >
        
        <input 
            v-else-if="number" 
            type="number" 
            v-bind="props" 
            :class="['w-full form-input', error && 'form-input-error']" 
            :value="value"
            :required="required"
            @input="$emit('input', $event.target.value)"
        >

        <div 
            v-else-if="password" 
            class="relative w-full"
        >
            <input 
                v-bind="props" 
                :type="showPw ? 'text' : 'password'" 
                :class="['w-full form-input', error && 'form-input-error']" 
                :value="value"
                :required="required"
                @input="$emit('input', $event.target.value)"
            >
            <a class="absolute top-0 right-0 bottom-0 flex items-center justify-center w-12" @click.prevent="showPw = !showPw">
                <icon :name="showPw ? 'hide' : 'show'" />
            </a>
        </div>

        <div 
            v-else-if="percentage"
            class="relative w-60"
        >
            <input 
                type="number" 
                min="0" 
                max="100"
                step="any" 
                v-bind="props" 
                :value="value"
                :class="['w-full form-input', error && 'form-input-error']" 
                @input="$emit('input', $event.target.value)" 
                style="padding-right: 1.5rem;"
            >
            <div class="absolute top-0 bottom-0 right-0 px-3 flex items-center justify-center">
                %
            </div>
        </div>

        <options-checker
            v-else-if="boolean"
            :value="value"
            :options="[
                { value: true, label: 'Yes' },
                { value: false, label: 'No' },
            ]"
            @input="$emit('input', $event)"
        />

        <options-checker 
            v-else-if="checker"
            v-bind="props"
            :value="value" 
            :options="getSelectOptions()" 
            @input="$emit('input', $event)" 
        />

        <select
            v-else-if="options"
            v-bind="props"
            :value="value"
            :class="['w-full form-input', error && 'form-input-error']" 
            @input="$emit('input', $event.target.value)"
        >
            <option :value="null">-- {{ props.placeholder || 'Please Select' }} --</option>
            <option v-for="opt in getSelectOptions()" :key="opt.value" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>

        <currency-input 
            v-else-if="currency"
            :value="value"
            v-bind="props" 
            @input="$emit('input', $event)" 
        />

        <color-picker 
            v-else-if="color"
            :value="value" 
            @input="$emit('input', $event)" 
        />

        <datepicker 
            v-else-if="date"
            :value="value" 
            :config="props" 
            @input="$emit('input', $event)" 
        />

        <timepicker 
            v-else-if="time"
            :value="value" 
            :config="props" 
            @input="$emit('input', $event)" 
        />

        <textarea
            v-else-if="textarea"
            v-bind="props" 
            :value="value"
            :class="['w-full form-input', error && 'form-input-error']" 
            @input="$emit('input', $event.target.value)" 
        />

        <url-path-input
            v-else-if="url"
            v-bind="props"
            :value="value"
            @input="$emit('input', $event)"
        />

        <phone-input
            v-else-if="phone"
            :value="value"
            :error="error ? true : false"
            @input="$emit('input', $event)"
        />

        <address-input
            v-else-if="address"
            :value="value"
            v-bind="props"
            @input="$emit('input', $event)"
        />

        <div v-else-if="latlng" class="w-full h-96">
            <latlng-input
                :value="value"
                v-bind="props"
                @input="$emit('input', $event)"
            />
        </div>
        
        <template v-else-if="$slots.input">
            <slot name="input" />
        </template>
        
        <input 
            v-else 
            type="text" 
            v-bind="props" 
            :value="value"
            :required="required"
            :class="['w-full form-input', error && 'form-input-error']" 
            @input="$emit('input', $event.target.value)"
        >

        <div v-if="error" class="mt-1 text-red-500 font-medium text-xs">
            {{ error }}
        </div>

        <div v-if="$slots.caption" class="mt-1 text-xs">
            <slot name="caption" />
        </div>
    </div>
</template>

<script>
import Datepicker from './datepicker.vue'
import Timepicker from './timepicker.vue'
import PhoneInput from './phone-input.vue'
import ColorPicker from './color-picker.vue'
import LatlngInput from './latlng-input.vue'
import AddressInput from './address-input.vue'
import UrlPathInput from './url-path-input.vue'
import CurrencyInput from './currency-input.vue'
import OptionsChecker from './options-checker.vue'

export default {
    name: 'Field',
    props: {
        value: [String, Number, Boolean, Array, Object],
        url: Boolean,
        date: Boolean,
        time: Boolean,
        error: String,
        color: Boolean,
        email: Boolean,
        state: Boolean,
        phone: Boolean,
        latlng: Boolean,
        number: Boolean,
        address: Boolean,
        boolean: Boolean,
        checker: Boolean,
        options: [Array, String],
        country: Boolean,
        currency: Boolean,
        password: Boolean,
        textarea: Boolean,
        required: Boolean,
        percentage: Boolean,
        props: {
            type: Object,
            default () {
                return {}
            },
        },
    },
    components: {
        PhoneInput,
        Timepicker,
        Datepicker,
        ColorPicker,
        LatlngInput,
        AddressInput,
        UrlPathInput,
        CurrencyInput,
        OptionsChecker,
    },
    data () {
        return {
            showPw: false,
        }
    },
    computed: {
        states () {
            return [
                'Johor',
                'Kedah',
                'Kelantan',
                'Kuala Lumpur',
                'Labuan',
                'Melaka',
                'Negeri Sembilan',
                'Pahang',
                'Perak',
                'Perlis',
                'Pulau Pinang',
                'Putrajaya',
                'Sabah',
                'Sarawak',
                'Selangor',
                'Terengganu',
            ]
        },
        countries () {
            return [
                'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Anguilla', 'Antigua & Barbuda', 'Argentina', 'Armenia', 'Aruba', 'Ascension Island', 'Australia', 'Austria', 'Azerbaijan', 
                'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda', 'Bhutan', 'Bolivia', 'Bosnia & Herzegovina', 'Botswana', 'Brazil', 'British Virgin Islands', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 
                'Cambodia', 'Cameroon', 'Canada', 'Canary Islands', 'Cape Verde', 'Cayman Islands', 'Chad', 'Chile', 'China', 'Colombia', 'Congo', 'Cook Islands', 'Costa Rica', 'Cote D Ivoire', 'Croatia', 'Cruise Ship', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark',
                'Djibouti', 'Dominica', 'Dominican Republic', 
                'Ecuador', 'Egypt', 'El Salvador', 'Equatorial Guinea', 'Estonia', 'Ethiopia', 
                'Falkland Islands', 'Faroe Islands', 'Fiji', 'Finland', 'France', 'French Polynesia', 'French West Indies',
                'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Guam', 'Guatemala', 'Guernsey', 'Guinea', 'Guinea Bissau', 'Guyana', 'Haiti',
                'Honduras', 'Hong Kong', 'Hungary',
                'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Isle of Man', 'Israel', 'Italy',
                'Jamaica', 'Japan', 'Jersey', 'Jordan',
                'Kazakhstan', 'Kenya', 'Kuwait', 'Kyrgyz Republic',
                'Laos', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg',
                'Macau', 'Macedonia', 'Madagascar', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Mauritania', 'Mauritius', 'Mexico', 'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Montserrat', 'Morocco', 'Mozambique', 'Myanmar',
                'Namibia', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'New Caledonia', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Norway',
                'Oman',
                'Pakistan', 'Palestine', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Puerto Rico',
                'Qatar',
                'Reunion', 'Romania', 'Russia', 'Rwanda',
                'Saint Pierre & Miquelon', 'Samoa', 'San Marino', 'Satellite', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'South Africa', 'South Korea', 'Spain', 'Sri Lanka', 'St Kitts & Nevis', 'St Lucia', 'St Vincent', 'St. Lucia', 'Sudan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syria',
                'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor L\'Este', 'Togo', 'Tonga', 'Trinidad & Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Turks & Caicos',
                'U.S. Outlying Islands', 'U.S. Virgin Islands',
                'Uganda', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan',
                'Vanuatu', 'Vatican City', 'Venezuela', 'Vietnam',
                'Yemen',
                'Zambia', 'Zimbabwe'
            ]
        },
        currencies () {
            return [
                { value: 'USD', label: 'USD - US Dollar' },
                { value: 'CAD', label: 'CAD - Canadian Dollar' },
                { value: 'EUR', label: 'EUR - Euro' },
                { value: 'AED', label: 'AED - United Arab Emirates Dirham' },
                { value: 'AFN', label: 'AFN - Afghan Afghani' },
                { value: 'ALL', label: 'ALL - Albanian Lek' },
                { value: 'AMD', label: 'AMD - Armenian Dram' },
                { value: 'ARS', label: 'ARS - Argentine Peso' },
                { value: 'AUD', label: 'AUD - Australian Dollar' },
                { value: 'AZN', label: 'AZN - Azerbaijani Manat' },
                { value: 'BAM', label: 'BAM - Bosnia-Herzegovina Convertible Mark' },
                { value: 'BDT', label: 'BDT - Bangladeshi Taka' },
                { value: 'BGN', label: 'BGN - Bulgarian Lev' },
                { value: 'BHD', label: 'BHD - Bahraini Dinar' },
                { value: 'BIF', label: 'BIF - Burundian Franc' },
                { value: 'BND', label: 'BND - Brunei Dollar' },
                { value: 'BOB', label: 'BOB - Bolivian Boliviano' },
                { value: 'BRL', label: 'BRL - Brazilian Real' },
                { value: 'BWP', label: 'BWP - Botswanan Pula' },
                { value: 'BYN', label: 'BYN - Belarusian Ruble' },
                { value: 'BZD', label: 'BZD - Belize Dollar' },
                { value: 'CDF', label: 'CDF - Congolese Franc' },
                { value: 'CHF', label: 'CHF - Swiss Franc' },
                { value: 'CLP', label: 'CLP - Chilean Peso' },
                { value: 'CNY', label: 'CNY - Chinese Yuan' },
                { value: 'COP', label: 'COP - Colombian Peso' },
                { value: 'CRC', label: 'CRC - Costa Rican Colón' },
                { value: 'CVE', label: 'CVE - Cape Verdean Escudo' },
                { value: 'CZK', label: 'CZK - Czech Republic Koruna' },
                { value: 'DJF', label: 'DJF - Djiboutian Franc' },
                { value: 'DKK', label: 'DKK - Danish Krone' },
                { value: 'DOP', label: 'DOP - Dominican Peso' },
                { value: 'DZD', label: 'DZD - Algerian Dinar' },
                { value: 'EEK', label: 'EEK - Estonian Kroon' },
                { value: 'EGP', label: 'EGP - Egyptian Pound' },
                { value: 'ERN', label: 'ERN - Eritrean Nakfa' },
                { value: 'ETB', label: 'ETB - Ethiopian Birr' },
                { value: 'GBP', label: 'GBP - British Pound Sterling' },
                { value: 'GEL', label: 'GEL - Georgian Lari' },
                { value: 'GHS', label: 'GHS - Ghanaian Cedi' },
                { value: 'GNF', label: 'GNF - Guinean Franc' },
                { value: 'GTQ', label: 'GTQ - Guatemalan Quetzal' },
                { value: 'HKD', label: 'HKD - Hong Kong Dollar' },
                { value: 'HNL', label: 'HNL - Honduran Lempira' },
                { value: 'HRK', label: 'HRK - Croatian Kuna' },
                { value: 'HUF', label: 'HUF - Hungarian Forint' },
                { value: 'IDR', label: 'IDR - Indonesian Rupiah' },
                { value: 'ILS', label: 'ILS - Israeli New Sheqel' },
                { value: 'INR', label: 'INR - Indian Rupee' },
                { value: 'IQD', label: 'IQD - Iraqi Dinar' },
                { value: 'IRR', label: 'IRR - Iranian Rial' },
                { value: 'ISK', label: 'ISK - Icelandic Króna' },
                { value: 'JMD', label: 'JMD - Jamaican Dollar' },
                { value: 'JOD', label: 'JOD - Jordanian Dinar' },
                { value: 'JPY', label: 'JPY - Japanese Yen' },
                { value: 'KES', label: 'KES - Kenyan Shilling' },
                { value: 'KHR', label: 'KHR - Cambodian Riel' },
                { value: 'KMF', label: 'KMF - Comorian Franc' },
                { value: 'KRW', label: 'KRW - South Korean Won' },
                { value: 'KWD', label: 'KWD - Kuwaiti Dinar' },
                { value: 'KZT', label: 'KZT - Kazakhstani Tenge' },
                { value: 'LBP', label: 'LBP - Lebanese Pound' },
                { value: 'LKR', label: 'LKR - Sri Lankan Rupee' },
                { value: 'LTL', label: 'LTL - Lithuanian Litas' },
                { value: 'LVL', label: 'LVL - Latvian Lats' },
                { value: 'LYD', label: 'LYD - Libyan Dinar' },
                { value: 'MAD', label: 'MAD - Moroccan Dirham' },
                { value: 'MDL', label: 'MDL - Moldovan Leu' },
                { value: 'MGA', label: 'MGA - Malagasy Ariary' },
                { value: 'MKD', label: 'MKD - Macedonian Denar' },
                { value: 'MMK', label: 'MMK - Myanma Kyat' },
                { value: 'MOP', label: 'MOP - Macanese Pataca' },
                { value: 'MUR', label: 'MUR - Mauritian Rupee' },
                { value: 'MXN', label: 'MXN - Mexican Peso' },
                { value: 'MYR', label: 'MYR - Malaysian Ringgit' },
                { value: 'MZN', label: 'MZN - Mozambican Metical' },
                { value: 'NAD', label: 'NAD - Namibian Dollar' },
                { value: 'NGN', label: 'NGN - Nigerian Naira' },
                { value: 'NIO', label: 'NIO - Nicaraguan Córdoba' },
                { value: 'NOK', label: 'NOK - Norwegian Krone' },
                { value: 'NPR', label: 'NPR - Nepalese Rupee' },
                { value: 'NZD', label: 'NZD - New Zealand Dollar' },
                { value: 'OMR', label: 'OMR - Omani Rial' },
                { value: 'PAB', label: 'PAB - Panamanian Balboa' },
                { value: 'PEN', label: 'PEN - Peruvian Nuevo Sol' },
                { value: 'PHP', label: 'PHP - Philippine Peso' },
                { value: 'PKR', label: 'PKR - Pakistani Rupee' },
                { value: 'PLN', label: 'PLN - Polish Zloty' },
                { value: 'PYG', label: 'PYG - Paraguayan Guarani' },
                { value: 'QAR', label: 'QAR - Qatari Rial' },
                { value: 'RON', label: 'RON - Romanian Leu' },
                { value: 'RSD', label: 'RSD - Serbian Dinar' },
                { value: 'RUB', label: 'RUB - Russian Ruble' },
                { value: 'RWF', label: 'RWF - Rwandan Franc' },
                { value: 'SAR', label: 'SAR - Saudi Riyal' },
                { value: 'SDG', label: 'SDG - Sudanese Pound' },
                { value: 'SEK', label: 'SEK - Swedish Krona' },
                { value: 'SGD', label: 'SGD - Singapore Dollar' },
                { value: 'SOS', label: 'SOS - Somali Shilling' },
                { value: 'SYP', label: 'SYP - Syrian Pound' },
                { value: 'THB', label: 'THB - Thai Baht' },
                { value: 'TND', label: 'TND - Tunisian Dinar' },
                { value: 'TOP', label: 'TOP - Tongan Paʻanga' },
                { value: 'TRY', label: 'TRY - Turkish Lira' },
                { value: 'TTD', label: 'TTD - Trinidad and Tobago Dollar' },
                { value: 'TWD', label: 'TWD - New Taiwan Dollar' },
                { value: 'TZS', label: 'TZS - Tanzanian Shilling' },
                { value: 'UAH', label: 'UAH - Ukrainian Hryvnia' },
                { value: 'UGX', label: 'UGX - Ugandan Shilling' },
                { value: 'UYU', label: 'UYU - Uruguayan Peso' },
                { value: 'UZS', label: 'UZS - Uzbekistan Som' },
                { value: 'VEF', label: 'VEF - Venezuelan Bolívar' },
                { value: 'VND', label: 'VND - Vietnamese Dong' },
                { value: 'XAF', label: 'XAF - CFA Franc BEAC' },
                { value: 'XOF', label: 'XOF - CFA Franc BCEAO' },
                { value: 'YER', label: 'YER - Yemeni Rial' },
                { value: 'ZAR', label: 'ZAR - South African Rand' },
                { value: 'ZMK', label: 'ZMK - Zambian Kwacha' },
                { value: 'ZWL', label: 'ZWL - Zimbabwean Dollar' },
            ]
        },
    },
    methods: {
        getSelectOptions () {
            let options

            if (this.options === 'country') options = this.countries
            else if (this.options === 'state') options = this.states
            else if (this.options === 'currency') options = this.currencies
            else options = this.options

            return options.map(opt => {
                if (opt.hasOwnProperty('value') && opt.hasOwnProperty('label')) return opt
                else if (opt.hasOwnProperty('value')) return { value: opt.value, label: opt.value}
                else return { value: opt, label: opt }
            })
        },
    }
}
</script>

<style>
.box .field:last-child {
    margin-bottom: 0;
}
</style>
