<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['WarehouseStaff'])) {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once("../db/conn.php");

// Xử lý kiểm kê vật tư (cho nhân viên vệ sinh)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cleanerInventory'])) {
    $itemName = $_POST['itemName'];
    $quantity = $_POST['quantity'];

    // Sửa query để insert vào bảng inventory
    $sqlInventory = "INSERT INTO inventory (itemName, quantity, inventoryDate) 
                     VALUES (?, ?, CURDATE())";
    $stmt = $conn->prepare($sqlInventory);
    $stmt->bind_param("si", $itemName, $quantity);

    if ($stmt->execute()) {
        echo "Kiểm kê vật tư thành công!";
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
}

// Lấy dữ liệu báo cáo kho hàng ngày
$dailyReport = [];
$sqlDailyReport = "SELECT itemName, SUM(quantity) AS totalQuantity 
                    FROM inventory 
                    WHERE inventoryDate = CURDATE() 
                    GROUP BY itemName";
$resultDailyReport = $conn->query($sqlDailyReport);
if ($resultDailyReport && $resultDailyReport->num_rows > 0) {
    while ($row = $resultDailyReport->fetch_assoc()) {
        $dailyReport[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Quản lý kho vật tư</title>
</head>
<body id="page-top">
    <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="warehouse_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-warehouse"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kho</div>
    </a>

    <hr class="sidebar-divider my-0" />

    <li class="nav-item active">
        <a class="nav-link" href="warehouse_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider" />


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
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>

                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý kho vật tư</h2>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Kiểm kê vật tư</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Tên vật tư:</label>
                                    <input type="text" name="itemName" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Số lượng:</label>
                                    <input type="number" name="quantity" class="form-control" required>
                                </div>
                                <button type="submit" name="cleanerInventory" class="btn btn-primary">Kiểm kê</button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Báo cáo kho hàng ngày</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tên vật tư</th>
                                            <th>Tổng số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dailyReport as $report): ?>
                                            <tr>
                                                <td><?php echo $report['itemName']; ?></td>
                                                <td><?php echo $report['totalQuantity']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

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
