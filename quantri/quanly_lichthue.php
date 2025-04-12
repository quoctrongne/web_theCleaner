<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT b.booking_date, b.booking_time, b.address, b.totalAmount, b.status,
                             c.fullName AS customerName, s.name AS serviceName, b.id
                      FROM bookings b
                      LEFT JOIN customers c ON b.customerID = c.customerID
                      LEFT JOIN services s ON b.service_id = s.serviceID
                      ORDER BY b.booking_date DESC, b.booking_time DESC");
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch thuê</title>
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
                    <h2 class="mb-4">Quản lý lịch thuê</h2>
                    <?php if (isset($_GET['msg'])) : ?>
                        <div class="alert alert-success"><?php echo $_GET['msg']; ?></div>
                    <?php endif; ?>
                    <?php if (isset($_GET['err'])) : ?>
                        <div class="alert alert-danger"><?php echo $_GET['err']; ?></div>
                    <?php endif; ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách lịch thuê</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Khách hàng</th>
                                            <th>Dịch vụ</th>
                                            <th>Ngày thuê</th>
                                            <th>Giờ thuê</th>
                                            <th>Địa chỉ</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($bookings)) : ?>
                                            <?php foreach ($bookings as $booking) : ?>
                                                <tr>
                                                    <td><?php echo $booking['customerName'] ?? 'N/A'; ?></td>
                                                    <td><?php echo $booking['serviceName'] ?? 'N/A'; ?></td>
                                                    <td><?php echo $booking['booking_date']; ?></td>
                                                    <td><?php echo $booking['booking_time']; ?></td>
                                                    <td><?php echo $booking['address']; ?></td>
                                                    <td><?php echo number_format($booking['totalAmount'], 2); ?></td>
                                                    <td>
                                                        <form method="POST" action="capnhat_trangthai.php">
                                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                            <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                                                                <option value="pending" <?php if ($booking['status'] === 'pending') echo 'selected'; ?>>Chờ xác nhận</option>
                                                                <option value="confirmed" <?php if ($booking['status'] === 'confirmed') echo 'selected'; ?>>Đã xác nhận</option>
                                                                <option value="completed" <?php if ($booking['status'] === 'completed') echo 'selected'; ?>>Hoàn thành</option>
                                                                <option value="cancelled" <?php if ($booking['status'] === 'cancelled') echo 'selected'; ?>>Đã hủy</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <a href="sua_lichthue.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Sửa</a>
                                                        <a href="xoa_lichthue.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa lịch thuê này không?')"><i class="fas fa-trash-alt"></i> Xóa</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr><td colspan="8" class="text-center">Không có lịch thuê nào.</td></tr>
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