<template>
    <div class="phone-input">
        <div class="relative w-full">
            <a 
                :class="[
                    'absolute left-0 top-0 bottom-0 px-3 leading-normal flex items-center justify-center',
                    loading && 'pointer-events-none',
                ]" 
                @click="$refs.modal.open()"
            >
                <spinner class="mx-2" size="32" color="#bbb" v-if="loading" />
                <template v-else>
                    {{ phone.code }}
                </template>
            </a>

            <input type="text" 
                :class="['form-input w-full pl-14', error && 'form-input-error']" 
                v-model="phone.number"
                :required="required"
                :disabled="loading"
                @input="update()"
            >
        </div>

        <modal ref="modal" title="Select Country Code" size="sm">
            <div class="-mx-4">
                <a
                    v-for="val in countries"
                    :key="val.name"
                    class="flex items-center py-2 px-4 border-t hover:bg-gray-100"
                    @click="
                        phone.code = val.code
                        update()
                        $refs.modal.close()
                    "
                >
                    <img :src="`https://www.countryflags.io/${val.flag}`" class="mr-2 w-6 flex-shrink-0">

                    <div class="text-gray-500 text-sm w-16 flex-shrink-0">
                        {{ val.code }}
                    </div>

                    <div class="font-medium">
                        {{ val.name }}
                    </div>
                </a>
            </div>
        </modal>
    </div>
</template>

<script>
export default {
    name: 'PhoneInput',
    props: {
        value: String,
        required: Boolean,
        error: Boolean,
    },
    data () {
        return {
            loading: false,
            countries: [],
            phone: {
                code: '+60',
                number: null,
            },
        }
    },
    watch: {
        value (val) {
            this.init()
        },
    },
    created () {
        this.fetch()
        this.init()
    },
    methods: {
        init () {
            if (this.value?.startsWith('+')) {
                const country = this.countries.find(val => (this.value.startsWith(val.code)))

                if (country) {
                    this.phone.code = country.code
                    this.phone.number = this.value.replace(country.code, '')
                }
            }
            else this.phone.number = this.value
        },
        update () {
            if (this.phone.number) this.$emit('input', `${this.phone.code}${this.phone.number}`)
            else this.$emit('input', null)
        },
        fetch () {
            this.countries = [
                { "name": "Afghanistan", "code": "+93", "flag": "AF/flat/64.png" },
                { "name": "Aland Islands", "code": "+358", "flag": "AX/flat/64.png" },
                { "name": "Albania", "code": "+355", "flag": "AL/flat/64.png" },
                { "name": "Algeria", "code": "+213", "flag": "DZ/flat/64.png" },
                { "name": "AmericanSamoa", "code": "+1684", "flag": "AS/flat/64.png" },
                { "name": "Andorra", "code": "+376", "flag": "AD/flat/64.png" },
                { "name": "Angola", "code": "+244", "flag": "AO/flat/64.png" },
                { "name": "Anguilla", "code": "+1264", "flag": "AI/flat/64.png" },
                { "name": "Antarctica", "code": "+672", "flag": "AQ/flat/64.png" },
                { "name": "Antigua and Barbuda", "code": "+1268", "flag": "AG/flat/64.png" },
                { "name": "Argentina", "code": "+54", "flag": "AR/flat/64.png" },
                { "name": "Armenia", "code": "+374", "flag": "AM/flat/64.png" },
                { "name": "Aruba", "code": "+297", "flag": "AW/flat/64.png" },
                { "name": "Ascension Island", "code": "+247", "flag": "AC/flat/64.png" },
                { "name": "Australia", "code": "+61", "flag": "AU/flat/64.png" },
                { "name": "Austria", "code": "+43", "flag": "AT/flat/64.png" },
                { "name": "Azerbaijan", "code": "+994", "flag": "AZ/flat/64.png" },
                { "name": "Bahamas", "code": "+1242", "flag": "BS/flat/64.png" },
                { "name": "Bahrain", "code": "+973", "flag": "BH/flat/64.png" },
                { "name": "Bangladesh", "code": "+880", "flag": "BD/flat/64.png" },
                { "name": "Barbados", "code": "+1246", "flag": "BB/flat/64.png" },
                { "name": "Belarus", "code": "+375", "flag": "BY/flat/64.png" },
                { "name": "Belgium", "code": "+32", "flag": "BE/flat/64.png" },
                { "name": "Belize", "code": "+501", "flag": "BZ/flat/64.png" },
                { "name": "Benin", "code": "+229", "flag": "BJ/flat/64.png" },
                { "name": "Bermuda", "code": "+1441", "flag": "BM/flat/64.png" },
                { "name": "Bhutan", "code": "+975", "flag": "BT/flat/64.png" },
                { "name": "Bolivia", "code": "+591", "flag": "BO/flat/64.png" },
                { "name": "Bosnia and Herzegovina", "code": "+387", "flag": "BA/flat/64.png" },
                { "name": "Botswana", "code": "+267", "flag": "BW/flat/64.png" },
                { "name": "Brazil", "code": "+55", "flag": "BR/flat/64.png" },
                { "name": "British Indian Ocean Territory", "code": "+246", "flag": "IO/flat/64.png" },
                { "name": "Brunei Darussalam", "code": "+673", "flag": "BN/flat/64.png" },
                { "name": "Bulgaria", "code": "+359", "flag": "BG/flat/64.png" },
                { "name": "Burkina Faso", "code": "+226", "flag": "BF/flat/64.png" },
                { "name": "Burundi", "code": "+257", "flag": "BI/flat/64.png" },
                { "name": "Cambodia", "code": "+855", "flag": "KH/flat/64.png" },
                { "name": "Cameroon", "code": "+237", "flag": "CM/flat/64.png" },
                { "name": "Canada", "code": "+1", "flag": "CA/flat/64.png" },
                { "name": "Cape Verde", "code": "+238", "flag": "CV/flat/64.png" },
                { "name": "Cayman Islands", "code": "+1345", "flag": "KY/flat/64.png" },
                { "name": "Central African Republic", "code": "+236", "flag": "CF/flat/64.png" },
                { "name": "Chad", "code": "+235", "flag": "TD/flat/64.png" },
                { "name": "Chile", "code": "+56", "flag": "CL/flat/64.png" },
                { "name": "China", "code": "+86", "flag": "CN/flat/64.png" },
                { "name": "Christmas Island", "code": "+61", "flag": "CX/flat/64.png" },
                { "name": "Cocos (Keeling) Islands", "code": "+61", "flag": "CC/flat/64.png" },
                { "name": "Colombia", "code": "+57", "flag": "CO/flat/64.png" },
                { "name": "Comoros", "code": "+269", "flag": "KM/flat/64.png" },
                { "name": "Congo", "code": "+242", "flag": "CG/flat/64.png" },
                { "name": "Cook Islands", "code": "+682", "flag": "CK/flat/64.png" },
                { "name": "Costa Rica", "code": "+506", "flag": "CR/flat/64.png" },
                { "name": "Croatia", "code": "+385", "flag": "HR/flat/64.png" },
                { "name": "Cuba", "code": "+53", "flag": "CU/flat/64.png" },
                { "name": "Cyprus", "code": "+357", "flag": "CY/flat/64.png" },
                { "name": "Czech Republic", "code": "+420", "flag": "CZ/flat/64.png" },
                { "name": "Democratic Republic of the Congo", "code": "+243", "flag": "CD/flat/64.png" },
                { "name": "Denmark", "code": "+45", "flag": "DK/flat/64.png" },
                { "name": "Djibouti", "code": "+253", "flag": "DJ/flat/64.png" },
                { "name": "Dominica", "code": "+1767", "flag": "DM/flat/64.png" },
                { "name": "Dominican Republic", "code": "+1849", "flag": "DO/flat/64.png" },
                { "name": "Ecuador", "code": "+593", "flag": "EC/flat/64.png" },
                { "name": "Egypt", "code": "+20", "flag": "EG/flat/64.png" },
                { "name": "El Salvador", "code": "+503", "flag": "SV/flat/64.png" },
                { "name": "Equatorial Guinea", "code": "+240", "flag": "GQ/flat/64.png" },
                { "name": "Eritrea", "code": "+291", "flag": "ER/flat/64.png" },
                { "name": "Estonia", "code": "+372", "flag": "EE/flat/64.png" },
                { "name": "Eswatini", "code": "+268", "flag": "SZ/flat/64.png" },
                { "name": "Ethiopia", "code": "+251", "flag": "ET/flat/64.png" },
                { "name": "Falkland Islands (Malvinas)", "code": "+500", "flag": "FK/flat/64.png" },
                { "name": "Faroe Islands", "code": "+298", "flag": "FO/flat/64.png" },
                { "name": "Fiji", "code": "+679", "flag": "FJ/flat/64.png" },
                { "name": "Finland", "code": "+358", "flag": "FI/flat/64.png" },
                { "name": "France", "code": "+33", "flag": "FR/flat/64.png" },
                { "name": "French Guiana", "code": "+594", "flag": "GF/flat/64.png" },
                { "name": "French Polynesia", "code": "+689", "flag": "PF/flat/64.png" },
                { "name": "Gabon", "code": "+241", "flag": "GA/flat/64.png" },
                { "name": "Gambia", "code": "+220", "flag": "GM/flat/64.png" },
                { "name": "Georgia", "code": "+995", "flag": "GE/flat/64.png" },
                { "name": "Germany", "code": "+49", "flag": "DE/flat/64.png" },
                { "name": "Ghana", "code": "+233", "flag": "GH/flat/64.png" },
                { "name": "Gibraltar", "code": "+350", "flag": "GI/flat/64.png" },
                { "name": "Greece", "code": "+30", "flag": "GR/flat/64.png" },
                { "name": "Greenland", "code": "+299", "flag": "GL/flat/64.png" },
                { "name": "Grenada", "code": "+1473", "flag": "GD/flat/64.png" },
                { "name": "Guadeloupe", "code": "+590", "flag": "GP/flat/64.png" },
                { "name": "Guam", "code": "+1671", "flag": "GU/flat/64.png" },
                { "name": "Guatemala", "code": "+502", "flag": "GT/flat/64.png" },
                { "name": "Guernsey", "code": "+44", "flag": "GG/flat/64.png" },
                { "name": "Guinea", "code": "+224", "flag": "GN/flat/64.png" },
                { "name": "Guinea-Bissau", "code": "+245", "flag": "GW/flat/64.png" },
                { "name": "Guyana", "code": "+592", "flag": "GY/flat/64.png" },
                { "name": "Haiti", "code": "+509", "flag": "HT/flat/64.png" },
                { "name": "Holy See (Vatican City State)", "code": "+379", "flag": "VA/flat/64.png" },
                { "name": "Honduras", "code": "+504", "flag": "HN/flat/64.png" },
                { "name": "Hong Kong", "code": "+852", "flag": "HK/flat/64.png" },
                { "name": "Hungary", "code": "+36", "flag": "HU/flat/64.png" },
                { "name": "Iceland", "code": "+354", "flag": "IS/flat/64.png" },
                { "name": "India", "code": "+91", "flag": "IN/flat/64.png" },
                { "name": "Indonesia", "code": "+62", "flag": "ID/flat/64.png" },
                { "name": "Iran", "code": "+98", "flag": "IR/flat/64.png" },
                { "name": "Iraq", "code": "+964", "flag": "IQ/flat/64.png" },
                { "name": "Ireland", "code": "+353", "flag": "IE/flat/64.png" },
                { "name": "Isle of Man", "code": "+44", "flag": "IM/flat/64.png" },
                { "name": "Israel", "code": "+972", "flag": "IL/flat/64.png" },
                { "name": "Italy", "code": "+39", "flag": "IT/flat/64.png" },
                { "name": "Ivory Coast / Cote d'Ivoire", "code": "+225", "flag": "CI/flat/64.png" },
                { "name": "Jamaica", "code": "+1876", "flag": "JM/flat/64.png" },
                { "name": "Japan", "code": "+81", "flag": "JP/flat/64.png" },
                { "name": "Jersey", "code": "+44", "flag": "JE/flat/64.png" },
                { "name": "Jordan", "code": "+962", "flag": "JO/flat/64.png" },
                { "name": "Kazakhstan", "code": "+77", "flag": "KZ/flat/64.png" },
                { "name": "Kenya", "code": "+254", "flag": "KE/flat/64.png" },
                { "name": "Kiribati", "code": "+686", "flag": "KI/flat/64.png" },
                { "name": "Korea, Democratic People's Republic of Korea", "code": "+850", "flag": "KP/flat/64.png" },
                { "name": "Korea, Republic of South Korea", "code": "+82", "flag": "KR/flat/64.png" },
                { "name": "Kosovo", "code": "+383", "flag": "XK/flat/64.png" },
                { "name": "Kuwait", "code": "+965", "flag": "KW/flat/64.png" },
                { "name": "Kyrgyzstan", "code": "+996", "flag": "KG/flat/64.png" },
                { "name": "Laos", "code": "+856", "flag": "LA/flat/64.png" },
                { "name": "Latvia", "code": "+371", "flag": "LV/flat/64.png" },
                { "name": "Lebanon", "code": "+961", "flag": "LB/flat/64.png" },
                { "name": "Lesotho", "code": "+266", "flag": "LS/flat/64.png" },
                { "name": "Liberia", "code": "+231", "flag": "LR/flat/64.png" },
                { "name": "Libya", "code": "+218", "flag": "LY/flat/64.png" },
                { "name": "Liechtenstein", "code": "+423", "flag": "LI/flat/64.png" },
                { "name": "Lithuania", "code": "+370", "flag": "LT/flat/64.png" },
                { "name": "Luxembourg", "code": "+352", "flag": "LU/flat/64.png" },
                { "name": "Macau", "code": "+853", "flag": "MO/flat/64.png" },
                { "name": "Madagascar", "code": "+261", "flag": "MG/flat/64.png" },
                { "name": "Malawi", "code": "+265", "flag": "MW/flat/64.png" },
                { "name": "Malaysia", "code": "+60", "flag": "MY/flat/64.png" },
                { "name": "Maldives", "code": "+960", "flag": "MV/flat/64.png" },
                { "name": "Mali", "code": "+223", "flag": "ML/flat/64.png" },
                { "name": "Malta", "code": "+356", "flag": "MT/flat/64.png" },
                { "name": "Marshall Islands", "code": "+692", "flag": "MH/flat/64.png" },
                { "name": "Martinique", "code": "+596", "flag": "MQ/flat/64.png" },
                { "name": "Mauritania", "code": "+222", "flag": "MR/flat/64.png" },
                { "name": "Mauritius", "code": "+230", "flag": "MU/flat/64.png" },
                { "name": "Mayotte", "code": "+262", "flag": "YT/flat/64.png" },
                { "name": "Mexico", "code": "+52", "flag": "MX/flat/64.png" },
                { "name": "Micronesia, Federated States of Micronesia", "code": "+691", "flag": "FM/flat/64.png" },
                { "name": "Moldova", "code": "+373", "flag": "MD/flat/64.png" },
                { "name": "Monaco", "code": "+377", "flag": "MC/flat/64.png" },
                { "name": "Mongolia", "code": "+976", "flag": "MN/flat/64.png" },
                { "name": "Montenegro", "code": "+382", "flag": "ME/flat/64.png" },
                { "name": "Montserrat", "code": "+1664", "flag": "MS/flat/64.png" },
                { "name": "Morocco", "code": "+212", "flag": "MA/flat/64.png" },
                { "name": "Mozambique", "code": "+258", "flag": "MZ/flat/64.png" },
                { "name": "Myanmar", "code": "+95", "flag": "MM/flat/64.png" },
                { "name": "Namibia", "code": "+264", "flag": "NA/flat/64.png" },
                { "name": "Nauru", "code": "+674", "flag": "NR/flat/64.png" },
                { "name": "Nepal", "code": "+977", "flag": "NP/flat/64.png" },
                { "name": "Netherlands", "code": "+31", "flag": "NL/flat/64.png" },
                { "name": "Netherlands Antilles", "code": "+599", "flag": "AN/flat/64.png" },
                { "name": "New Caledonia", "code": "+687", "flag": "NC/flat/64.png" },
                { "name": "New Zealand", "code": "+64", "flag": "NZ/flat/64.png" },
                { "name": "Nicaragua", "code": "+505", "flag": "NI/flat/64.png" },
                { "name": "Niger", "code": "+227", "flag": "NE/flat/64.png" },
                { "name": "Nigeria", "code": "+234", "flag": "NG/flat/64.png" },
                { "name": "Niue", "code": "+683", "flag": "NU/flat/64.png" },
                { "name": "Norfolk Island", "code": "+672", "flag": "NF/flat/64.png" },
                { "name": "North Macedonia", "code": "+389", "flag": "MK/flat/64.png" },
                { "name": "Northern Mariana Islands", "code": "+1670", "flag": "MP/flat/64.png" },
                { "name": "Norway", "code": "+47", "flag": "NO/flat/64.png" },
                { "name": "Oman", "code": "+968", "flag": "OM/flat/64.png" },
                { "name": "Pakistan", "code": "+92", "flag": "PK/flat/64.png" },
                { "name": "Palau", "code": "+680", "flag": "PW/flat/64.png" },
                { "name": "Palestine", "code": "+970", "flag": "PS/flat/64.png" },
                { "name": "Panama", "code": "+507", "flag": "PA/flat/64.png" },
                { "name": "Papua New Guinea", "code": "+675", "flag": "PG/flat/64.png" },
                { "name": "Paraguay", "code": "+595", "flag": "PY/flat/64.png" },
                { "name": "Peru", "code": "+51", "flag": "PE/flat/64.png" },
                { "name": "Philippines", "code": "+63", "flag": "PH/flat/64.png" },
                { "name": "Pitcairn", "code": "+872", "flag": "PN/flat/64.png" },
                { "name": "Poland", "code": "+48", "flag": "PL/flat/64.png" },
                { "name": "Portugal", "code": "+351", "flag": "PT/flat/64.png" },
                { "name": "Puerto Rico", "code": "+1939", "flag": "PR/flat/64.png" },
                { "name": "Qatar", "code": "+974", "flag": "QA/flat/64.png" },
                { "name": "Reunion", "code": "+262", "flag": "RE/flat/64.png" },
                { "name": "Romania", "code": "+40", "flag": "RO/flat/64.png" },
                { "name": "Russia", "code": "+7", "flag": "RU/flat/64.png" },
                { "name": "Rwanda", "code": "+250", "flag": "RW/flat/64.png" },
                { "name": "Saint Barthelemy", "code": "+590", "flag": "BL/flat/64.png" },
                { "name": "Saint Helena, Ascension and Tristan Da Cunha", "code": "+290", "flag": "SH/flat/64.png" },
                { "name": "Saint Kitts and Nevis", "code": "+1869", "flag": "KN/flat/64.png" },
                { "name": "Saint Lucia", "code": "+1758", "flag": "LC/flat/64.png" },
                { "name": "Saint Martin", "code": "+590", "flag": "MF/flat/64.png" },
                { "name": "Saint Pierre and Miquelon", "code": "+508", "flag": "PM/flat/64.png" },
                { "name": "Saint Vincent and the Grenadines", "code": "+1784", "flag": "VC/flat/64.png" },
                { "name": "Samoa", "code": "+685", "flag": "WS/flat/64.png" },
                { "name": "San Marino", "code": "+378", "flag": "SM/flat/64.png" },
                { "name": "Sao Tome and Principe", "code": "+239", "flag": "ST/flat/64.png" },
                { "name": "Saudi Arabia", "code": "+966", "flag": "SA/flat/64.png" },
                { "name": "Senegal", "code": "+221", "flag": "SN/flat/64.png" },
                { "name": "Serbia", "code": "+381", "flag": "RS/flat/64.png" },
                { "name": "Seychelles", "code": "+248", "flag": "SC/flat/64.png" },
                { "name": "Sierra Leone", "code": "+232", "flag": "SL/flat/64.png" },
                { "name": "Singapore", "code": "+65", "flag": "SG/flat/64.png" },
                { "name": "Sint Maarten", "code": "+1721", "flag": "SX/flat/64.png" },
                { "name": "Slovakia", "code": "+421", "flag": "SK/flat/64.png" },
                { "name": "Slovenia", "code": "+386", "flag": "SI/flat/64.png" },
                { "name": "Solomon Islands", "code": "+677", "flag": "SB/flat/64.png" },
                { "name": "Somalia", "code": "+252", "flag": "SO/flat/64.png" },
                { "name": "South Africa", "code": "+27", "flag": "ZA/flat/64.png" },
                { "name": "South Georgia and the South Sandwich Islands", "code": "+500", "flag": "GS/flat/64.png" },
                { "name": "South Sudan", "code": "+211", "flag": "SS/flat/64.png" },
                { "name": "Spain", "code": "+34", "flag": "ES/flat/64.png" },
                { "name": "Sri Lanka", "code": "+94", "flag": "LK/flat/64.png" },
                { "name": "Sudan", "code": "+249", "flag": "SD/flat/64.png" },
                { "name": "Suriname", "code": "+597", "flag": "SR/flat/64.png" },
                { "name": "Svalbard and Jan Mayen", "code": "+47", "flag": "SJ/flat/64.png" },
                { "name": "Sweden", "code": "+46", "flag": "SE/flat/64.png" },
                { "name": "Switzerland", "code": "+41", "flag": "CH/flat/64.png" },
                { "name": "Syrian Arab Republic", "code": "+963", "flag": "SY/flat/64.png" },
                { "name": "Taiwan", "code": "+886", "flag": "TW/flat/64.png" },
                { "name": "Tajikistan", "code": "+992", "flag": "TJ/flat/64.png" },
                { "name": "Tanzania, United Republic of Tanzania", "code": "+255", "flag": "TZ/flat/64.png" },
                { "name": "Thailand", "code": "+66", "flag": "TH/flat/64.png" },
                { "name": "Timor-Leste", "code": "+670", "flag": "TL/flat/64.png" },
                { "name": "Togo", "code": "+228", "flag": "TG/flat/64.png" },
                { "name": "Tokelau", "code": "+690", "flag": "TK/flat/64.png" },
                { "name": "Tonga", "code": "+676", "flag": "TO/flat/64.png" },
                { "name": "Trinidad and Tobago", "code": "+1868", "flag": "TT/flat/64.png" },
                { "name": "Tunisia", "code": "+216", "flag": "TN/flat/64.png" },
                { "name": "Turkey", "code": "+90", "flag": "TR/flat/64.png" },
                { "name": "Turkmenistan", "code": "+993", "flag": "TM/flat/64.png" },
                { "name": "Turks and Caicos Islands", "code": "+1649", "flag": "TC/flat/64.png" },
                { "name": "Tuvalu", "code": "+688", "flag": "TV/flat/64.png" },
                { "name": "Uganda", "code": "+256", "flag": "UG/flat/64.png" },
                { "name": "Ukraine", "code": "+380", "flag": "UA/flat/64.png" },
                { "name": "United Arab Emirates", "code": "+971", "flag": "AE/flat/64.png" },
                { "name": "United Kingdom", "code": "+44", "flag": "GB/flat/64.png" },
                { "name": "United States", "code": "+1", "flag": "US/flat/64.png" },
                { "name": "Uruguay", "code": "+598", "flag": "UY/flat/64.png" },
                { "name": "Uzbekistan", "code": "+998", "flag": "UZ/flat/64.png" },
                { "name": "Vanuatu", "code": "+678", "flag": "VU/flat/64.png" },
                { "name": "Venezuela, Bolivarian Republic of Venezuela", "code": "+58", "flag": "VE/flat/64.png" },
                { "name": "Vietnam", "code": "+84", "flag": "VN/flat/64.png" },
                { "name": "Virgin Islands, British", "code": "+1284", "flag": "VG/flat/64.png" },
                { "name": "Virgin Islands, U.S.", "code": "+1340", "flag": "VI/flat/64.png" },
                { "name": "Wallis and Futuna", "code": "+681", "flag": "WF/flat/64.png" },
                { "name": "Yemen", "code": "+967", "flag": "YE/flat/64.png" },
                { "name": "Zambia", "code": "+260", "flag": "ZM/flat/64.png" },
                { "name": "Zimbabwe", "code": "+263", "flag": "ZW/flat/64.png" },
            ]
        }
    }
}
</script>
