<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);

// Đặt trạng thái khóa trong bảng users
$stmt = $conn->prepare("UPDATE users SET status='locked' WHERE userID=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Đã khóa tài khoản nhân viên thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Lỗi khi khóa tài khoản: " . $stmt->error;
    $_SESSION['message_type'] = "danger";
}

header("Location: managerment_employee.php");
exit();
?>