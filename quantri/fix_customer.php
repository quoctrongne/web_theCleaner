<?php
session_start();

// Kiểm tra đăng nhập và quyền truy cập
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin'])) {
    header("Location: login.php");
    exit();
}

// Kết nối database
require_once("../db/conn.php");

// Lấy customerID từ URL
$customerID = isset($_GET['id']) ? $_GET['id'] : null;
if ($customerID === null) {
    echo "Không có ID khách hàng!";
    exit();
}

// Lấy thông tin khách hàng từ cơ sở dữ liệu
$sql = "SELECT * FROM customers WHERE customerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo "Không tìm thấy khách hàng!";
    exit();
}

// Cập nhật thông tin khách hàng khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Cập nhật thông tin khách hàng trong cơ sở dữ liệu
    $updateSql = "UPDATE customers SET fullName = ?, email = ?, phone = ?, address = ? WHERE customerID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssi", $fullName, $email, $phone, $address, $customerID);

    if ($updateStmt->execute()) {
        // Cập nhật thành công, chuyển hướng về trang danh sách khách hàng
        header("Location: managerment_customer.php?message=Cập nhật khách hàng thành công");
        exit();
    } else {
        echo "Cập nhật thất bại!";
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
    <title>Sửa khách hàng</title>
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
                    <h2 class="mb-4">Sửa thông tin khách hàng</h2>

                    <!-- Hiển thị thông báo nếu có -->
                    <?php if (isset($_GET['message'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_GET['message']; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="fullName">Họ và tên</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($customer['fullName']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                        <a href="managerment_customer.php" class="btn btn-secondary">Quay lại</a>
                    </form>
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
