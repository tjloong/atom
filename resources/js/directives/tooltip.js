import { createPopper } from '@node/@popperjs/core'

export default (el, binding) => {
    if (!binding.value) return
    
    let popper

    let tooltip = document.createElement('div')
    tooltip.classList.add('py-1', 'px-2', 'rounded', 'bg-black', 'opacity-90', 'text-white', 'text-xs', 'hidden')
    tooltip.innerHTML = binding.value

    el.append(tooltip)

    el.addEventListener('mouseover', (e) => {
        if (!popper) popper = createPopper(el, tooltip, { placement: 'auto' })
        tooltip.classList.remove('hidden')  
    })

    el.addEventListener('mouseout', (e) => {
        if (popper) setTimeout(() => popper.destroy, 100)
        tooltip.classList.add('hidden')
    })
}