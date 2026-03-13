/**
 * Thông báo thành công
 * @param message
 */
function sweetSuccess(message){
    $.toaster({
        priority : 'success',
        title: ' Thông báo',
        message: message,
    });
}
/**
 * Thông báo lỗi
 * @param message
 */
function sweetAlert(message, timer){
    if(!timer){
        timer = 1500;
    }
    Swal.fire({
        position: 'center',
        icon: 'error',
        title: 'Lỗi...!',
        text: message,
        showConfirmButton: false,
        timer: timer,
        showCloseButton: true
    });
}
