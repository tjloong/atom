<template>
    <div class="address-input">
        <slot>
            <div v-if="value" class="text-sm mb-1.5">
                {{ getFormatted(value) }}
            </div>

            <a class="text-blue-500 text-sm font-medium inline-flex items-center" @click="open()">
                Edit address
            </a>
        </slot>
        

        <modal ref="modal" size="sm" title="Edit Address">
            <form v-if="address" @submit.prevent="submit()" novalidate>
                <field v-model="address.addr" :error="errors.addr" required>
                    Address
                </field>

                <field 
                    v-model="address.postcode"
                    :error="errors.postcode"
                    :required="strict || false"
                    :props="{ maxlength: validatePostcode ? 6 : null }"
                >
                    Postcode
                </field>

                <field v-model="address.city" :error="errors.city" :required="strict || false">
                    City
                </field>

                <field
                    v-if="!enableCountry || (address.country === 'Malaysia')"
                    v-model="address.state"
                    options="state"
                    :error="errors.state"
                    :required="strict || false"
                >
                    State
                </field>

                <field v-else v-model="address.state" :error="errors.state" :required="strict || false">
                    State
                </field>

                <field
                    v-if="enableCountry"
                    v-model="address.country"
                    options="country"
                    :error="errors.country"
                    :required="strict || false"
                >
                    Country
                </field>

                <btn submit color="green" :loading="loading">
                    Done
                </btn>
            </form>
        </modal>
    </div>
</template>

<script>
export default {
    name: 'AddressInput',
    props: {
        value: Object,
        strict: Boolean,
        enableCountry: Boolean,
        validatePostcode: Boolean,
    },
    data () {
        return {
            errors: {},
            address: null,
            loading: false,
            postcodes: [],
        }
    },
    methods: {
        open () {
            this.address = {
                addr: null,
                postcode: null,
                city: null,
                state: null,
                country: null,
                ...this.value,
            }

            this.$refs.modal.open()
        },
        close () {
            this.address = null
            this.$refs.modal.close()
        },
        getFormatted (address) {
            if (!address) return null

            const city = [address.postcode, address.city].filter(Boolean).join(' ')
            const formatted = [address.addr, city, address.state, address.country].filter(Boolean).join(', ')

            return formatted
        },
        submit () {
            this.validate()
                .then(() => {
                    this.$emit('input', { ...this.address, formatted: this.getFormatted(this.address) })
                    this.close()
                })
                .catch(() => {})
        },
        validate () {
            this.loading = true

            return new Promise((resolve, reject) => {
                if (this.validatePostcode) {
                    axios.get('/storage/postcodes.json').then(res => {
                        this.postcodes = res.data

                        if (this.validator()) resolve()
                        else reject()

                        this.loading = false
                    })
                }
                else {
                    if (this.validator()) resolve()
                    else reject()

                    this.loading = false
                }
            })
        },
        validator () {
            const errors = {}

            if (!this.address.addr) errors.addr = 'Address is required.'
            if (this.strict && !this.address.city) errors.city = 'City is required.'
            if (this.strict && !this.address.state) errors.state = 'State is required.'
            if (this.strict && this.enableCountry && !this.address.country) errors.country = 'Country is required.'

            if (this.strict && !this.address.postcode) errors.postcode = 'Postcode is required.'
            else if (this.validatePostcode && (!this.enableCountry || this.address.country === 'Malaysia')) {
                if (!/^\d{5,6}$/.test(this.address.postcode)) errors.postcode = 'Invalid postcode.'
                else {
                    const postcode = this.postcodes.find(val => (
                        val.postcode === this.address.postcode
                        && val.state === this.address.state
                    ))

                    if (!postcode) errors.postcode = 'Postcode does not belongs to the state.'
                }
            }

            this.errors = errors

            return _.isEmpty(this.errors)
        },
    }
}
</script>
