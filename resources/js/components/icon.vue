<template>
	<box-icon 
		v-if="name"
		:name="name"
        :size="dimension"
		color="currentcolor"
		class="icon mx-1"
	/>
</template>

<script>
export default {
	name: 'Icon',
	props: {
		name: String,
		size: {
			type: String,
			default: 'sm',
		},
	},
	computed: {
		dimension () {
			if (this.size === 'xs') return '16px'
			if (this.size === 'sm') return '20px'
			if (this.size === 'md') return '24px'
			if (this.size === 'lg') return '28px'
			if (this.size === 'xl') return '32px'
			if (this.size === '2xl') return '40px'

			return this.size
		}
	}
}
</script>
