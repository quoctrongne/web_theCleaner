<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập cho nhân viên kho (WarehouseStaff)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'WarehouseStaff') {
    header("Location: login.php");
    exit();
}

// Xử lý thêm vật tư vệ sinh (Insert vào bảng Inventory)
if (isset($_POST['add_inventory'])) {
    $itemName = $_POST['itemName'];
    $quantity = $_POST['quantity'];

    // Lệnh INSERT vào bảng Inventory với các cột itemName và quantity
    $sqlInsert = "INSERT INTO Inventory (itemName, quantity) VALUES (?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("si", $itemName, $quantity);
    
    if ($stmtInsert->execute()) {
        $_SESSION['message'] = "Thêm vật tư vệ sinh thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm vật tư: " . $stmtInsert->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtInsert->close();
    header("Location: inventory_management.php");
    exit();
}

// Lấy dữ liệu thống kê kho
$inventoryItems = [];
$sqlInventory = "SELECT itemName, SUM(quantity) AS totalQuantity FROM Inventory GROUP BY itemName";
$resultInventory = $conn->query($sqlInventory);
if ($resultInventory && $resultInventory->num_rows > 0) {
    while ($row = $resultInventory->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý kho vật tư</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar của kho (bạn có thể chỉnh sửa lại nếu cần) -->
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
        <!-- End of Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar (có thể giữ nguyên của Admin hoặc chỉnh sửa lại cho kho) -->
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý kho vật tư</h2>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>
                    
                    <!-- Nút mở modal thêm vật tư vệ sinh -->
                    <button class="btn btn-success mb-4" data-toggle="modal" data-target="#addInventoryModal">
                        <i class="fas fa-plus"></i> Thêm vật tư vệ sinh
                    </button>

                    <!-- Bảng hiển thị danh sách vật tư -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thống kê kho</h6>
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
                                        <?php foreach ($inventoryItems as $item): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                                                <td><?php echo htmlspecialchars($item['totalQuantity']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>  <!-- /.container-fluid -->
            </div>  <!-- End of Content -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Bản quyền &copy; Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>  <!-- End of Content Wrapper -->
    </div>  <!-- End of Page Wrapper -->

    <!-- Modal thêm vật tư vệ sinh -->
    <div class="modal fade" id="addInventoryModal" tabindex="-1" role="dialog" aria-labelledby="addInventoryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInventoryModalLabel">Thêm vật tư vệ sinh</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="inventory_management.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="itemName">Tên vật tư:</label>
                            <input type="text" class="form-control" id="itemName" name="itemName" placeholder="Nhập tên vật tư" required>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Số lượng:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Nhập số lượng" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-success" name="add_inventory">Thêm vật tư</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Các file JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
