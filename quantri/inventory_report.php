<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra đăng nhập và quyền truy cập cho nhân viên kho (WarehouseStaff)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'WarehouseStaff') {
    header("Location: login.php");
    exit();
}

// Xử lý cập nhật tiêu thụ vật tư (gửi từ form trong bảng)
if (isset($_POST['update_consumption'])) {
    // Lấy tên vật tư và số lượng tiêu thụ nhập vào
    $itemName = $_POST['itemName'];
    $consumed = $_POST['consumed'];
    // Ép số tiêu thụ về số dương rồi lưu dưới dạng số âm (để trừ đi số lượng tồn)
    $consumed = -abs($consumed);
    
    // Insert bản ghi tiêu thụ (số âm) với ngày giao dịch là hôm nay
    $sqlInsert = "INSERT INTO Inventory (itemName, quantity, inventoryDate) VALUES (?, ?, CURDATE())";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param("si", $itemName, $consumed);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Cập nhật tiêu thụ cho vật tư '{$itemName}' thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật tiêu thụ: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
    header("Location: inventory_report.php");
    exit();
}

// Lấy dữ liệu báo cáo kho hôm nay: Tổng số lượng của từng vật tư (bao gồm nhập và tiêu thụ)
$reportItems = [];
$sqlReport = "SELECT itemName, SUM(quantity) AS dailyQuantity 
              FROM Inventory 
              WHERE DATE(inventoryDate) = CURDATE() 
              GROUP BY itemName";
$resultReport = $conn->query($sqlReport);
if ($resultReport && $resultReport->num_rows > 0) {
    while ($row = $resultReport->fetch_assoc()) {
        $reportItems[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo vật tư - Kiểm kê hôm nay</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        /* Class để giới hạn kích thước form nhập trong bảng */
        .small-form { max-width: 150px; display: inline-block; }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar của kho -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="warehouse_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Kho Panel</div>
            </a>
            <hr class="sidebar-divider my-0" />
            <li class="nav-item">
                <a class="nav-link" href="warehouse_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider" />
            <div class="sidebar-heading">Chức năng chính:</div>
            <li class="nav-item">
                <a class="nav-link" href="inventory_check.php">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Kiểm kê vật tư</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventory_management.php">
                    <i class="fas fa-boxes"></i>
                    <span>Quản lý kho</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventory_report.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Báo cáo kho</span>
                </a>
            </li>
        </ul>
        <!-- End Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <?php require("includes/topbar.php"); ?>
                <!-- End Topbar -->

                <div class="container-fluid">
                    <h2 class="mb-4">Báo cáo vật tư (Hôm nay)</h2>
                    
                    <!-- Hiển thị thông báo nếu có -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <!-- Bảng báo cáo vật tư -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Kiểm kê và cập nhật tiêu thụ</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tên vật tư</th>
                                            <th>Tổng số lượng</th>
                                            <th>Tiêu thụ hôm nay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($reportItems)): ?>
                                            <?php foreach ($reportItems as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['dailyQuantity']); ?></td>
                                                    <td>
                                                        <!-- Form nhập số lượng tiêu thụ cho vật tư này -->
                                                        <form method="POST" action="inventory_report.php" class="small-form">
                                                            <input type="hidden" name="itemName" value="<?php echo htmlspecialchars($item['itemName']); ?>">
                                                            <input type="number" name="consumed" class="form-control" placeholder="SL tiêu thụ" required>
                                                            <button type="submit" name="update_consumption" class="btn btn-sm btn-primary mt-1">Cập nhật</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Không có dữ liệu kiểm kê hôm nay.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-3 text-muted">Lưu ý: Số lượng tiêu thụ bạn nhập là số dương, hệ thống sẽ tự chuyển đổi thành số âm để ghi nhận tiêu thụ.</p>
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
