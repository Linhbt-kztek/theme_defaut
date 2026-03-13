<?php

//$ten_file = "../storage/logs/laravel.log";
//
//// Đọc nội dung của file
//$noi_dung = file_get_contents($ten_file);
//
//// In nội dung ra màn hình
//echo nl2br($noi_dung);


$file = fopen("../storage/logs/laravel.log", 'r');
if ($file) {
    while (($line = fgets($file)) !== false) {
        echo nl2br($line);
    }
    fclose($file);
}
?>

