const colors = {
    'info': 'darkblue',
    'alert': 'indianred',
    'warning': 'goldenrod',
    'success': 'mediumseagreen',
}

const icons = {
    'info': 'message',
    'alert': 'message-x',
    'warning': 'message-error',
    'success': 'message-check',
}

/**
* Alert
*/
const $alert = (content = null, type = 'info') => {
    const title = content?.title ? `<div class="font-semibold mb-3">${content.title}</div>` : ''
    const message = content?.message || content || ''

    const el = document.createElement('div')
    el.classList.add('fixed', 'inset-0', 'z-20', 'flex', 'items-center', 'justify-center')
    el.innerHTML = `
        <div class="bg-black opacity-80 w-full h-full" id="bg"></div>
        <div class="absolute p-4">
            <div class="max-w-lg mx-auto bg-white rounded-md border p-4 animate-fade-in-up" style="min-width: 20rem;">
                <div class="flex mb-4">
                    <box-icon name="${icons[type]}" color="${colors[type]}" class="flex-shrink-0 mr-2"></box-icon>
                    <div class="flex-grow self-center">
                        ${title}
                        ${message}
                    </div>
                </div>

                <button type="button" class="py-1.5 px-4 text-center font-medium text-xs uppercase text-blue-600 rounded-md bg-blue-100">
                    Close
                </button>
            </div>
        </div>
    `
    const dismiss = () => el.remove()

    el.querySelector('button').addEventListener('click', dismiss)
    el.querySelector('#bg').addEventListener('click', dismiss)

    document.body.appendChild(el)
}

/**
* Toast
*/
const $toast = (msg, type = 'info', duration = 2500, pos = 'bottom center') => {
    const ypos = pos.split(' ')[0]
    const xpos = pos.split(' ')[1]

    if (ypos === 'top') pos = 'top-10'
    if (ypos === 'center') pos = 'top-1/2 transform -translate-y-1/2'
    if (ypos === 'bottom') pos = 'bottom-10'

    if (xpos === 'left') pos = `${pos} left-10`
    if (xpos === 'center') pos = `${pos} left-1/2 transform -translate-x-1/2`
    if (xpos === 'right') pos = `${pos} right-10`

    const borderColors = {
        info: 'border-blue-500',
        warning: 'border-yellow-500',
        alert: 'border-red-500',
        success: 'border-green-500',
    }

    const el = document.createElement('div')
    el.innerHTML = `
        <div class="fixed z-20 ${pos}">
            <div class="max-w-md mx-auto bg-white rounded-md border shadow-lg p-1.5 animate-fade-in-up" style="min-width: 200px;">
                <a class="float-right" id="close">
                    <box-icon name="x" size="20px" color="dimgray"></box-icon>
                </a>

                <div class="flex px-5 py-2 border-l-4 ${borderColors[type]}">
                    <box-icon name="${icons[type]}" color="${colors[type]}" class="flex-shrink-0 mr-2"></box-icon>
                    <div class="flex-grow self-center font-medium">
                        ${msg}
                    </div>
                </div>
            </div>
        </div>
    `

    const dismiss = () => el.remove()

    el.querySelector('#close').addEventListener('click', dismiss)

    setTimeout(dismiss, duration);

    document.body.appendChild(el)
}

/**
* Confirmation prompt
*/
const $confirm = (data) => {
    const title = data?.title ? `<div class="font-semibold mb-3">${data.title}</div>` : ''
    const message = data?.message || ''

    const el = document.createElement('div')
    el.classList.add('fixed', 'inset-0', 'z-20', 'flex', 'items-center', 'justify-center')
    el.innerHTML = `
        <div class="bg-black opacity-80 w-full h-full" id="bg"></div>
        <div class="absolute p-4">
            <div class="max-w-lg mx-auto bg-white rounded-md border p-4 animate-fade-in-up" style="min-width: 20rem;">
                <div class="flex mb-6">
                    <box-icon name="message" color="dimgray" class="flex-shrink-0 mr-2"></box-icon>
                    <div class="flex-grow self-center">
                        ${title}
                        ${message}
                    </div>
                </div>

                <div class="flex items-center">
                    <button type="button" id="yes" class="py-1.5 px-4 text-center text-sm font-semibold uppercase text-green-800 rounded-md bg-green-100 mr-1.5">
                        Yes
                    </button>

                    <button type="button" id="no" class="py-1.5 px-4 text-center text-sm font-semibold uppercase text-gray-600 rounded-md hover:bg-gray-100">
                        No
                    </button>
                </div>
            </div>
        </div>
    `
    const accepted = () => {
        el.remove()
        data.onConfirmed && data.onConfirmed()
    }

    const rejected = () => {
        el.remove()
        data.onRejected && data.onRejected()
    }

    el.querySelector('button#yes').addEventListener('click', accepted)
    el.querySelector('button#no').addEventListener('click', rejected)
    el.querySelector('#bg').addEventListener('click', () => el.remove())

    document.body.appendChild(el)
}

/**
* Handle page error
*/
const $error = (e) => {
    const key = Object.keys(e)[0]

    if (key) $toast(e[key], 'alert')
    else $toast('There were some error while performing the operation', 'alert')
}

export default {
    install (Vue) {
        Vue.prototype.$alert = $alert
        Vue.prototype.$toast = $toast
        Vue.prototype.$confirm = $confirm
        Vue.prototype.$error = $error
    }
};

