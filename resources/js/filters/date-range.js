export default (value) => {
	if (_.isObject(value)) {
		let from = value.from
		let to = value.to

		if (from && to) return `from ${from} to ${to}`
		if (from && !to) return `from ${from} onward`
		if (!from && to) return `until ${to}`
	}
	else return value
}
