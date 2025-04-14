<?php
/**
 * Tạo QR code cho thanh toán MoMo
 * 
 * File này xử lý việc tạo QR code động cho thanh toán MoMo
 * Sử dụng database để lưu log thay vì file text
 */

// Import config và database connection
require_once 'config.php';
require_once 'database_connection.php';

// Xử lý request
header('Content-Type: image/png');

// Lấy các thông số từ URL
$phone = isset($_GET['phone']) ? $_GET['phone'] : get_config('momo_phone', '0326097576');
$amount = isset($_GET['amount']) ? intval($_GET['amount']) : 0;
$description = isset($_GET['description']) ? $_GET['description'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : get_config('company_name', 'theCleaner');
$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';

// Kiểm tra và làm sạch dữ liệu
$phone = preg_replace('/[^0-9+]/', '', $phone);
$amount = max(0, $amount);
$description = substr(trim($description), 0, 255);
$name = substr(trim($name), 0, 100);
$transaction_id = substr(trim($transaction_id), 0, 100);

// Tạo nội dung QR code theo định dạng MoMo
// Format: 2|99|phone|name|description|0|0|amount
$qrContent = "2|99|{$phone}|{$name}|{$description}|0|0|{$amount}";

// Lưu log vào database thay vì file
try {
    $log_data = [
        'transaction_id' => $transaction_id,
        'phone' => $phone,
        'amount' => $amount,
        'description' => $description,
        'qr_content' => $qrContent
    ];
    
    $log_id = db_insert('momo_logs', $log_data);
    
    if (!$log_id) {
        error_log("Không thể ghi log MoMo QR Code: " . json_encode($log_data));
    }
} catch (Exception $e) {
    error_log("Lỗi khi ghi log MoMo QR Code: " . $e->getMessage());
}

// Tạo QR code
generateQRCode($qrContent);

/**
 * Hàm tạo QR code
 * 
 * @param string $content Nội dung QR code
 */
function generateQRCode($content) {
    // Sử dụng thư viện QR code (có thể thay thế bằng thư viện thực tế của bạn)
    // Trong ví dụ này, chúng ta sử dụng API QR Server để tạo QR code
    $url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($content);
    
    // Lấy nội dung QR code từ API
    $qrImage = @file_get_contents($url);
    
    // Kiểm tra lỗi khi lấy QR code
    if ($qrImage === false) {
        generateErrorQRCode();
    } else {
        // Xuất QR code
        echo $qrImage;
    }
}

/**
 * Hàm tạo QR code lỗi
 */
function generateErrorQRCode() {
    // Tạo một hình ảnh lỗi đơn giản
    $img = imagecreate(250, 250);
    $bg = imagecolorallocate($img, 255, 255, 255);
    $textcolor = imagecolorallocate($img, 255, 0, 0);
    imagestring($img, 5, 50, 100, "QR Code Error", $textcolor);
    imagestring($img, 3, 50, 120, "Please try again", $textcolor);
    
    // Xuất hình ảnh lỗi
    imagepng($img);
    imagedestroy($img);
}
?>