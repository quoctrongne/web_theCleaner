<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Consultant') {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu và lấy dữ liệu như trước
require_once("../db/conn.php");

// Lấy thông tin thống kê
$totalCustomers = 0;
$totalEmployees = 0;
$pendingRentals = 0;
$recentRentals = [];

// Truy vấn số lượng khách hàng
$sqlCustomers = "SELECT COUNT(*) AS total FROM Customers";
$resultCustomers = $conn->query($sqlCustomers);
if ($resultCustomers && $resultCustomers->num_rows > 0) {
    $rowCustomers = $resultCustomers->fetch_assoc();
    $totalCustomers = $rowCustomers['total'];
}

// Truy vấn số lượng nhân viên
$sqlEmployees = "SELECT COUNT(*) AS total FROM Employees";
$resultEmployees = $conn->query($sqlEmployees);
if ($resultEmployees && $resultEmployees->num_rows > 0) {
    $rowEmployees = $resultEmployees->fetch_assoc();
    $totalEmployees = $rowEmployees['total'];
}

// Truy vấn số lượng lịch thuê đang chờ xử lý
$sqlRentals = "SELECT COUNT(*) AS total FROM Rentals WHERE status = 'Đã đặt'";
$resultRentals = $conn->query($sqlRentals);
if ($resultRentals && $resultRentals->num_rows > 0) {
    $rowRentals = $resultRentals->fetch_assoc();
    $pendingRentals = $rowRentals['total'];
}

// Truy vấn danh sách lịch thuê gần đây
$sqlRecentRentals = "SELECT Rentals.rentalID, Users.fullName AS customerName, Services.serviceName, Rentals.rentalDate, Rentals.status 
                      FROM Rentals 
                      JOIN Customers ON Rentals.customerID = Customers.customerID
                      JOIN Users ON Customers.customerID = Users.userID
                      JOIN Services ON Rentals.serviceID = Services.serviceID
                      ORDER BY Rentals.rentalDate DESC LIMIT 5"; // Lấy 5 lịch thuê gần nhất
$resultRecentRentals = $conn->query($sqlRecentRentals);
if ($resultRecentRentals && $resultRecentRentals->num_rows > 0) {
    while ($rowRecentRental = $resultRecentRentals->fetch_assoc()) {
        $recentRentals[] = $rowRecentRental;
    }
}

// Đóng kết nối cơ sở dữ liệu
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Trang chủ Admin</title>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Tư vấn Panel</div>
            </a>

            <hr class="sidebar-divider my-0" />

            <li class="nav-item active">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider" />

            <div class="sidebar-heading">Chức năng chính:</div>
            <li class="nav-item">
    <a class="nav-link" href="quanly_khachhangNV.php">
        <i class="fas fa-users"></i>
        <span>Quản lý khách hàng</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="quanly_nhanvienNV.php">
        <i class="fas fa-users"></i>
        <span>Quản lý nhân viên</span>
    </a>
</li>

