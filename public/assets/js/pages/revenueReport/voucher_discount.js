function showVoucherDetail(voucherId) {
  $(`#voucherModal-${voucherId}`).modal('show')
}

function showPromotionalDetail(promotionalId) {
  fetch(`/admin/RevenueReport/promotional-detail-report/${promotionalId}`)
    .then(response => response.json())
    .then(data => {
      if (data.status) {
        const template = document.getElementById(`promotionalModal-${promotionalId}`);
        console.log(data.data);

        if (!template) {
          console.error('Template không tồn tại')
          return
        }

        if(data.data.condition_type === 1 && data.data.type === 1) {
           template.innerHTML = template.innerHTML.replace(/__DESC__/g, `Giảm ${data.data.rate} % cho đơn hàng từ ${data.data.condition_value}đ`)
        }else if(data.data.condition_type === 1 && data.data.type === 2) {
           template.innerHTML = template.innerHTML.replace(/__DESC__/g, `Giảm ${data.data.rate}đ cho đơn hàng từ ${data.data.condition_value}đ`)
        }else if(data.data.condition_type === 2 && data.data.type === 1) {
           template.innerHTML = template.innerHTML.replace(/__DESC__/g, `Giảm ${data.data.rate} % cho đơn hàng từ ${data.data.condition_value}đ`)
        }else {
           template.innerHTML = template.innerHTML.replace(/__DESC__/g, `Giảm ${data.data.rate}đ cho đơn hàng từ ${data.data.condition_value}đ`)
        }

       

        template.innerHTML = template.innerHTML.replace(/__ID__/g, data.data.id)
        template.innerHTML = template.innerHTML.replace(/__NAME__/g, data.data.name)

        template.innerHTML = template.innerHTML.replace(/__USED_COUNT__/g, data.data.discounts.length)
        template.innerHTML = template.innerHTML.replace(/__LIMITED__/g, data.data.limited ?? '-')
        template.innerHTML = template.innerHTML.replace(/__EXPIRY__/g, `${main.formatDate(data.data.expiry_date_from)} - ${main.formatDate(data.data.expiry_date_to)}`);
        let html = '';
              
        let index = 0;

        data.data.discounts.forEach(discount => {
        const order = discount.order;
  
        if (discount.is_delete !== 0 || !order || order.payment_status !== 2) return;

        const codeOrder = order.code_order || 'N/A';
        const customerName = order.customer?.name || 'Khách lẻ';
        const customerPhone = order.customer?.phone ? `<small class="text-muted">${order.customer.phone}</small>` : '';
        const createdAt = new Date(order.created_at).toLocaleString('vi-VN');
        const realAmount = Number(order.real_amount || 0).toLocaleString('vi-VN') + 'đ';
        const discountAmount = Number(order.discount || 0).toLocaleString('vi-VN') + 'đ';
        const amount = Number(order.amount || 0).toLocaleString('vi-VN') + 'đ';

        html += `
        <tr>
            <td style="text-align: center">${index}</td>
            <td><span class="text-primary" style="font-size: 14px">${codeOrder}</span></td>
            <td>
            ${order.customer?.name ? `<div class="fw-semibold">${customerName}</div>${customerPhone}` : '<span class="">Khách lẻ</span>'}
            </td>
            <td>${createdAt}</td>
            <td class="text-center"><span>${realAmount}</span></td>
            <td class="text-center"><span style="font-size: 14px">${discountAmount}</span></td>
            <td class="text-center"><span style="font-size: 14px">${amount}</span></td>
        </tr>
        `;

        index++;
    });
  $(`#promotionalModal-${promotionalId} .promotional-detail-table`).append(html)

        $(`#promotionalModal-${promotionalId}`).modal('show')
      } else {
        //...
      }
    })
    .catch(error => {
      console.error('Error fetching promotional detail:', error)
    })
}

function handleSearch() {
  const form_voucher = document.getElementById('form-search-report-voucher')
  const form_promotional = document.getElementById('form-search-report-promotional')
  setTimeout(() => {
    if (form_voucher) {
      form_voucher.submit()
    }
    if (form_promotional) {
      form_promotional.submit()
    }
  }, 1000)
}

function openVoucherDrawer(voucherId) {
  console.log(voucherId);
  fetch(`/admin/RevenueReport/voucher-details/${voucherId}`)
    .then(response => response.json())
    .then(data => {
      console.log(data)
      if (data.status) {
        renderVoucherDetails(data.data)
        document.getElementById('voucherDrawer').style.display = 'block'
        setTimeout(() => {
          document.getElementById('voucherDrawer').classList.add('show')
        }, 10)
      } else {
        console.error('Không thể tải dữ liệu voucher details')
      }
    })
    .catch(error => {
      console.error('Error fetching voucher detail:', error)
    })
}


function closeVoucherDrawer() {
  document.getElementById('voucherDrawer').classList.remove('show')
  setTimeout(() => {
    document.getElementById('voucherDrawer').style.display = 'none'
    closeOrderDrawer() // Close level 2 drawer if open
  }, 300)
}

function openOrderDrawer(voucherDetailId, voucherCode) {
  document.getElementById('voucherCode').textContent = voucherCode

  fetch(`/admin/RevenueReport/voucher-order-details/${voucherDetailId}`)
    .then(response => response.json())
    .then(data => {
      if (data.status) {
        renderOrderDetails(data.data)
        document.getElementById('orderDrawer').style.display = 'block'
        setTimeout(() => {
          document.getElementById('orderDrawer').classList.add('show')
        }, 10)
      }
    })
}

function closeOrderDrawer() {
  document.getElementById('orderDrawer').classList.remove('show')
  setTimeout(() => {
    document.getElementById('orderDrawer').style.display = 'none'
  }, 300)
}

$limit = 0

function renderVoucherDetails(data) {
  let html = '<div class="list-group">'
  
  // Lấy thông tin voucher từ item đầu tiên (vì tất cả đều có cùng voucher)
  const firstItem = data[0];
  const voucherInfo = firstItem.voucher;
  
  // Cập nhật thông tin voucher chung
  $('#voucherName').text(voucherInfo.name)
  $('#expiry').text(
    `${main.formatDate(voucherInfo.expiry_date_from)} - ${main.formatDate(voucherInfo.expiry_date_to)}`
  )
  
  // Xác định loại giảm giá
  const type = voucherInfo.type == 1 ? '%' : 'đ'
  const conditionType = voucherInfo.condition_type == 1 ? 'có giá trị tối thiểu' : 'có số lượng vé tối thiểu'
  const conditionConditrion = voucherInfo.condition_type == 1 ? 'đ' : 'vé'
  $('#voucherDesc').text(
    `Giảm ${voucherInfo.rate} ${type} cho đơn hàng ${conditionType} là ${voucherInfo.condition_value} ${conditionConditrion}`
  )
  
  // Tính tổng số voucher và số lượt giới hạn
  const totalVouchers = data.length;
  const limitText = voucherInfo.limited ? voucherInfo.limited : 'Không giới hạn';
  $('#countTurn').text(`${totalVouchers} voucher`)
  
  // Render từng voucher detail
  data.forEach(voucherDetail => {
    // Đếm số lượt đã sử dụng (discount không rỗng)
    const usageCount = voucherDetail.discount ? voucherDetail.discount.length : 0;
    
    // Kiểm tra trạng thái hết lượt
    const isOutOfStock = voucherInfo.limited && usageCount >= voucherInfo.limited;
    
    html += `
      <div class="list-group-item">
        <div class="d-flex justify-content-between align-items-center row">
          <div class="col-md-1" onclick="openOrderDrawer('${voucherDetail.id}', '${voucherDetail.code}')">       
            <button type="button" class="btn rounded-pill btn-soft-info waves-effect waves-light" style="width:40px; height:40px">
              <i class="ri-arrow-left-s-line"></i>
            </button>
          </div>

          <div class="col-md-11 d-flex">  
            <div class="w-50">
              <h6 class="mb-1">
                <code style="font-size: 14px;" class="text-primary">${voucherDetail.code}</code>
              </h6>
              <small class="text-muted">
                Trạng thái: ${voucherDetail.status == 1 ? 'Hoạt động' : 'Không hoạt động'}
              </small>
            </div>
            <span class="w-25">
              <span class="text-danger">${usageCount}</span> / 
              <span class="text-success">${limitText}</span>
            </span>
            <span class="badge w-25 ${isOutOfStock ? 'bg-danger' : 'bg-success'}" style="font-size: 14px; align-self: center">
              ${isOutOfStock ? 'Hết lượt' : 'Còn lượt'}
            </span>
          </div>
        </div>
      </div>
    `
  })

  html += '</div>'
  document.getElementById('voucherDetailsContent').innerHTML = html
}


function renderOrderDetails(orders) {
  console.log(orders);
  $totalRealAmount = orders.reduce((total, d) => total + parseFloat(d.real_amount), 0) //doanh thu
  $totalDiscountAmount = orders.reduce((total, d) => total + parseFloat(d.order_discount), 0) // giảm giá
  $totalOrderAmount = orders.reduce((total, d) => total + parseFloat(d.order_amount), 0) //thực thu

  // $('#countTurn').text(orders.length);

  let html = `
        <div class="table-responsive">
            <table class="table table-bordered  mb-0">
                <thead class="table-light">
                    <tr class="" style="font-weight: 300;">
                        <th>#</th>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Thời gian mua</th>
                        <th style="text-align:right">Thành tiền</th>
                        <th style="text-align:right">Giảm giá</th>
                        <th style="text-align:right">Thực thu</th>
                    </tr>
                </thead>
                <tbody>
    `

  orders.forEach((order, index) => {
    html += `
            <tr>
             <td><span class="">${index + 1}</span></td>
                <td><span class="text-primary">${order.code_order}</span></td>
                <td>${order.customer_name || 'Khách lẻ'}</td>
                <td>${new Date(order.order_date).toLocaleString('vi-VN')}</td>
                <td class="text-end">${new Intl.NumberFormat('vi-VN').format(
                  order.real_amount
                )}đ</td>
                <td class="text-end">${new Intl.NumberFormat('vi-VN').format(
                  order.order_discount
                )}đ</td>
                <td class="text-end">${new Intl.NumberFormat('vi-VN').format(
                  order.real_amount - order.order_discount
                )}đ</td>
            </tr>
        `
  })
  html += `
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                        <td class="text-end">${new Intl.NumberFormat('vi-VN').format(
                          $totalRealAmount
                        )}đ</td>
                        <td class="text-end">${new Intl.NumberFormat('vi-VN').format(
                          $totalDiscountAmount
                        )}đ</td>
                        <td class="text-end">${new Intl.NumberFormat('vi-VN').format(
                          $totalOrderAmount
                        )}đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `

  document.getElementById('orderDetailsContent').innerHTML = html
}
