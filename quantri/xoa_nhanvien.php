<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

$employeeID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$employeeID) {
    header("Location: quanly_nhanvien.php");
    exit();
}

// Xóa nhân viên
$stmt = $conn->prepare("DELETE FROM Employees WHERE employeeID = ?");
$stmt->bind_param("i", $employeeID);
$result1 = $stmt->execute();

$stmt = $conn->prepare("DELETE FROM Users WHERE userID = ?");
$stmt->bind_param("i", $employeeID);
$result2 = $stmt->execute();

if ($result1 && $result2) {
    header("Location: quanly_nhanvien.php");
    exit();
}

$conn->close();
?>