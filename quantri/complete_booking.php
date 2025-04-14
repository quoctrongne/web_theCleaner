<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập cho nhân viên Cleaner
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Cleaner') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bookingID'])) {
    $bookingID = $_POST['bookingID'];

    $sql = "UPDATE bookings SET status = 'completed' WHERE bookingID = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Lỗi khi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt->bind_param("i", $bookingID);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Trạng thái đơn hàng đã được cập nhật thành 'Hoàn thành'.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật trạng thái: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
}

$conn->close();
header("Location: cleaner_dashboard.php");
exit();
?>
