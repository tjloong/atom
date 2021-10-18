export default (obj) => {
	if (!obj) return obj
	if (typeof obj == 'string') return obj

	const address = [obj.name, obj.addr1, obj.addr2, obj.city, obj.zip, obj.state, obj.country]

	return address.filter(Boolean).join(', ')
}
