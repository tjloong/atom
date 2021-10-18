export default  (value) => {
	return value
		.replace('-', ' ')
		.replace(/\w\S*/g, (txt) => (txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()))
}
