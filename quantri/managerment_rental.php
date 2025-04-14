<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Xử lý cập nhật lịch thuê
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_booking'])) {
    $booking_id = $_POST['booking_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE bookings SET booking_date = ?, booking_time = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $booking_date, $booking_time, $status, $booking_id);
    
    if ($stmt->execute()) {
        $message = "Cập nhật lịch thuê thành công!";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Xử lý xóa lịch thuê
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    
    if ($stmt->execute()) {
        $message = "Xóa lịch thuê thành công!";
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}

// Lấy danh sách lịch thuê
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
                    <?php if (isset($message)) : ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
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
                                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editModal<?php echo $booking['id']; ?>">
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </button>
                                                        <a href="?action=delete&id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa lịch thuê này không?')">
                                                            <i class="fas fa-trash-alt"></i> Xóa
                                                        </a>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal Sửa -->
                                                <div class="modal fade" id="editModal<?php echo $booking['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $booking['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel<?php echo $booking['id']; ?>">Sửa lịch thuê</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form method="POST" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                                    <div class="form-group">
                                                                        <label>Ngày thuê</label>
                                                                        <input type="date" class="form-control" name="booking_date" value="<?php echo $booking['booking_date']; ?>" required>
                                                                    </div>
                    
                                                                    <div class="form-group">
                                                                        <label>Khung giờ</label>
                                                                        <select class="form-control" name="booking_time">
                                                                            <option value="8:00 - 10:00" <?php if ($booking['booking_time'] === '8:00 - 10:00') echo 'selected'; ?>>8:00 - 10:00</option>
                                                                            <option value="10:00 - 12:00" <?php if ($booking['booking_time'] === '10:00 - 12:00') echo 'selected'; ?>>10:00 - 12:00</option>
                                                                            <option value="13:00 - 15:00" <?php if ($booking['booking_time'] === '13:00 - 15:00') echo 'selected'; ?>>13:00 - 15:00</option>
                                                                            <option value="15:00 - 17:00" <?php if ($booking['booking_time'] === '15:00 - 17:00') echo 'selected'; ?>>15:00 - 17:00</option>
                                                                        </select>
                                                                    </div>
                    
                                                                    <div class="form-group">
                                                                        <label>Trạng thái</label>
                                                                        <select class="form-control" name="status">
                                                                            <option value="pending" <?php if ($booking['status'] === 'pending') echo 'selected'; ?>>Chờ xác nhận</option>
                                                                            <option value="confirmed" <?php if ($booking['status'] === 'confirmed') echo 'selected'; ?>>Đã xác nhận</option>
                                                                            <option value="completed" <?php if ($booking['status'] === 'completed') echo 'selected'; ?>>Hoàn thành</option>
                                                                            <option value="cancelled" <?php if ($booking['status'] === 'cancelled') echo 'selected'; ?>>Đã hủy</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                                <button type="submit" name="update_booking" class="btn btn-primary">Lưu thay đổi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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