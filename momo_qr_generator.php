<?php
/**
 * Tạo QR code cho thanh toán MoMo
 * 
 * File này xử lý việc tạo QR code động cho thanh toán MoMo
 * Trong triển khai thực tế, bạn sẽ sử dụng API chính thức của MoMo
 */

// Hiển thị lỗi - chỉ sử dụng trong môi trường phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Xử lý request
header('Content-Type: image/png');

// Lấy các thông số từ URL
$phone = isset($_GET['phone']) ? $_GET['phone'] : '0326097576';
$amount = isset($_GET['amount']) ? intval($_GET['amount']) : 0;
$description = isset($_GET['description']) ? $_GET['description'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : 'theCleaner';

// Tạo nội dung QR code theo định dạng MoMo
// Format: 2|99|phone|name|description|0|0|amount
$qrContent = "2|99|{$phone}|{$name}|{$description}|0|0|{$amount}";

// Lưu log để debug
file_put_contents('momo_qr_log.txt', date('Y-m-d H:i:s') . " - Generated QR: " . $qrContent . "\n", FILE_APPEND);

// Sử dụng thư viện QR code (có thể thay thế bằng thư viện thực tế của bạn)
// Trong ví dụ này, chúng ta sử dụng API QR Server để tạo QR code
$url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrContent);

// Lấy nội dung QR code từ API
$qrImage = @file_get_contents($url);

// Kiểm tra lỗi khi lấy QR code
if ($qrImage === false) {
    // Nếu không lấy được QR code từ API, tạo một hình ảnh lỗi đơn giản
    $img = imagecreate(250, 250);
    $bg = imagecolorallocate($img, 255, 255, 255);
    $textcolor = imagecolorallocate($img, 255, 0, 0);
    imagestring($img, 5, 50, 100, "QR Code Error", $textcolor);
    imagestring($img, 3, 50, 120, "Please try again", $textcolor);
    
    // Xuất hình ảnh lỗi
    imagepng($img);
    imagedestroy($img);
} else {
    // Xuất QR code
    echo $qrImage;
}
?>