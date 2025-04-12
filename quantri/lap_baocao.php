<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập cho nhân viên kế toán
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Accountant') {
    header("Location: login.php");
    exit();
}

require_once("../db/conn.php");

// Khởi tạo biến cho dữ liệu báo cáo và thông báo lỗi
$reportData = [];
$errorMessage = "";

// Xử lý khi người dùng bấm nút "Tạo báo cáo"
if (isset($_POST['generate_report'])) {
    $reportType = $_POST['report_type'];
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];

    if (empty($fromDate) || empty($toDate)) {
        $errorMessage = "Vui lòng chọn khoảng thời gian cho báo cáo.";
    } else {
        switch ($reportType) {
            case 'invoice_summary':
                // Báo cáo hóa đơn: thống kê số lượng và tổng tiền theo trạng thái
                $sql = "SELECT status, COUNT(*) AS total, SUM(totalAmount) AS total_amount
                        FROM invoices
                        WHERE issueDate BETWEEN ? AND ?
                        GROUP BY status";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $fromDate, $toDate);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $reportData['invoices'][] = $row;
                    }
                } else {
                    $errorMessage = "Không có dữ liệu hóa đơn trong khoảng thời gian đã chọn.";
                }
                $stmt->close();
                break;

            case 'income_expense_summary':
                // Báo cáo thu chi: tổng hợp các khoản thu/chi từ bảng thuchi
                $sql = "SELECT loai, SUM(soTien) AS total_amount
                        FROM thuchi
                        WHERE ngayGiaoDich BETWEEN ? AND ?
                        GROUP BY loai";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $fromDate, $toDate);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $reportData['income_expense'][] = $row;
                    }
                } else {
                    $errorMessage = "Không có dữ liệu thu chi trong khoảng thời gian đã chọn.";
                }
                $stmt->close();
                break;

            default:
                $errorMessage = "Loại báo cáo không hợp lệ.";
                break;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lập báo cáo - Kế toán</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="ketoan_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-chart-pie"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kế toán Panel</div>
    </a>
    <hr class="sidebar-divider my-0">
    
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link" href="ketoan_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <hr class="sidebar-divider">
    
    <!-- Heading -->
    <div class="sidebar-heading">
        Chức năng chính:
    </div>
    
    <!-- Quản lý hóa đơn -->
    <li class="nav-item">
        <a class="nav-link" href="quanly_hoadon.php">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Quản lý hóa đơn</span>
        </a>
    </li>
    
    <!-- Quản lý thu chi -->
    <li class="nav-item">
        <a class="nav-link" href="quanly_thuchi.php">
            <i class="fas fa-money-bill-wave"></i>
            <span>Quản lý thu chi</span>
        </a>
    </li>
    
    <!-- Lập báo cáo -->
    <li class="nav-item">
        <a class="nav-link" href="lap_baocao.php">
            <i class="fas fa-chart-line"></i>
            <span>Lập báo cáo</span>
        </a>
    </li>
    
    <hr class="sidebar-divider d-none d-md-block">
    
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>


        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Include Topbar của Kế toán -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    
    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php echo $_SESSION['user']['fullName']; ?>
                </span>
                <!-- Thay thế đường dẫn avatar bằng đường dẫn thực tế -->
                <img class="img-profile rounded-circle" src="path/to/your/avatar.png" alt="Avatar">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <a class="dropdown-item" href="profile.php">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Hồ sơ
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Đăng xuất
                </a>
            </div>
        </li>
    </ul>
</nav>


                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Lập báo cáo</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chọn loại báo cáo và khoảng thời gian</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="lap_baocao.php">
                                <div class="form-group">
                                    <label for="report_type">Loại báo cáo:</label>
                                    <select class="form-control" id="report_type" name="report_type">
                                        <option value="invoice_summary">Thống kê hóa đơn theo trạng thái</option>
                                        <option value="income_expense_summary">Tổng hợp thu chi</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="from_date">Từ ngày:</label>
                                    <input type="date" class="form-control" id="from_date" name="from_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="to_date">Đến ngày:</label>
                                    <input type="date" class="form-control" id="to_date" name="to_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="generate_report">Tạo báo cáo</button>
                            </form>

                            <?php if (!empty($errorMessage)): ?>
                                <div class="alert alert-danger mt-3" role="alert">
                                    <?php echo $errorMessage; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($reportData)): ?>
                                <h2 class="mt-4">Kết quả báo cáo</h2>

                                <?php if (isset($reportData['invoices'])): ?>
                                    <h3>Thống kê hóa đơn</h3>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Trạng thái</th>
                                                    <th>Số lượng</th>
                                                    <th>Tổng tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reportData['invoices'] as $row): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                                        <td><?php echo $row['total']; ?></td>
                                                        <td><?php echo number_format($row['total_amount'], 0); ?> VND</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($reportData['income_expense'])): ?>
                                    <h3>Tổng hợp thu chi</h3>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Loại giao dịch</th>
                                                    <th>Tổng tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($reportData['income_expense'] as $row): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['loai']); ?></td>
                                                        <td><?php echo number_format($row['total_amount'], 0); ?> VND</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div> <!-- /.container-fluid -->
            </div> <!-- End of Content -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Bản quyền &copy; Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div> <!-- End of Content Wrapper -->
    </div> <!-- End of Page Wrapper -->

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
