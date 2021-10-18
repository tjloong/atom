export default (num, code = null, bracket = true) => {
	num = parseFloat(num)
	if (!Number.isFinite(num)) num = 0

	let currency = num.toLocaleString('en-US', { 
		style: 'currency', 
		currency: code || 'USD',
	})

	if (!code) currency = currency.replace('$', '').trim()
	if (bracket && num < 0) currency = `(${currency.replace('-', '').trim()})`

	return currency.trim()
}
