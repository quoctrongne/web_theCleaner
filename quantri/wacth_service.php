<?php
session_start();
require_once("../db/conn.php");

// Kiểm tra quyền người dùng
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Lấy serviceID từ URL
$serviceID = isset($_GET['id']) ? $_GET['id'] : null;
if ($serviceID === null) {
    echo "Không có ID dịch vụ!";
    exit();
}

// Lấy thông tin dịch vụ từ cơ sở dữ liệu
$sql = "SELECT * FROM services WHERE serviceID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $serviceID);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();

if (!$service) {
    echo "Không tìm thấy dịch vụ!";
    exit();
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin dịch vụ</title>
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
                    <h2 class="mb-4">Thông tin dịch vụ</h2>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Chi tiết dịch vụ</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Tên dịch vụ:</h5>
                                    <p><?php echo htmlspecialchars($service['name']); ?></p>

                                    <h5>Mã dịch vụ:</h5>
                                    <p><?php echo htmlspecialchars($service['service_code']); ?></p>

                                    <h5>Giới thiệu dịch vụ:</h5>
                                    <p><?php echo htmlspecialchars($service['description']); ?></p>

                                    <h5>Biểu tượng:</h5>
                                    <p><i class="<?php echo htmlspecialchars($service['icon']); ?>"></i></p>

                                    <h5>Giá dịch vụ:</h5>
                                    <p><?php echo number_format($service['price'], 0, ',', '.'); ?> VND</p>
                                    
                                    <h5>Trạng thái:</h5>
                                    <p><?php echo $service['is_active'] == 1 ? 'Hoạt động' : 'Không hoạt động'; ?></p>
                                    
                                    <h5>Ngày tạo:</h5>
                                    <p><?php echo $service['created_at']; ?></p>

                                    <h5>Ngày cập nhật:</h5>
                                    <p><?php echo $service['updated_at']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Ảnh minh họa:</h5>
                                    <p><img src="images/service-icons/<?php echo $service['icon']; ?>.png" alt="<?php echo $service['name']; ?>" class="img-fluid"></p>
                                </div>
                            </div>
                            <a href="managerment_service.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại danh sách dịch vụ</a>
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
