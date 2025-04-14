<?php
session_start();

// Kiểm tra quyền truy cập cho nhân viên tư vấn (Consultant)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Consultant') {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
require_once("../db/conn.php");

// Xử lý nếu có yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $faq_id = $_POST['faq_id'];
    $answer = $_POST['answer'];
    $customer_email = $_POST['customer_email'];

    // Cập nhật câu trả lời và email vào cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE faqs SET answer = ?, customer_email = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $answer, $customer_email, $faq_id);
    
    if ($stmt->execute()) {
        // Gửi email trả lời cho khách hàng
        // (Thêm mã gửi email ở đây nếu cần)
        echo "Câu trả lời đã được gửi và lưu thành công!";
    } else {
        echo "Lỗi khi lưu câu trả lời: " . $stmt->error;
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
    exit(); // Dừng thực thi thêm
}

// Truy vấn cơ sở dữ liệu để lấy danh sách các FAQ
$sql = "SELECT id, question, answer, display_order, is_active, created_at, updated_at, customer_email 
        FROM faqs 
        WHERE is_active = 1 
        ORDER BY display_order ASC, id ASC";

try {
    // Thực thi truy vấn
    $result = $conn->query($sql);

    // Khởi tạo mảng chứa các FAQs
    $faqs = [];

    // Kiểm tra nếu có kết quả trả về
    if ($result && $result->num_rows > 0) {
        // Lặp qua tất cả các hàng và lưu vào mảng
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
    } else {
        // Nếu không có dữ liệu trả về
        echo "Không có FAQ nào.";
    }
} catch (Exception $e) {
    // Xử lý lỗi nếu có
    echo "Lỗi: " . $e->getMessage();
}

// Đảm bảo đóng kết nối sau khi truy vấn
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="consultant_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Tư Vấn</div>
            </a>
            <hr class="sidebar-divider my-0">
            <!-- Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="consultant_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Bảng Điều Khiển</span>
                </a>
            </li>
            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="customer_employee_management.php">
                    <i class="fas fa-users"></i>
                    <span>Quản Lý Khách Hàng</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : "Tư vấn viên"; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Trang cá nhân
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Đăng xuất
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Nội dung chính -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Danh sách FAQ</h1>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách FAQ</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($faqs)): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Câu hỏi</th>
                                                <th>Trả lời</th>
                                                <th>Thứ tự hiển thị</th>
                                                <th>Kích hoạt</th>
                                                <th>Ngày tạo</th>
                                                <th>Ngày cập nhật</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($faqs as $faq): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($faq['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($faq['question']); ?></td>
                                                    <td><?php echo htmlspecialchars($faq['answer']); ?></td>
                                                    <td><?php echo htmlspecialchars($faq['display_order']); ?></td>
                                                    <td><?php echo ($faq['is_active'] == 1) ? 'Có' : 'Không'; ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($faq['created_at'])); ?></td>
                                                    <td><?php echo date("d/m/Y", strtotime($faq['updated_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#answerModal<?php echo $faq['id']; ?>">Trả lời</button>

                                                        <div class="modal fade" id="answerModal<?php echo $faq['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="answerModalLabel<?php echo $faq['id']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="answerModalLabel<?php echo $faq['id']; ?>">Trả lời câu hỏi</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form method="POST" action="reply_faq.php">
                                                                            <div class="form-group">
                                                                                <label for="answer">Trả lời câu hỏi:</label>
                                                                                <textarea class="form-control" id="answer" name="answer" rows="4" required></textarea>
                                                                            </div>
                                                                            <input type="hidden" name="faq_id" value="<?php echo $faq['id']; ?>">
                                                                            <input type="hidden" name="customer_email" value="<?php echo $faq['customer_email']; ?>">
                                                                            <button type="submit" class="btn btn-primary">Gửi trả lời</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center">Không có FAQ nào.</p>
                            <?php endif; ?>
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