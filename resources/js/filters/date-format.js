const dayjs = require('@node/dayjs')
const relativeTime = require('@node/dayjs/plugin/relativeTime')

dayjs.extend(relativeTime)

export default (value, option) => {
	const date = dayjs(value, 'YYYY-MM-DD HH:mm:ss')
	const format = {
		time: 'h:mm A',
		date: 'DD MMM, YYYY',
		datetime: 'DD MMM, YYYY - h:mm A',
	}

	if (date.isValid()) {
		if (option && option.format) return date.format(option.format)
		else if (option == 'fromNow') return date.fromNow()
		else if (option == 'datetime') return date.format(format.datetime)
		else if (option == 'time') return date.format(format.time)

		else return date.format(format.date)
	}

	return value
}

