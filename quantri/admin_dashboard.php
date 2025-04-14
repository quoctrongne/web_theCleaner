<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
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
// Sửa thành:
$sqlRentals = "SELECT COUNT(*) AS total FROM bookings WHERE status = 'pending'";

$resultRentals = $conn->query($sqlRentals);
if ($resultRentals && $resultRentals->num_rows > 0) {
    $rowRentals = $resultRentals->fetch_assoc();
    $pendingRentals = $rowRentals['total'];
}

// Truy vấn danh sách lịch thuê gần đây
$sqlRecentRentals = "SELECT b.id, c.fullName AS customerName, s.name AS serviceName, b.booking_date, b.status 
                      FROM bookings b
                      JOIN customers c ON b.customerID = c.customerID
                      JOIN services s ON b.service_id = s.serviceID
                      ORDER BY b.booking_date DESC LIMIT 5";
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
                <div class="sidebar-brand-text mx-3">Admin</div>
            </a>

            <hr class="sidebar-divider my-0" />

            <li class="nav-item active">
                <a class="nav-link" href="admin_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider" />
            <li class="nav-item">
    <a class="nav-link" href="managerment_customer.php">
        <i class="fas fa-users"></i>
        <span>Quản lý khách hàng</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="managerment_employee.php">
        <i class="fas fa-users"></i>
        <span>Quản lý nhân viên</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="managerment_rental.php">
        <i class="fas fa-users"></i>
        <span>Quản lý lịch thuê</span>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="managerment_service.php">
        <i class="fas fa-users"></i>
        <span>Quản lý dịch vụ</span>
    </a>
</li>

            <li class="nav-item">
                <a class="nav-link" href="wacth_revenue.php">
                    <i class="fas fa-chart-area"></i>
                    <span>Xem doanh thu</span></a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="reports.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Thống kê báo cáo</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block" />

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <h2>Tổng quan</h2>
                    <div>
                        <p>Tổng số khách hàng: <?php echo $totalCustomers; ?></p>
                        <p>Tổng số nhân viên: <?php echo $totalEmployees; ?></p>
                        <p>Tổng số lịch thuê đang chờ xử lý: <?php echo $pendingRentals; ?></p>
                    </div>

                    <!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Lịch thuê gần đây</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <h2>Lịch thuê gần đây</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Mã lịch thuê</th>
                                <th>Khách hàng</th>
                                <th>Dịch vụ</th>
                                <th>Ngày thuê</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRentals as $rental): ?>
                                <tr>
                                    <td><?php echo $rental['id']; ?></td>
                                    <td><?php echo $rental['customerName']; ?></td>
                                    <td><?php echo $rental['serviceName']; ?></td>
                                    <td><?php echo $rental['booking_date']; ?></td>
                                    <td><?php echo $rental['status']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>