<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập cho nhân viên kế toán
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Accountant') {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once("../db/conn.php");

// Lấy thông tin thống kê cho kế toán (ví dụ: hóa đơn)
$totalInvoices = 0;
$pendingInvoices = 0;
$paidInvoices = 0;
$recentInvoices = [];

// Truy vấn tổng số hóa đơn
$sqlTotalInvoices = "SELECT COUNT(*) AS total FROM Invoices"; // Thay 'Invoices' bằng tên bảng hóa đơn của bạn
$resultTotalInvoices = $conn->query($sqlTotalInvoices);
if ($resultTotalInvoices && $resultTotalInvoices->num_rows > 0) {
    $rowTotalInvoices = $resultTotalInvoices->fetch_assoc();
    $totalInvoices = $rowTotalInvoices['total'];
}

// Truy vấn số lượng hóa đơn chưa thanh toán
$sqlPendingInvoices = "SELECT COUNT(*) AS total FROM Invoices WHERE status = 'Chưa thanh toán'"; // Thay 'Invoices' và điều kiện trạng thái của bạn
$resultPendingInvoices = $conn->query($sqlPendingInvoices);
if ($resultPendingInvoices && $resultPendingInvoices->num_rows > 0) {
    $rowPendingInvoices = $resultPendingInvoices->fetch_assoc();
    $pendingInvoices = $rowPendingInvoices['total'];
}

// Truy vấn số lượng hóa đơn đã thanh toán
$sqlPaidInvoices = "SELECT COUNT(*) AS total FROM Invoices WHERE status = 'Đã thanh toán'"; // Thay 'Invoices' và điều kiện trạng thái của bạn
$resultPaidInvoices = $conn->query($sqlPaidInvoices);
if ($resultPaidInvoices && $resultPaidInvoices->num_rows > 0) {
    $rowPaidInvoices = $resultPaidInvoices->fetch_assoc();
    $paidInvoices = $rowPaidInvoices['total'];
}

// Truy vấn danh sách hóa đơn gần đây
$sqlRecentInvoices = "SELECT invoiceID, supplierName, issueDate, totalAmount, status 
                       FROM Invoices 
                       ORDER BY issueDate DESC LIMIT 5"; // Thay 'Invoices' và các cột của bạn
$resultRecentInvoices = $conn->query($sqlRecentInvoices);
if ($resultRecentInvoices && $resultRecentInvoices->num_rows > 0) {
    while ($rowRecentInvoice = $resultRecentInvoices->fetch_assoc()) {
        $recentInvoices[] = $rowRecentInvoice;
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
    <title>Trang chủ Kế toán</title>
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
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="ketoan_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kế toán Panel</div>
            </a>

            <hr class="sidebar-divider my-0" />

            <li class="nav-item active">
                <a class="nav-link" href="ketoan_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider" />

            <div class="sidebar-heading">Chức năng chính:</div>
            <li class="nav-item">
                <a class="nav-link" href="quanly_hoadon.php">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Quản lý hóa đơn</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="quanly_thuchi.php">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Quản lý thu chi</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="lap_baocao.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Lập báo cáo</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block" />

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <h2>Tổng quan Kế toán</h2>
                    <div>
                        <p>Tổng số hóa đơn: <?php echo $totalInvoices; ?></p>
                        <p>Số hóa đơn chưa thanh toán: <?php echo $pendingInvoices; ?></p>
                        <p>Số hóa đơn đã thanh toán: <?php echo $paidInvoices; ?></p>
                    </div>

                    <h2>Hóa đơn gần đây</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Mã hóa đơn</th>
                                <th>Nhà cung cấp</th>
                                <th>Ngày lập</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentInvoices as $invoice): ?>
                                <tr>
                                    <td><?php echo $invoice['invoiceID']; ?></td>
                                    <td><?php echo $invoice['supplierName']; ?></td>
                                    <td><?php echo $invoice['issueDate']; ?></td>
                                    <td><?php echo number_format($invoice['totalAmount']); ?></td>
                                    <td><?php echo $invoice['status']; ?></td>
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