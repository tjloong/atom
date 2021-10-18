<template>
    <div class="min-h-screen relative px-4 py-4 md:py-20">
        <div class="absolute inset-0">
            <img src="/storage/img/blob-bg.svg" class="w-full h-full object-cover object-center opacity-50">
        </div>

        <div class="max-w-md mx-auto relative">
            <a class="block w-20 mb-4" href="/">
                <img src="/storage/img/logo.svg" class="w-full">
            </a>

            <form @submit.prevent="submit()">
                <box>
                    <div class="p-5 md:p-10">
                        <div class="text-2xl font-bold mb-6">
                            Sign in to your account
                        </div>

                        <div v-if="flash" class="mb-4 text-sm bg-blue-100 text-blue-800 rounded p-4">
                            {{ flash.message }}
                        </div>

                        <div v-if="errors.email || errors.password" class="mb-4 text-sm bg-red-100 text-red-800 rounded p-4">
                            {{ errors.email || errors.password }}
                        </div>

                        <div class="mb-5 w-full">
                            <div class="font-medium text-gray-500 text-xs uppercase mb-2">Email</div>
                            <input type="text" v-model="form.email" class="w-full form-input" tabindex="1" required autofocus>
                        </div>

                        <div class="mb-8 w-full">
                            <div class="flex justify-between mb-2">
                                <div class="font-medium text-gray-500 text-xs uppercase">
                                    Password
                                </div>

                                <inertia-link :href="route('password.forgot')" class="text-blue-500 text-xs">
                                    Forgot Password?
                                </inertia-link>
                            </div>
                            <input type="password" v-model="form.password" class="w-full form-input" tabindex="2" required>
                        </div>

                        <btn submit :loading="form.processing" size="md" class="w-full">
                            Sign In
                        </btn>
                    </div>
                </box>

                <div class="mt-6 text-sm">
                    Don't have an account? 

                    <inertia-link :href="route('register', { ref: 'page-login' })" class="text-blue-500 font-medium">
                        Sign Up
                    </inertia-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Login',
    layout: null,
    props: {
        flash: [Object, String],
        errors: Object,
    },
    metaInfo: { title: 'Login' },
    data () {
        return {
            form: this.$inertia.form({
                email: null,
                password: null,
                remember: false,
            }),
        }
    },
    methods: {
        submit () {
            this.form.post(this.route('login'))
        }
    }
}
</script>