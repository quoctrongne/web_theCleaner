<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền truy cập cho nhân viên kho (WarehouseStaff)
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['WarehouseStaff'])) {
    header("Location: login.php");
    exit();
}

/* ----------------------
   1. Xử lý thêm vật tư mới vào kho (Form Thêm Vật Tư)
---------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_inventory'])) {
    $itemName = $_POST['itemName'];
    $quantity = $_POST['quantity'];

    // Kiểm tra xem vật tư đã có trong kho chưa
    $sqlCheck = "SELECT * FROM inventory WHERE itemName = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $itemName);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Nếu vật tư đã tồn tại, cập nhật bằng cách cộng thêm số lượng mới
        $row = $resultCheck->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;

        $sqlUpdate = "UPDATE inventory SET quantity = ? WHERE itemName = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("is", $newQuantity, $itemName);
        if ($stmtUpdate->execute()) {
            $_SESSION['message'] = "Cập nhật số lượng vật tư '{$itemName}' thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật số lượng vật tư: " . $stmtUpdate->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmtUpdate->close();
    } else {
        // Nếu vật tư chưa tồn tại, thêm mới với ngày nhập kho là CURDATE()
        $sqlInsert = "INSERT INTO inventory (itemName, quantity, inventoryDate) VALUES (?, ?, CURDATE())";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("si", $itemName, $quantity);
        if ($stmtInsert->execute()) {
            $_SESSION['message'] = "Thêm vật tư '{$itemName}' vào kho thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Lỗi khi thêm vật tư: " . $stmtInsert->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmtInsert->close();
    }
    $stmtCheck->close();
    header("Location: inventory_management.php");
    exit();
}

/* ----------------------
   2. Xử lý thêm số lượng bổ sung cho vật tư (phiếu nhập kho gửi Admin)
---------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_additional'])) {
    $itemName = $_POST['itemName'];
    $additional = $_POST['additional'];
    // Trong phiếu nhập kho, số lượng nhập lưu ở trường 'consumed' dưới dạng dương, trạng thái mặc định "Pending"
    $status = "Pending";
    $description = "Phiếu nhập kho: thêm " . $additional . " đơn vị.";

    // Kiểm tra xem vật tư có tồn tại trong kho không
    $sqlCheck = "SELECT inventory_id, quantity FROM inventory WHERE itemName = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $itemName);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        $row = $resultCheck->fetch_assoc();
        $inventoryID = $row['inventory_id'];
        $currentQuantity = $row['quantity'];
        $newQuantity = $currentQuantity + $additional;

        // Cập nhật số lượng mới vào bảng Inventory
        $sqlUpdate = "UPDATE inventory SET quantity = ? WHERE inventory_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ii", $newQuantity, $inventoryID);
        if ($stmtUpdate->execute()) {
            // Insert phiếu nhập kho vào bảng reports
            $sqlInsertReport = "INSERT INTO reports (inventory_id, consumed, remaining, status, report_date, description) VALUES (?, ?, ?, ?, CURDATE(), ?)";
            $stmtInsertReport = $conn->prepare($sqlInsertReport);
            $stmtInsertReport->bind_param("iiiss", $inventoryID, $additional, $newQuantity, $status, $description);
            if ($stmtInsertReport->execute()) {
                $_SESSION['message'] = "Thêm số lượng cho vật tư '{$itemName}' thành công! Phiếu nhập kho đã được gửi đến Admin.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Số lượng bổ sung được cập nhật nhưng xảy ra lỗi khi tạo phiếu nhập kho: " . $stmtInsertReport->error;
                $_SESSION['message_type'] = "danger";
            }
            $stmtInsertReport->close();
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật số lượng: " . $stmtUpdate->error;
            $_SESSION['message_type'] = "danger";
        }
        $stmtUpdate->close();
    } else {
        $_SESSION['message'] = "Không tìm thấy vật tư '{$itemName}' trong kho.";
        $_SESSION['message_type'] = "danger";
    }
    $stmtCheck->close();
    header("Location: inventory_management.php");
    exit();
}

/* ----------------------
   3. Lấy dữ liệu vật tư từ bảng inventory để hiển thị (Thông tin kho)
---------------------- */
$inventoryItems = [];
$sqlInventory = "SELECT itemName, quantity FROM inventory";
$resultInventory = $conn->query($sqlInventory);
if ($resultInventory && $resultInventory->num_rows > 0) {
    while ($row = $resultInventory->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}

/* ----------------------
   4. Lấy dữ liệu phiếu nhập kho đã gửi Admin từ bảng reports
      (chỉ lấy các bản ghi có mô tả bắt đầu với "Phiếu nhập kho:")
---------------------- */
$inputReports = [];
// Nếu lọc theo thời gian được gửi qua GET, áp dụng điều kiện lọc
if (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $filterStart = $_GET['start_date'];
    $filterEnd = $_GET['end_date'];
    $sqlInputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                       FROM reports r
                       JOIN inventory i ON r.inventory_id = i.inventory_id
                       WHERE r.description LIKE 'Phiếu nhập kho:%' AND r.report_date BETWEEN ? AND ?
                       ORDER BY r.report_date DESC";
    $stmtInput = $conn->prepare($sqlInputReport);
    $stmtInput->bind_param("ss", $filterStart, $filterEnd);
    $stmtInput->execute();
    $resultInput = $stmtInput->get_result();
    if ($resultInput && $resultInput->num_rows > 0) {
        while ($row = $resultInput->fetch_assoc()) {
            $inputReports[] = $row;
        }
    }
    $stmtInput->close();
} else {
    $sqlInputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                       FROM reports r
                       JOIN inventory i ON r.inventory_id = i.inventory_id
                       WHERE r.description LIKE 'Phiếu nhập kho:%'
                       ORDER BY r.report_date DESC";
    $resultInputReport = $conn->query($sqlInputReport);
    if ($resultInputReport && $resultInputReport->num_rows > 0) {
        while ($row = $resultInputReport->fetch_assoc()) {
            $inputReports[] = $row;
        }
    }
}

/* ----------------------
   5. Lấy dữ liệu phiếu xuất kho từ bảng reports
      (Các bản ghi không có mô tả bắt đầu với "Phiếu nhập kho:")
---------------------- */
$outputReports = [];
$sqlOutputReport = "SELECT r.report_id, i.itemName, r.consumed, r.remaining, r.report_date, r.status, r.description
                    FROM reports r
                    JOIN inventory i ON r.inventory_id = i.inventory_id
                    WHERE r.description NOT LIKE 'Phiếu nhập kho:%'
                    ORDER BY r.report_date DESC";
$resultOutputReport = $conn->query($sqlOutputReport);
if ($resultOutputReport && $resultOutputReport->num_rows > 0) {
    while ($row = $resultOutputReport->fetch_assoc()) {
        $outputReports[] = $row;
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
    <style>
        .small-form { max-width: 150px; display: inline-block; }
        .table-section { margin-bottom: 30px; }
    </style>
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
            <li class="nav-item">
                <a class="nav-link" href="warehouse_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Bảng điều khiển</span>
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
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý kho vật tư</h2>

                    <!-- Form Thêm Vật Tư Mới -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thêm vật tư vào kho</h6>
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
                                <button type="submit" name="add_inventory" class="btn btn-primary">Thêm vật tư</button>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng hiển thị thông tin kho vật tư -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin kho vật tư</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($inventoryItems)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Tên vật tư</th>
                                                <th>Số lượng hiện có</th>
                                                <th>Thêm số lượng</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inventoryItems as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                    <td>
                                                        <!-- Form thêm số lượng bổ sung cho vật tư -->
                                                        <form method="POST" action="inventory_management.php" class="small-form">
                                                            <input type="hidden" name="itemName" value="<?php echo htmlspecialchars($item['itemName']); ?>">
                                                            <input type="number" name="additional" class="form-control" placeholder="Số lượng thêm" required>
                                                    </td>
                                                    <td>
                                                            <button type="submit" name="update_additional" class="btn btn-sm btn-primary mt-1">Cập nhật</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Không có vật tư nào trong kho.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Form lọc phiếu nhập kho đã gửi Admin -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lọc phiếu nhập kho</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="inventory_report.php">
                                <div class="form-group">
                                    <label for="start_date">Từ ngày:</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Đến ngày:</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Lọc</button>
                            </form>
                        </div>
                    </div>

                    <!-- Bảng hiển thị phiếu nhập kho đã gửi Admin -->
                    <div class="card shadow mb-4 table-section">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Phiếu nhập kho</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($inputReports)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Tên vật tư</th>
                                                <th>Số lượng nhập</th>
                                                <th>Số lượng còn lại</th>
                                                <th>Ngày nhập kho</th>
                                                <th>Trạng thái</th>
                                                <th>Mô tả</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($inputReports as $report): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($report['itemName']); ?></td>
                                                    <td><?php echo htmlspecialchars($report['consumed']); ?></td>
                                                    <td><?php echo htmlspecialchars($report['remaining']); ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($report['report_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($report['status']); ?></td>
                                                    <td><?php echo htmlspecialchars($report['description']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Không có phiếu nhập kho nào được gửi cho Admin.</p>
                            <?php endif; ?>
                        </div>
                    </div>

    

                </div><!-- /.container-fluid -->
            </div><!-- End of Content -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div><!-- End of Content Wrapper -->
    </div><!-- End of Page Wrapper -->

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
