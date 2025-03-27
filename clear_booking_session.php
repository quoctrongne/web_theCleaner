<?php
/**
 * File này xóa thông tin đặt lịch khỏi session sau khi hiển thị trang đặt lịch thành công
 */

// Khởi tạo session nếu chưa có
session_start();

// Xóa các biến session liên quan đến đặt lịch
unset($_SESSION['booking_success']);
unset($_SESSION['booking_id']);
unset($_SESSION['booking_name']);
unset($_SESSION['booking_service']);
unset($_SESSION['booking_date']);
unset($_SESSION['booking_time']);
unset($_SESSION['booking_address']);
unset($_SESSION['booking_area']);

// Trả về phản hồi JSON (nếu cần)
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>