<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền người dùng
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Lấy serviceID từ URL
$serviceID = isset($_GET['id']) ? $_GET['id'] : null;
if ($serviceID === null) {
    echo "Không có ID dịch vụ!";
    exit();
}

// Xóa dịch vụ khỏi cơ sở dữ liệu
$sql = "DELETE FROM services WHERE serviceID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $serviceID);
if ($stmt->execute()) {
    // Xóa thành công, chuyển hướng về trang danh sách dịch vụ
    header("Location: managerment_service.php?message=Xóa dịch vụ thành công");
    exit();
} else {
    echo "Xóa dịch vụ thất bại!";
    exit();
}

// Đóng kết nối
$conn->close();
?>
