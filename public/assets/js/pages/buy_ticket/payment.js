function showModalPayment(data) {
    paymentData = data.paymentData;

    $('#qrPaymentModal').empty();
    $('#codePaymentModal').empty();
    $('#mtcPaymentModal').empty();
    $('#descPaymentModal').empty();
    $('#totalPaymentModal').empty();

    $('#qrPaymentModal').qrcode({
        text: paymentData.result_api.qrCode,
        width: 200, // Kích thước của mã QR  
        height: 200
    });

    $('#codePaymentModal').text(paymentData.result_api.orderId);
    $('#mtcPaymentModal').text(paymentData.result_api.requestCode);
    $('#descPaymentModal').text(payment.paymentObjectName);
    $('#totalPaymentModal').text(main_layout.formattedNumber(paymentData.result_api.amount) + ' ' + paymentData.result_api.currency);

    $('#questionPaymentBtn').on('click', function () {
        questionPayment(paymentData.result_api.orderId, true);
    });

    countDown(paymentData.payment_last_time, paymentData.result_api.orderId);

    $('#modalPayment').modal('show');
    main_layout.hide_loader();
}

let countdownInterval; // định nghĩa biến toàn cục  

function countDown(date_time, orderId) {
    const endTime = new Date(date_time).getTime();

    const updateCountdown = () => {
        const now = new Date().getTime();
        const timeLeft = endTime - now;
        if (timeLeft <= 0) {
            questionPayment(orderId, true);
            $('#countdownPaymentModal').text("Thời gian đã hết!");
            clearInterval(countdownInterval); // Dừng cập nhật  
            return;
        }
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        $('#countdownPaymentModal').text(`${minutes < 10 ? '0' + minutes : minutes}:${seconds < 10 ? '0' + seconds : seconds}s`);

        questionPayment(orderId);
    };

    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

function questionPayment(orderId, lasttime = false) {
    $.ajax({
        url: payment.url_payment_result,
        type: 'get',
        data: {
            orderId: orderId,
            check_payment: true,
            update_order: true
        },
        success: function (result) {
            if (lasttime || result.status == 1) {
                $('#modalPayment').modal('hide');
                main_layout.alert_main(result.error, result.status == 1 ? 'success' : 'error');
                clearInterval(countdownInterval); // Dừng đếm giờ ở đây  

                setTimeout(() => {
                    location.reload();
                }, 1000);
            }

            if (result.status == 2 || result.status == 3) {
                $('#modalPayment').modal('hide');
                main_layout.alert_main(result.error, 'error');
                clearInterval(countdownInterval); // Dừng đếm giờ ở đây  
            }
        }
    });
}  