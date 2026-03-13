<div class="card">
    <form id="form_image" enctype="multipart/form-data" method="post" action="{{ route('config.process-banner', $id) }}">
        @csrf
        @method('PUT')
        <div class="card-header title_content">
            <b class="text-center">Cấu hình thông tin</b>
        </div>
        <div class="card-body border border-dashed border-start-0 border-end-0 row">
            <!-- bên trái -->
            @php($i = 0)
            @include('admin.config.include.showImage')
            <div class="row">
                @php($i = 1)
                @include('admin.config.include.showImage')
                <div class="col-8">
                    <div class="row">
                        @for ($i = 2; $i <= 5; $i++)
                            @include('admin.config.include.showImage')
                        @endfor
                    </div>
                </div>
            </div>

            <!-- bên trái -->
        </div>
        <div class="card-footer">
            <div class="col-sm-auto">
                <button class="btn  btn-primary" type="button" onclick="$('#form_image').submit()">
                    Cập nhật ảnh
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        for (let index = 0; index <= 5; index++) {
            var rowHeight = index == 1 ? '250px' : '100px';

            $('#image' + index).spartanMultiImagePicker({
                fieldName: 'image' + index,
                maxCount: 1,
                rowHeight: rowHeight,
                groupClassName: '',
                minFileSize: '12228',
                maxFileSize: '1024000',
                dropFileLabel: 'Drop Here',
                onExtensionErr: function(index, file) {
                    alert('Please only input png or jpg type file')
                },
                onSizeErr: function(index, file) {
                    const fileSizeKB = file.size / 1024 // Chuyển đổi kích thước thành kilobyte
                    if (fileSizeKB < 12) {
                        main_layout.alert_main('Kích cỡ ảnh ít nhất là 12KB', 'error')
                    } else {
                        main_layout.alert_main('Kích cỡ ảnh quá lớn', 'error')
                    }
                },
            })

            $('.remove-image-' + index).on('click', function() {
                $(this).parents('.image_div').remove()
                $('#wap_image' + index).append(`<div id="image` + index + `"></div>`)
                $('#image' + index).spartanMultiImagePicker({
                    fieldName: 'image' + index,
                    maxCount: 1,
                    rowHeight: rowHeight,
                    groupClassName: '',
                    minFileSize: '12228',
                    maxFileSize: '1024000',
                    dropFileLabel: 'Drop Here',
                    onExtensionErr: function(index, file) {
                        alert('Please only input png or jpg type file')
                    },
                    onSizeErr: function(index, file) {
                        const fileSizeKB = file.size / 1024
                        if (fileSizeKB < 12) {
                            main_layout.alert_main('Kích cỡ ảnh ít nhất là 12KB', 'error')

                        } else {
                            console.log(index, file)
                            main_layout.alert_main('Kích cỡ ảnh quá lớn', 'error')
                        }
                    },
                })
            })
        }
    })
</script>
