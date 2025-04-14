<?php
session_start();

// Kiểm tra quyền truy cập cho nhân viên kho (WarehouseStaff)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'WarehouseStaff') {
    header("Location: login.php");
    exit();
}

require_once("../db/conn.php");

// Biến để lưu thông tin số lượng vật tư
$inventoryInfo = [];

// Xử lý khi tìm kiếm vật tư
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_inventory'])) {
    $itemName = $_POST['itemName'];

    // Kiểm tra vật tư trong kho
    $sqlCheck = "SELECT itemName, SUM(quantity) AS totalQuantity FROM Inventory WHERE itemName = ? GROUP BY itemName";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $itemName);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    // Nếu vật tư tồn tại trong kho
    if ($resultCheck->num_rows > 0) {
        $row = $resultCheck->fetch_assoc();
        $inventoryInfo = [
            'itemName' => $row['itemName'],
            'totalQuantity' => $row['totalQuantity']
        ];
    } else {
        $inventoryInfo = [
            'itemName' => $itemName,
            'totalQuantity' => 0
        ];
    }

    $stmtCheck->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kiểm tra vật tư</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
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
                    <span>Kiểm tra vật tư</span>
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

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>

                <div class="container-fluid">
                    <h2 class="mb-4">Kiểm tra số lượng vật tư</h2>

                    <!-- Hiển thị thông báo nếu có -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <!-- Form kiểm tra vật tư -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Kiểm tra vật tư trong kho</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Tên vật tư:</label>
                                    <input type="text" name="itemName" class="form-control" required>
                                </div>
                                <button type="submit" name="check_inventory" class="btn btn-primary">Kiểm tra</button>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng hiển thị thông tin tồn kho -->
                    <?php if (!empty($inventoryInfo)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin vật tư</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Tên vật tư:</strong> <?php echo htmlspecialchars($inventoryInfo['itemName']); ?></p>
                            <p><strong>Số lượng tồn kho:</strong> <?php echo htmlspecialchars($inventoryInfo['totalQuantity']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>  <!-- /.container-fluid -->
            </div>  <!-- End of Content -->
        </div>  <!-- End of Content Wrapper -->
    </div>  <!-- End of Page Wrapper -->

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
