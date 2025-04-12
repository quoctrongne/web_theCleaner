<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'WarehouseStaff') {
    header("Location: login.php");
    exit();
}

require_once("../db/conn.php");

// Xử lý kiểm kê vật tư
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inventory'])) {
    $itemName = $_POST['itemName'];
    $quantity = $_POST['quantity'];

    $sqlInventory = "INSERT INTO Inventory (itemName, quantity, inventoryDate) VALUES ('$itemName', $quantity, NOW())";
    if ($conn->query($sqlInventory) !== TRUE) {
        echo "Lỗi: " . $sqlInventory . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Kiểm kê vật tư</title>
</head>
<body id="page-top">
    <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="warehouse_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-warehouse"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Kho Panel</div>
    </a>

    <hr class="sidebar-divider my-0" />

    <li class="nav-item active">
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
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Kiểm kê vật tư</h2>
                    <div class="card shadow mb-4">
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
                                <button type="submit" name="inventory" class="btn btn-primary">Kiểm kê</button>
                            </form>
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