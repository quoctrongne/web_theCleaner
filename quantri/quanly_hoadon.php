<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập cho nhân viên kế toán
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Accountant') {
    header("Location: login.php");
    exit();
}

require_once("../db/conn.php");

// Xử lý thêm hóa đơn
if (isset($_POST['add_invoice'])) {
    $supplierName = $_POST['supplierName'];
    $issueDate = $_POST['issueDate'];
    $totalAmount = $_POST['totalAmount'];
    $status = $_POST['status'];

    $sqlInsert = "INSERT INTO invoices (supplierName, issueDate, totalAmount, status) VALUES (?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ssds", $supplierName, $issueDate, $totalAmount, $status);

    if ($stmtInsert->execute()) {
        $_SESSION['message'] = "Thêm hóa đơn thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi thêm hóa đơn: " . $stmtInsert->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtInsert->close();
    header("Location: quanly_hoadon.php");
    exit();
}

// Xử lý sửa hóa đơn
if (isset($_POST['edit_invoice'])) {
    $invoiceID = $_POST['invoiceID'];
    $supplierName = $_POST['supplierName'];
    $issueDate = $_POST['issueDate'];
    $totalAmount = $_POST['totalAmount'];
    $status = $_POST['status'];

    $sqlUpdate = "UPDATE invoices SET supplierName = ?, issueDate = ?, totalAmount = ?, status = ? WHERE invoiceID = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssdsi", $supplierName, $issueDate, $totalAmount, $status, $invoiceID);

    if ($stmtUpdate->execute()) {
        $_SESSION['message'] = "Cập nhật hóa đơn thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật hóa đơn: " . $stmtUpdate->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtUpdate->close();
    header("Location: quanly_hoadon.php");
    exit();
}

// Xử lý xóa hóa đơn
if (isset($_GET['delete_id'])) {
    $invoiceID = $_GET['delete_id'];
    $sqlDelete = "DELETE FROM invoices WHERE invoiceID = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $invoiceID);

    if ($stmtDelete->execute()) {
        $_SESSION['message'] = "Xóa hóa đơn thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Lỗi khi xóa hóa đơn: " . $stmtDelete->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmtDelete->close();
    header("Location: quanly_hoadon.php");
    exit();
}

// Lấy danh sách hóa đơn
$sqlSelect = "SELECT * FROM invoices ORDER BY issueDate DESC";
$resultSelect = $conn->query($sqlSelect);
$invoices = [];
if ($resultSelect && $resultSelect->num_rows > 0) {
    while ($row = $resultSelect->fetch_assoc()) {
        $invoices[] = $row;
    }
}

// Lấy thông tin hóa đơn để sửa (nếu có ID được truyền)
$invoiceToEdit = null;
if (isset($_GET['edit_id'])) {
    $editID = $_GET['edit_id'];
    $sqlEdit = "SELECT * FROM invoices WHERE invoiceID = ?";
    $stmtEdit = $conn->prepare($sqlEdit);
    $stmtEdit->bind_param("i", $editID);
    $stmtEdit->execute();
    $resultEdit = $stmtEdit->get_result();
    if ($resultEdit->num_rows == 1) {
        $invoiceToEdit = $resultEdit->fetch_assoc();
    }
    $stmtEdit->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <title>Quản lý hóa đơn</title>
    <style>
        .modal-dialog {
            max-width: 800px;
            margin: 1.75rem auto;
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

    <li class="nav-item">
        <a class="nav-link" href="accountant_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider" />

    <div class="sidebar-heading">Chức năng chính:</div>
    <li class="nav-item active">
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
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['user']['fullName']; ?></span>
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
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

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Bạn có chắc chắn muốn đăng xuất?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Chọn "Đăng xuất" bên dưới nếu bạn đã sẵn sàng kết thúc phiên làm việc hiện tại.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Hủy</button>
                <a class="btn btn-primary" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </div>
</div>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Quản lý hóa đơn</h1>
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                            <?php echo $_SESSION['message']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách hóa đơn</h6>
                            <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#addInvoiceModal"><i class="fas fa-plus"></i> Thêm mới</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Mã HĐ</th>
                                            <th>Nhà cung cấp</th>
                                            <th>Ngày lập</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($invoices as $invoice): ?>
                                            <tr>
                                                <td><?php echo $invoice['invoiceID']; ?></td>
                                                <td><?php echo htmlspecialchars($invoice['supplierName']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['issueDate']); ?></td>
                                                <td><?php echo number_format($invoice['totalAmount']); ?></td>
                                                <td><?php echo htmlspecialchars($invoice['status']); ?></td>
                                                <td>
                                                    <a href="quanly_hoadon.php?edit_id=<?php echo $invoice['invoiceID']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                                                    <a href="quanly_hoadon.php?delete_id=<?php echo $invoice['invoiceID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?')"><i class="fas fa-trash"></i> Xóa</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php require_once('includes/footer.php')?>
        </div>
    </div>

    <div class="modal fade" id="addInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="addInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addInvoiceModalLabel">Thêm hóa đơn mới</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="quanly_hoadon.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="supplierName">Nhà cung cấp:</label>
                            <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                        </div>
                        <div class="form-group">
                            <label for="issueDate">Ngày lập:</label>
                            <input type="date" class="form-control" id="issueDate" name="issueDate" required>
                        </div>
                        <div class="form-group">
                            <label for="totalAmount">Tổng tiền:</label>
                            <input type="number" class="form-control" id="totalAmount" name="totalAmount" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Trạng thái:</label>
                            <select class="form-control" id="status" name="status">
                                <option value="Chưa thanh toán">Chưa thanh toán</option>
                                <option value="Đã thanh toán">Đã thanh toán</option>
                                <option value="Đang xử lý">Đang xử lý</option>
                                <option value="Hủy">Hủy</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" name="add_invoice">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($invoiceToEdit): ?>
        <div class="modal fade show" id="editInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="editInvoiceModalLabel" aria-hidden="true" style="display: block;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInvoiceModalLabel">Sửa hóa đơn</h5>
                        <button type="button" class="close" onclick="window.location.href='quanly_hoadon.php'" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="quanly_hoadon.php">
                        <input type="hidden" name="invoiceID" value="<?php echo $invoiceToEdit['invoiceID']; ?>">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="supplierName">Nhà cung cấp:</label>
                                <input type="text" class="form-control" id="supplierName" name="supplierName" value="<?php echo htmlspecialchars($invoiceToEdit['supplierName']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="issueDate">Ngày lập:</label>
                                <input type="date" class="form-control" id="issueDate" name="issueDate" value="<?php echo htmlspecialchars($invoiceToEdit['issueDate']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="totalAmount">Tổng tiền:</label>
                                <input type="number" class="form-control" id="totalAmount" name="totalAmount" step="0.01" value="<?php echo htmlspecialchars($invoiceToEdit['totalAmount']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Trạng thái:</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="Chưa thanh toán" <?php if ($invoiceToEdit['status'] == 'Chưa thanh toán') echo 'selected'; ?>>Chưa thanh toán</option>
                                    <option value="Đã thanh toán" <?php if ($invoiceToEdit['status'] == 'Đã thanh toán') echo 'selected'; ?>>Đã thanh toán</option>
                                    <option value="Đang xử lý" <?php if ($invoiceToEdit['status'] == 'Đang xử lý') echo 'selected'; ?>>Đang xử lý</option>
                                    <option value="Hủy" <?php if ($invoiceToEdit['status'] == 'Hủy') echo 'selected'; ?>>Hủy</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="window.location.href='quanly_hoadon.php'">Đóng</button>
                            <button type="submit" class="btn btn-primary" name="edit_invoice">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
</body>
</html>