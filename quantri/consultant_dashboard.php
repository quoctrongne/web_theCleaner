<?php
session_start();

// Kiểm tra quyền truy cập cho nhân viên tư vấn (Consultant)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Consultant') {
    header("Location: login.php");
    exit();
}

// Nếu cần, bạn có thể lấy dữ liệu từ CSDL (trong ví dụ này không có truy vấn cụ thể)
require_once("../db/conn.php");
// Ví dụ: nếu cần truy vấn dữ liệu dashboard, thêm code truy vấn ở đây
// ...
// Đóng kết nối nếu không cần dùng nữa
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Nhân Viên Tư Vấn</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Liên kết CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        /* Một số style nếu cần */
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar dành cho nhân viên tư vấn -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
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
            <!-- Sidebar Toggler -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar dành cho nhân viên tư vấn -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Thông tin người dùng -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : "Tư vấn viên"; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown thông tin người dùng -->
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
                <!-- End Topbar -->

                <!-- Nội dung chính -->
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Chào mừng đến với Dashboard Tư Vấn</h1>
                    <p>Ở đây bạn có thể quản lý khách hàng cũng như xem các phản hồi liên quan đến dịch vụ.</p>
                    
                    <!-- Ví dụ: Mục quản lý khách hàng -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Quản lý khách hàng</h6>
                        </div>
                        <div class="card-body">
                            <p>Chức năng quản lý khách hàng sẽ hiển thị thông tin từ hệ thống phản hồi hoặc các câu hỏi FAQ liên quan đến khách hàng.</p>
                            <!-- Bạn có thể thêm bảng hoặc liên kết đến trang chi tiết -->
                        </div>
                    </div>
                </div>
                <!-- End container-fluid -->
            </div>
            <!-- End của Nội dung chính -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>© Your Website <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
