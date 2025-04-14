<?php
session_start();
require_once("../db/conn.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Xử lý cập nhật giá dịch vụ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_pricing'])) {
        $pricing_id = $_POST['pricing_id'];
        $base_price = $_POST['base_price'];
        
        // Cập nhật giá dịch vụ
        $sql = "UPDATE service_pricing SET base_price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("di", $base_price, $pricing_id);
        
        if ($stmt->execute()) {
            $success_message = "Cập nhật giá dịch vụ thành công!";
        } else {
            $error_message = "Lỗi khi cập nhật giá dịch vụ: " . $conn->error;
        }
    } elseif (isset($_POST['reset_default'])) {
        // Giá mặc định cho vệ sinh nhà ở
        $default_home_prices = [
            1 => 20000.00,  // Dưới 50m²
            2 => 16000.00,  // 50m² - 100m²
            3 => 14000.00   // Trên 100m²
        ];
        
        // Giá mặc định cho vệ sinh văn phòng
        $default_office_prices = [
            4 => 25000.00,  // Dưới 100m²
            5 => 22000.00,  // 100m² - 300m²
            6 => 20000.00   // Trên 300m²
        ];
        
        // Cập nhật về giá mặc định
        $sql = "UPDATE service_pricing SET base_price = CASE ";
        
        foreach ($default_home_prices as $id => $price) {
            $sql .= "WHEN id = $id THEN $price ";
        }
        
        foreach ($default_office_prices as $id => $price) {
            $sql .= "WHEN id = $id THEN $price ";
        }
        
        $sql .= "ELSE base_price END";
        
        if ($conn->query($sql)) {
            $success_message = "Đã đặt lại tất cả về giá mặc định!";
        } else {
            $error_message = "Lỗi khi đặt lại giá mặc định: " . $conn->error;
        }
    } elseif (isset($_POST['update_all'])) {
        // Xử lý cập nhật tất cả giá
        $percentage = $_POST['percentage'];
        $operation = $_POST['operation'];
        
        if ($operation == 'increase') {
            $sql = "UPDATE service_pricing SET base_price = base_price * (1 + $percentage/100)";
        } else {
            $sql = "UPDATE service_pricing SET base_price = base_price * (1 - $percentage/100)";
        }
        
        if ($conn->query($sql)) {
            $success_message = "Đã " . ($operation == 'increase' ? "tăng" : "giảm") . " tất cả giá lên $percentage%!";
        } else {
            $error_message = "Lỗi khi cập nhật tất cả giá: " . $conn->error;
        }
    }
}

// Lấy thông tin dịch vụ vệ sinh nhà ở (service_id = 1)
$sql_home = "SELECT sp.id, sp.min_area, sp.max_area, sp.base_price, s.name, s.serviceID 
             FROM service_pricing sp
             JOIN services s ON sp.service_id = s.serviceID
             WHERE s.service_code = 'home'
             ORDER BY sp.min_area ASC";
$result_home = $conn->query($sql_home);
$home_pricing = [];
if ($result_home && $result_home->num_rows > 0) {
    while ($row = $result_home->fetch_assoc()) {
        $home_pricing[] = $row;
    }
}

// Lấy thông tin dịch vụ vệ sinh văn phòng (service_id = 2)
$sql_office = "SELECT sp.id, sp.min_area, sp.max_area, sp.base_price, s.name, s.serviceID 
               FROM service_pricing sp
               JOIN services s ON sp.service_id = s.serviceID
               WHERE s.service_code = 'office'
               ORDER BY sp.min_area ASC";
$result_office = $conn->query($sql_office);
$office_pricing = [];
if ($result_office && $result_office->num_rows > 0) {
    while ($row = $result_office->fetch_assoc()) {
        $office_pricing[] = $row;
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
    <style>
        .pricing-card {
            margin-bottom: 30px;
        }
        .pricing-card .card-header {
            font-weight: bold;
        }
        .edit-price-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .area-range {
            font-weight: normal;
            font-size: 0.9rem;
        }
        .price-actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .price-cell {
            font-weight: bold;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <?php require("includes/sidebar.php"); ?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php require("includes/topbar.php"); ?>
                <div class="container-fluid">
                    <h2 class="mb-4">Quản lý dịch vụ</h2>
                    
                    <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Các nút tác vụ nhanh -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Tác vụ nhanh</h6>
                        </div>
                        <div class="card-body">
                            <div class="price-actions">
                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#updateAllModal">
                                    <i class="fas fa-percentage"></i> Cập nhật tất cả giá
                                </button>
                                
                                <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn đặt lại tất cả về giá mặc định?');">
                                    <input type="hidden" name="reset_default" value="1">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> Đặt lại về giá mặc định
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bảng giá vệ sinh nhà ở -->
                    <div class="card shadow pricing-card">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Bảng giá dịch vụ vệ sinh nhà ở</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Diện tích</th>
                                            <th>Giá (VNĐ/m²)</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($home_pricing)) : ?>
                                            <?php foreach ($home_pricing as $pricing) : ?>
                                                <tr>
                                                    <td>
                                                        <span class="area-range">
                                                            <?php if ($pricing['min_area'] == 0): ?>
                                                                Dưới <?php echo $pricing['max_area']; ?>m²
                                                            <?php elseif ($pricing['max_area'] === null): ?>
                                                                Trên <?php echo $pricing['min_area']; ?>m²
                                                            <?php else: ?>
                                                                <?php echo $pricing['min_area']; ?>m² - <?php echo $pricing['max_area']; ?>m²
                                                            <?php endif; ?>
                                                        </span>
                                                    </td>
                                                    <td class="price-cell">
                                                        <?php echo number_format($pricing['base_price'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary edit-price-btn" 
                                                                data-toggle="modal" 
                                                                data-target="#editPriceModal" 
                                                                data-id="<?php echo $pricing['id']; ?>"
                                                                data-price="<?php echo $pricing['base_price']; ?>"
                                                                data-area="<?php 
                                                                    if ($pricing['min_area'] == 0): 
                                                                        echo 'Dưới ' . $pricing['max_area'] . 'm²';
                                                                    elseif ($pricing['max_area'] === null): 
                                                                        echo 'Trên ' . $pricing['min_area'] . 'm²';
                                                                    else: 
                                                                        echo $pricing['min_area'] . 'm² - ' . $pricing['max_area'] . 'm²';
                                                                    endif; 
                                                                ?>">
                                                            <i class="fas fa-edit"></i> Sửa giá
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Không có dữ liệu bảng giá</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bảng giá vệ sinh văn phòng -->
                    <div class="card shadow pricing-card">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Bảng giá dịch vụ vệ sinh văn phòng</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Diện tích</th>
                                            <th>Giá (VNĐ/m²)</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($office_pricing)) : ?>
                                            <?php foreach ($office_pricing as $pricing) : ?>
                                                <tr>
                                                    <td>
                                                        <span class="area-range">
                                                            <?php if ($pricing['min_area'] == 0): ?>
                                                                Dưới <?php echo $pricing['max_area']; ?>m²
                                                            <?php elseif ($pricing['max_area'] === null): ?>
                                                                Trên <?php echo $pricing['min_area']; ?>m²
                                                            <?php else: ?>
                                                                <?php echo $pricing['min_area']; ?>m² - <?php echo $pricing['max_area']; ?>m²
                                                            <?php endif; ?>
                                                        </span>
                                                    </td>
                                                    <td class="price-cell">
                                                        <?php echo number_format($pricing['base_price'], 0, ',', '.'); ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary edit-price-btn" 
                                                                data-toggle="modal" 
                                                                data-target="#editPriceModal" 
                                                                data-id="<?php echo $pricing['id']; ?>"
                                                                data-price="<?php echo $pricing['base_price']; ?>"
                                                                data-area="<?php 
                                                                    if ($pricing['min_area'] == 0): 
                                                                        echo 'Dưới ' . $pricing['max_area'] . 'm²';
                                                                    elseif ($pricing['max_area'] === null): 
                                                                        echo 'Trên ' . $pricing['min_area'] . 'm²';
                                                                    else: 
                                                                        echo $pricing['min_area'] . 'm² - ' . $pricing['max_area'] . 'm²';
                                                                    endif; 
                                                                ?>">
                                                            <i class="fas fa-edit"></i> Sửa giá
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Không có dữ liệu bảng giá</td>
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
    
    <!-- Modal chỉnh sửa giá -->
    <div class="modal fade" id="editPriceModal" tabindex="-1" role="dialog" aria-labelledby="editPriceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPriceModalLabel">Chỉnh sửa giá dịch vụ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="area_range">Phạm vi diện tích:</label>
                            <input type="text" class="form-control" id="area_range" readonly>
                        </div>
                        <div class="form-group">
                            <label for="base_price">Giá dịch vụ (VNĐ/m²):</label>
                            <input type="number" class="form-control" id="base_price" name="base_price" min="0" required>
                        </div>
                        <input type="hidden" id="pricing_id" name="pricing_id">
                        <input type="hidden" name="update_pricing" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal cập nhật tất cả giá -->
    <div class="modal fade" id="updateAllModal" tabindex="-1" role="dialog" aria-labelledby="updateAllModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateAllModalLabel">Cập nhật tất cả giá</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="" onsubmit="return confirm('Bạn có chắc chắn muốn cập nhật tất cả giá?');">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="operation">Thao tác:</label>
                            <select class="form-control" id="operation" name="operation" required>
                                <option value="increase">Tăng giá</option>
                                <option value="decrease">Giảm giá</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="percentage">Tỷ lệ phần trăm (%):</label>
                            <input type="number" class="form-control" id="percentage" name="percentage" min="1" max="100" value="5" required>
                            <small class="form-text text-muted">Ví dụ: Nhập 5 để tăng/giảm 5% giá hiện tại.</small>
                        </div>
                        <input type="hidden" name="update_all" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Khi modal mở, điền dữ liệu vào form
            $('#editPriceModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var price = button.data('price');
                var area = button.data('area');
                
                var modal = $(this);
                modal.find('#pricing_id').val(id);
                modal.find('#base_price').val(price);
                modal.find('#area_range').val(area);
                modal.find('#editPriceModalLabel').text('Chỉnh sửa giá dịch vụ: ' + area);
            });
            
            // Format input giá tiền khi nhập
            $('#base_price').on('input', function() {
                var value = $(this).val();
                if (value.length > 0) {
                    value = value.replace(/[^\d]/g, '');
                    $(this).val(value);
                }
            });
        });
    </script>
</body>
</html>