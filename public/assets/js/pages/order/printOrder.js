function printTicket($orderId) {
    $('#printTicketAffterPaymentSuccess').removeClass('animate-pulse');
    event.preventDefault();

    var url = "order/in-ve" + '/' + $orderId;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'blob';
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var blobUrl = URL.createObjectURL(xhr.response); // Tạo một URL cho đối tượng Blob
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            iframe.onload = function () {
                window.setTimeout(function () {
                    iframe.contentWindow.print(); // In nội dung của iframe
                    document.body.removeChild(iframe);
                    URL.revokeObjectURL(blobUrl); // Hủy URL của đối tượng Blob
                }, 0);
                // iframe.contentWindow.print(); // In nội dung của iframe
                // document.body.removeChild(iframe);
                // URL.revokeObjectURL(blobUrl); // Hủy URL của đối tượng Blob
            };
            iframe.src = blobUrl;
        }
    };
    xhr.send();

}