<!DOCTYPE html>
<html>
<head>
    <title>Xem log</title>
</head>
<body>

<?php
if(isset($_POST["xoa"]) and $_POST["xoa"]=="1"){


    $file = fopen("../storage/logs/laravel.log", 'w');

// Ghi chuỗi rỗng để xóa nội dung
    fwrite($file, '');

// Đóng file lại
    fclose($file);

    header("Location: log.php");
    exit();

}

?>

<form method="post" >
    <input type="hidden" name="xoa" value="1">
    <input type="submit" value="Xoá" />
</form>

<br/>


<iframe src="read_log.php" width="100%" height="800" ></iframe>

</body>
</html>
