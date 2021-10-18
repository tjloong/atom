export default (str, isXhtml) => {
	const tag = (isXhtml || typeof isXhtml === 'undefined') ? '<br />' : '<br>'
	return str ? (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + tag + '$2') : null
}