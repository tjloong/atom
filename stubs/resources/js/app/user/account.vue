<template>
    <div class="max-w-md mx-auto">
        <page-header title="My Account" />

        <form @submit.prevent="submit()">
            <box>
                <div class="p-5">
                    <field v-model="form.name" required>
                        Name
                    </field>

                    <field v-model="form.email" required email>
                        Login Email
                    </field>

                    <field v-model="form.password" password>
                        Login Password
                    </field>
                </div>

                <template #buttons>
                    <btn submit color="green" :loading="form.processing">
                        Update
                    </btn>
                </template>
            </box>
        </form>
    </div>
</template>

<script>
export default {
    name: 'UserAccount',
    metaInfo: { title: 'My Account' },
    data () {
        return {
            form: this.$inertia.form({
                name: this.$auth('user.name'),
                email: this.$auth('user.email'),
                password: null,
            }),
        }
    },
    methods: {
        submit () {
            this.form
                .transform(data => {
                    if (!data.password) delete data.password
                    return data
                })
                .post(this.route('user.account'))
        }
    }
}
</script>
