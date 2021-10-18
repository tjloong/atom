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
                        <div class="text-2xl font-bold mb-2">
                            Reset Password
                        </div>

                        <div class="font-medium text-gray-500 mb-6">
                            {{ email }}
                        </div>

                        <div v-if="errors.email" class="mb-4 text-sm bg-red-100 text-red-800 rounded p-4">
                            {{ errors.email }}
                        </div>
                        
                        <div class="w-full mb-4">
                            <div class="font-medium text-gray-500 text-xs uppercase mb-2">New Password</div>
                            <input type="password" v-model="form.password" class="form-input w-full" required autofocus>
                        </div>
                        
                        <div class="w-full mb-6">
                            <div class="font-medium text-gray-500 text-xs uppercase mb-2">Confirm Password</div>
                            <input type="password" v-model="form.password_confirmation" class="form-input w-full" required>
                        </div>

                        <btn submit size="md" :loading="form.processing" class="w-full">
                            Reset Password
                        </btn>
                    </div>
                </box>

                <div class="text-sm mt-4">
                    <inertia-link :href="route('login')" class="flex items-center">
                        <box-icon name="left-arrow-alt"></box-icon> Back to login
                    </inertia-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ResetPassword',
    layout: null,
    props: {
        email: String,
        token: String,
        errors: Object,
    },
    metaInfo: { title: 'Reset Password' },
    data () {
        return {
            form: this.$inertia.form({
                email: this.email,
                token: this.token,
                password: null,
                password_confirmation: null,
            }),
        }
    },
    methods: {
        submit () {
            this.form.post(this.route('password.reset'))
        }
    }
}
</script>
