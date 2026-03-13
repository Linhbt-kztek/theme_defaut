document.addEventListener('DOMContentLoaded', () => {
  $(document).on('click', '.customer-input', function (e) {
    const wrapper = $(this).closest('.customer-wrapper')
    $('.customer-dropdown').addClass('d-none')
    wrapper.find('.customer-dropdown').removeClass('d-none')
    e.stopPropagation()
  })

  $(document).on('click', function (e) {
    if (!$(e.target).closest('.customer-wrapper').length) {
      $('.customer-dropdown').addClass('d-none')
    }
  })

  // Click chọn khách hàng
  $(document).on('click', '.customer-item', function () {
    const id = $(this).data('id')
    const name = $(this).data('name')
    const phone = $(this).data('phone')
    const address = $(this).data('address')


    const wrapper = $(this).closest('.customer-dropdown').closest('.customer-wrapper')
    const input = wrapper.find('.customer-input')

    const display = `${name} | ${phone}`
    input.val(display)

    const form = wrapper.closest('form')
    if (form.length) {
      form.find('input[name="customer_id"]').val(id)
    }

    const sbId = $('#sbCustomerId')
    if (sbId.length) sbId.val(id)

    wrapper.find('.customer-dropdown').addClass('d-none')
  })

  // Search
  $(document).on('keyup', '.customer-dropdown .dropdown-search input', function () {
    const keyword = $(this).val().toLowerCase().trim()
    const list = $(this).closest('.customer-dropdown').find('.customer-item')
    list.each(function () {
      const name = $(this).data('name').toLowerCase()
      const phone = String($(this).data('phone')).toLowerCase()
      const address = $(this).data('address')

      console.log({ name, phone, address, keyword });
      console.log(name.includes(keyword), phone.includes(keyword), address.includes(keyword));
      const match = name.includes(keyword) || phone.includes(keyword) || address.includes(keyword)


      $(this).toggleClass('d-none', !match)
    })
  })
})
