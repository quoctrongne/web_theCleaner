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
    $mail = new PHPMailer(true);
    
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      // SMTP server của bạn
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nguyenquoctrongbt2018@gmail.com'; // Email của bạn
        $mail->Password   = 'ueti rpzn cmlq qoyp';    // Mật khẩu ứng dụng (App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        // Thông tin người gửi và người nhận
        $mail->setFrom('your_email@gmail.com', 'theCleaner Service'); // Email và tên người gửi
        $mail->addAddress($bookingInfo['email'], $bookingInfo['name']);
        
        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = "Xác Nhận Đặt Lịch - theCleaner #" . $bookingCode;
        $mail->Body    = $emailTemplate;
        $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $emailTemplate));
        
        $mail->send();
        error_log("Email xác nhận đã gửi thành công đến: " . $bookingInfo['email']);
        return true;
    } catch (Exception $e) {
        error_log("Không thể gửi email: {$mail->ErrorInfo}");
        return false;
    }
}
?>