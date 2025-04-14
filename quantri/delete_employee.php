<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

$userID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$userID) {
    header("Location: managerment_employee.php");
    exit();
}

// Xóa người dùng từ bảng users
$stmt = $conn->prepare("DELETE FROM users WHERE userID = ?");
$stmt->bind_param("i", $userID);
$result = $stmt->execute();

if ($result) {
    $_SESSION['message'] = "Đã xóa nhân viên thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Lỗi khi xóa nhân viên: " . $stmt->error;
    $_SESSION['message_type'] = "danger";
}

header("Location: managerment_employee.php");
exit();
?>