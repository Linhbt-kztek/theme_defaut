document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('rentalType')?.addEventListener('click', e => {
    const tab = e.target.closest('li[data-value]')
    if (!tab) return
    setActiveTab(tab)
    currentType = tab.dataset.value
  })
})
