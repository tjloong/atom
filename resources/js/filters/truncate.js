export default (str, len) => {
	if (!str) return '';
	if (!len) len = 30;

	const substr = str.substr(0, len)

	return str.length > len ? `${substr}...` : substr
}