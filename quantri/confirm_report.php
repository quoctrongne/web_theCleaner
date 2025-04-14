<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_report']) && isset($_POST['reportID'])) {
    $reportID = $_POST['reportID'];

    $sqlUpdate = "UPDATE thongke_baocao SET trangThai = 'Xác nhận' WHERE id = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("i", $reportID);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Phiếu xuất kho đã được xác nhận!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi xác nhận phiếu: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
}
$conn->close();
header("Location: reports.php");
exit();
?>
