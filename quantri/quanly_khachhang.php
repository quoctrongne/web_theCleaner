<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['admin']) || !in_array($_SESSION['admin']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

// Kết nối database
require_once("../db/conn.php");

// Xử lý tìm kiếm
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$sqlSearch = '';
if (!empty($searchKeyword)) {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE fullName LIKE ? OR email LIKE ?");
    $searchParam = "%$searchKeyword%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

// Lấy danh sách khách hàng
$sql = "SELECT customerID, fullName, email, phone, address, registrationDate
        FROM customers " . $sqlSearch;
$result = $conn->query($sql);
$customers = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý khách hàng</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <?php require("includes/sidebar.php"); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
            <?php require("includes/topbar.php"); ?>

                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý khách hàng</h2>

                    <form method="GET" class="mb-3 d-flex">
                        <input type="text" name="search" class="form-control w-25 me-2" placeholder="Tìm kiếm theo tên hoặc email" value="<?php echo $searchKeyword; ?>">
                        <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Tìm kiếm</button>
                    </form>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách khách hàng</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Mã KH</th>
                                            <th>Họ và Tên</th>
                                            <th>Email</th>
                                            <th>Điện thoại</th>
                                            <th>Địa chỉ</th>
                                            <th>Ngày đăng ký</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($customers)) : ?>
                                            <?php foreach ($customers as $customer) : ?>
                                                <tr>
                                                    <td><?php echo $customer['customerID']; ?></td>
                                                    <td><?php echo $customer['fullName']; ?></td>
                                                    <td><?php echo $customer['email']; ?></td>
                                                    <td><?php echo $customer['phone']; ?></td>
                                                    <td><?php echo $customer['address']; ?></td>
                                                    <td><?php echo $customer['registrationDate']; ?></td>
                                                    <td>
                                                        <a href="sua_khachhang.php?id=<?php echo $customer['customerID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                        <a href="xoa_khachhang.php?id=<?php echo $customer['customerID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này không?')">
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="11" class="text-center">Không có khách hàng nào</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- /.container-fluid -->
            </div> <!-- /.content -->
        </div> <!-- /.content-wrapper -->
    </div> <!-- /.wrapper -->

    <!-- Script -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
