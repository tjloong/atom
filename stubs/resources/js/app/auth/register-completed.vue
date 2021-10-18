<template>
    <div class="max-w-screen-md mx-auto px-6 pt-20 pb-40">
        <div class="absolute inset-0">
            <img src="/storage/img/blob-bg.svg" class="w-full h-full object-cover object-center opacity-50">
        </div>

        <div class="max-w-lg mx-auto relative">
            <a class="block w-28 mb-4" href="/">
                <img src="/storage/img/logo.svg" class="w-full">
            </a>

            <div class="text-3xl font-bold mb-2">
                Thank you for signing up with us
            </div>

            <p class="mb-4">
                Your account is successfully created. We are so excited to have you as our newest friend!
            </p>

            <inertia-link class="py-2 px-4 bg-theme text-white inline-flex items-center rounded-md shadow" :href="route('dashboard')">
                Go to Dashboard <icon name="chevron-right"></icon>
            </inertia-link>
        </div>
    </div>    
</template>

<script>
export default {
    name: 'RegisterCompleted',
    layout: null,
}
</script>
