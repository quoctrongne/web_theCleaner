<?php
// Đường dẫn đến autoload của Composer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Hàm gửi email xác nhận đặt lịch
 * 
 * @param array $bookingInfo Thông tin đặt lịch
 * @param string $emailTemplate Mẫu email HTML
 * @param string $bookingCode Mã đặt lịch
 * @return bool Trạng thái gửi email
 */
function sendBookingConfirmationEmail($bookingInfo, $emailTemplate, $bookingCode) {
    // Tạo file log nếu chưa tồn tại
    $logFile = __DIR__ . '/email_debug.log';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Bắt đầu gửi email đến: " . $bookingInfo['email'] . "\n", FILE_APPEND);
    
    $mail = new PHPMailer(true);
    
    try {
        // Bật chế độ debug SMTP (mức 2 hiển thị đầy đủ)
        $mail->SMTPDebug = 3; // 0: tắt debug, 1: chỉ hiện lỗi, 2: tin nhắn, 3: tất cả
        
        // Bắt debug output để lưu vào file thay vì hiển thị
        $debugOutput = "";
        $mail->Debugoutput = function($str, $level) use (&$debugOutput, $logFile) {
            $debugOutput .= $str . "\n";
            file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $str . "\n", FILE_APPEND);
        };
        
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      // SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nguyenquoctrongbt2018@gmail.com'; // Email Gmail của bạn
        // Sử dụng mật khẩu mới
        $mail->Password   = 'mvdk qwii zmbn nloo'; // App Password mới
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Thông tin người gửi và người nhận
        $mail->setFrom('nguyenquoctrongbt2018@gmail.com', 'theCleaner Service'); // Dùng cùng email với Username
        $mail->addAddress($bookingInfo['email'], $bookingInfo['name']);
        
        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = "Xác Nhận Đặt Lịch - theCleaner #" . $bookingCode;
        $mail->Body    = $emailTemplate;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailTemplate));
        
        // Ghi log trước khi gửi
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Chuẩn bị gửi email\n", FILE_APPEND);
        
        $mail->send();
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Email đã gửi thành công\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        $errorMsg = "Không thể gửi email: " . $mail->ErrorInfo;
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: " . $errorMsg . "\n", FILE_APPEND);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - DEBUG: " . $debugOutput . "\n", FILE_APPEND);
        
        error_log($errorMsg);
        return false;
    }
}
?>