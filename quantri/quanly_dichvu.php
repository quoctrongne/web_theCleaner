<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Cập nhật câu truy vấn SQL để chỉ lấy 2 dịch vụ
$sql = "SELECT serviceID, name AS serviceName, price FROM services LIMIT 2";  // Đổi từ `serviceName` thành `name`
$result = $conn->query($sql);
$services = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dịch vụ</title>
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
                    <h2 class="mb-4">Quản lý dịch vụ</h2>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách dịch vụ</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên dịch vụ</th>
                                            <th>Giá</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($services)) : ?>
                                            <?php foreach ($services as $service) : ?>
                                                <tr>
                                                    <td><?php echo $service['serviceID']; ?></td>
                                                    <td><?php echo $service['serviceName']; ?></td>
                                                    <td><?php echo number_format($service['price'], 0, ',', '.'); ?> </td>
                                                    <td>
                                                        <a href="xem_dichvu.php?id=<?php echo $service['serviceID']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Thông tin dịch vụ</a>
                                                        <a href="xoa_dichvu.php?id=<?php echo $service['serviceID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này không?')">
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="6" class="text-center">Không có dịch vụ nào</td>
                                            </tr>
                                        <?php endif; ?>
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
