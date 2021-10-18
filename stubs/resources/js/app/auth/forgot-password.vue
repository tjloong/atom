<template>
    <div class="min-h-screen relative px-4 py-10 md:py-20">
        <div class="absolute inset-0">
            <img src="/storage/img/blob-bg.svg" class="w-full h-full object-cover object-center opacity-50">
        </div>

        <div class="relative max-w-md mx-auto">
            <a href="/" class="mb-4">
                <img src="/storage/img/logo.svg" class="w-20 mb-6">
            </a>

            <form @submit.prevent="submit()">
                <box>
                    <div class="p-5 md:p-10">
                        <div class="text-2xl font-bold mb-6">
                            Reset Password Request
                        </div>

                        <div v-if="errors.email" class="mb-4 text-sm bg-red-100 text-red-800 rounded p-4">
                            {{ errors.email }}
                        </div>

                        <div class="mb-5 w-full">
                            <div class="font-medium text-gray-500 text-xs uppercase mb-2">Your registered email</div>
                            <input type="email" class="w-full form-input" v-model="form.email" required autofocus>
                        </div>

                        <btn submit size="md" class="w-full" :loading="form.processing">
                            Send Request
                        </btn>
                    </div>
                </box>

                <div class="text-sm mt-4">
                    <inertia-link :href="route('login')" class="inline-flex items-center">
                        <box-icon name="left-arrow-alt"></box-icon>
                        Back to login
                    </inertia-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ForgotPassword',
    layout: null,
    props: {
        errors: Object,
    },
    metaInfo: { title: 'Reset Password Request' },
    data () {
        return {
            form: this.$inertia.form({
                email: null,
            }),
        }
    },
    methods: {
        submit () {
            this.form.post(this.route('password.forgot'))
        },
    }
}
</script>
