document.addEventListener('DOMContentLoaded', function () {
  const tabKey = 'pricing_active_tab'
  const tabs = document.querySelectorAll('#pricingTabs li')
  const panes = document.querySelectorAll('.tab-pane')

  // Tab mặc định nếu chưa có localStorage
  let activeTabId = localStorage.getItem(tabKey) || 'tab-court'

  const activeTab = document.querySelector(`#pricingTabs li[data-tab="${activeTabId}"]`)
  const activePane = document.getElementById(activeTabId)

  if (activeTab && activePane) {
    activeTab.classList.add('active')
    activePane.classList.add('active')
  }

  // Save tab khi click
  tabs.forEach(tab => {
    tab.addEventListener('click', function () {
      localStorage.setItem(tabKey, this.dataset.tab)
    })
  })
})
