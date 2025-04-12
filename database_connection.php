<?php
/**
 * File kết nối đến database đã được thay thế bằng mô phỏng
 * Các hàm trong file này đã được điều chỉnh để sử dụng dữ liệu tĩnh thay vì thực sự kết nối database
 */

// Đầu tiên include file config nếu chưa được include
if (!defined('DEVELOPMENT_MODE')) {
    require_once 'config.php';
}

// Không cần kết nối thực tế đến MySQL nữa
// Thay vào đó, chúng ta sẽ sử dụng dữ liệu tĩnh từ config.php

// Khai báo biến giả lập kết nối để tương thích với code cũ
$db_conn = null;

/**
 * Hàm để giả lập thực hiện câu lệnh SQL
 * 
 * @param string $sql Câu lệnh SQL với tham số ẩn dạng :name
 * @param array $params Mảng các tham số
 * @param bool $return_stmt Trả về PDOStatement thay vì thực thi ngay
 * @return object|false Trả về đối tượng giả lập PDOStatement
 */
function db_query($sql, $params = [], $return_stmt = false) {
    // Ghi log câu lệnh SQL để debug nếu cần
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        error_log("SQL Query (Mocked): " . $sql);
        error_log("Params: " . print_r($params, true));
    }
    
    // Trả về đối tượng giả lập PDOStatement
    $stmt = new MockPDOStatement();
    
    // Nếu chỉ cần prepare statement
    if ($return_stmt) {
        return $stmt;
    }
    
    // Xử lý câu lệnh dựa vào loại SQL
    if (stripos($sql, 'SELECT') === 0) {
        // Phân tích câu lệnh SELECT để xác định dữ liệu cần trả về
        if (stripos($sql, 'FROM services') !== false) {
            $stmt->setData([
                ['service_code' => 'home', 'name' => 'Vệ sinh nhà ở', 'serviceID' => 1],
                ['service_code' => 'office', 'name' => 'Vệ sinh văn phòng', 'serviceID' => 2]
            ]);
        } elseif (stripos($sql, 'FROM faqs') !== false) {
            $stmt->setData([
                [
                    'question' => 'Chi phí dịch vụ vệ sinh được tính như thế nào?',
                    'answer' => 'Chi phí dịch vụ vệ sinh được tính dựa trên nhiều yếu tố như diện tích, loại hình dịch vụ, mức độ bẩn, tần suất vệ sinh và các yêu cầu đặc biệt. Chúng tôi sẽ khảo sát và cung cấp báo giá chi tiết, minh bạch.'
                ],
                [
                    'question' => 'Các hóa chất vệ sinh có an toàn không?',
                    'answer' => 'Chúng tôi sử dụng các sản phẩm và hóa chất vệ sinh thân thiện với môi trường, an toàn cho sức khỏe con người và vật nuôi. Đối với khách hàng có yêu cầu đặc biệt, chúng tôi có thể sử dụng sản phẩm vệ sinh theo yêu cầu.'
                ],
                [
                    'question' => 'Thời gian thực hiện dịch vụ vệ sinh mất bao lâu?',
                    'answer' => 'Thời gian thực hiện dịch vụ vệ sinh phụ thuộc vào diện tích, loại hình dịch vụ và mức độ bẩn. Thông thường, dịch vụ vệ sinh nhà ở có diện tích trung bình sẽ mất khoảng 3-5 giờ, vệ sinh văn phòng có thể mất từ 2-8 giờ tùy quy mô.'
                ],
                [
                    'question' => 'Có cần chuẩn bị gì trước khi dịch vụ vệ sinh đến không?',
                    'answer' => 'Để tối ưu hóa hiệu quả vệ sinh, khách hàng nên dọn dẹp đồ đạc cá nhân, tài liệu quan trọng và các vật dụng có giá trị. Điều này giúp đội ngũ vệ sinh tập trung vào công việc chính và tránh làm xáo trộn đồ đạc cá nhân của khách hàng.'
                ]
            ]);
        } elseif (stripos($sql, 'FROM configurations') !== false) {
            // Xử lý tùy theo tham số truy vấn
            if (isset($params['key'])) {
                $key = $params['key'];
                global $configurations;
                if (isset($configurations[$key])) {
                    $stmt->setData([[
                        'config_key' => $key,
                        'config_value' => $configurations[$key]
                    ]]);
                } else {
                    $stmt->setData([]);
                }
            } else {
                // Trả về tất cả cấu hình
                global $configurations;
                $data = [];
                foreach ($configurations as $key => $value) {
                    $data[] = [
                        'config_key' => $key,
                        'config_value' => $value
                    ];
                }
                $stmt->setData($data);
            }
        } elseif (stripos($sql, 'FROM testimonials') !== false) {
            $stmt->setData([
                [
                    'id' => 1,
                    'name' => 'Nguyễn Văn A',
                    'email' => 'nguyenvana@example.com',
                    'service' => 'home',
                    'rating' => 5.0,
                    'testimonial' => 'Tôi rất hài lòng với dịch vụ vệ sinh của theCleaner. Nhân viên chuyên nghiệp, làm việc tỉ mỉ và hiệu quả.',
                    'photo_path' => 'images/client1.jpg',
                    'location' => 'Khách hàng tại Hà Nội',
                    'status' => 'approved'
                ],
                [
                    'id' => 2,
                    'name' => 'Trần Thị B',
                    'email' => 'tranthib@example.com',
                    'service' => 'office',
                    'rating' => 5.0,
                    'testimonial' => 'Dịch vụ vệ sinh văn phòng của theCleaner thực sự đáng tiền. Đội ngũ nhân viên làm việc nhanh chóng, chuyên nghiệp và hiệu quả.',
                    'photo_path' => 'images/client2.jpg',
                    'location' => 'Giám đốc công ty XYZ',
                    'status' => 'approved'
                ]
            ]);
        } elseif (stripos($sql, 'FROM service_features') !== false) {
            $service_id = $params['service_id'] ?? null;
            if ($service_id == 1) {
                $stmt->setData([
                    [
                        'id' => 1,
                        'service_id' => 1,
                        'title' => 'Vệ Sinh Phòng Khách',
                        'description' => 'Hút bụi và lau sàn nhà, lau chùi bàn ghế, tủ kệ, đồ trang trí, vệ sinh cửa sổ, rèm cửa và làm sạch các vật dụng khác.'
                    ],
                    [
                        'id' => 2,
                        'service_id' => 1,
                        'title' => 'Vệ Sinh Phòng Ngủ',
                        'description' => 'Hút bụi giường, nệm, làm sạch nội thất phòng ngủ, thay ga trải giường, vệ sinh cửa sổ và rèm cửa.'
                    ],
                    [
                        'id' => 3,
                        'service_id' => 1,
                        'title' => 'Vệ Sinh Nhà Bếp',
                        'description' => 'Làm sạch bề mặt bếp, tủ kệ, thiết bị gia dụng, vệ sinh lò vi sóng, lò nướng, tủ lạnh và các thiết bị khác.'
                    ],
                    [
                        'id' => 4,
                        'service_id' => 1,
                        'title' => 'Vệ Sinh Phòng Tắm',
                        'description' => 'Làm sạch bồn tắm, bồn rửa, toilet, gương, vách kính, sàn nhà và các vật dụng trong phòng tắm.'
                    ]
                ]);
            } elseif ($service_id == 2) {
                $stmt->setData([
                    [
                        'id' => 5,
                        'service_id' => 2,
                        'title' => 'Vệ Sinh Hàng Ngày',
                        'description' => 'Lau sàn, hút bụi thảm, vệ sinh bề mặt làm việc, làm sạch khu vực lễ tân, hành lang và khu vực chung.'
                    ],
                    [
                        'id' => 6,
                        'service_id' => 2,
                        'title' => 'Vệ Sinh Khu Vực Làm Việc',
                        'description' => 'Lau chùi bàn ghế, tủ kệ, thiết bị văn phòng, màn hình máy tính, bàn phím và điện thoại.'
                    ],
                    [
                        'id' => 7,
                        'service_id' => 2,
                        'title' => 'Vệ Sinh Khu Vực Nhà Bếp/Pantry',
                        'description' => 'Làm sạch bề mặt bếp, tủ kệ, thiết bị, lò vi sóng, tủ lạnh và khu vực ăn uống.'
                    ],
                    [
                        'id' => 8,
                        'service_id' => 2,
                        'title' => 'Vệ Sinh Nhà Vệ Sinh',
                        'description' => 'Làm sạch bồn rửa, toilet, gương, sàn nhà, bổ sung giấy vệ sinh, xà phòng và các vật dụng cần thiết.'
                    ]
                ]);
            } else {
                $stmt->setData([]);
            }
        } else {
            // Trường hợp mặc định trả về mảng rỗng
            $stmt->setData([]);
        }
    }
    
    return $stmt;
}

/**
 * Hàm lấy nhiều dòng dữ liệu
 * 
 * @param string $sql Câu lệnh SQL
 * @param array $params Tham số cho câu lệnh SQL
 * @param string $key_column Tên cột để làm key cho mảng kết quả (không bắt buộc)
 * @return array Mảng kết quả
 */
function db_get_rows($sql, $params = [], $key_column = null) {
    $stmt = db_query($sql, $params);
    if (!$stmt) {
        return [];
    }
    
    $data = $stmt->fetchAll();
    
    if ($key_column && !empty($data)) {
        $results = [];
        foreach ($data as $row) {
            if (isset($row[$key_column])) {
                $results[$row[$key_column]] = $row;
            }
        }
        return $results;
    }
    
    return $data;
}

/**
 * Hàm lấy một dòng dữ liệu
 * 
 * @param string $sql Câu lệnh SQL
 * @param array $params Tham số cho câu lệnh SQL
 * @return array|false Mảng kết quả hoặc false nếu không có dữ liệu
 */
function db_get_row($sql, $params = []) {
    $stmt = db_query($sql, $params);
    if (!$stmt) {
        return false;
    }
    
    $data = $stmt->fetch();
    return empty($data) ? false : $data;
}

/**
 * Hàm lấy một giá trị từ câu lệnh SQL
 * 
 * @param string $sql Câu lệnh SQL
 * @param array $params Tham số cho câu lệnh SQL
 * @param mixed $default Giá trị mặc định nếu không có dữ liệu
 * @return mixed Giá trị đầu tiên của dòng đầu tiên hoặc giá trị mặc định nếu không có dữ liệu
 */
function db_get_value($sql, $params = [], $default = null) {
    $row = db_get_row($sql, $params);
    if ($row === false) {
        return $default;
    }
    
    return reset($row);
}

/**
 * Hàm thêm dữ liệu vào bảng
 * 
 * @param string $table Tên bảng
 * @param array $data Mảng dữ liệu với key là tên cột
 * @return int|false ID mới được tạo hoặc false nếu thất bại
 */
function db_insert($table, $data) {
    // Ghi log để debug
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        error_log("Insert into $table: " . print_r($data, true));
    }
    
    // Trả về ID giả
    return mt_rand(1000, 9999);
}

/**
 * Hàm cập nhật dữ liệu trong bảng
 * 
 * @param string $table Tên bảng
 * @param array $data Mảng dữ liệu với key là tên cột
 * @param string $where Điều kiện WHERE (không bao gồm từ WHERE)
 * @param array $where_params Tham số cho điều kiện WHERE
 * @return int Số dòng bị ảnh hưởng
 */
function db_update($table, $data, $where, $where_params = []) {
    // Ghi log để debug
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        error_log("Update $table: " . print_r($data, true));
        error_log("WHERE: $where, Params: " . print_r($where_params, true));
    }
    
    // Giả lập 1 dòng bị ảnh hưởng
    return 1;
}

/**
 * Hàm xóa dữ liệu từ bảng
 * 
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE (không bao gồm từ WHERE)
 * @param array $params Tham số cho điều kiện WHERE
 * @return int Số dòng bị ảnh hưởng
 */
function db_delete($table, $where, $params = []) {
    // Ghi log để debug
    if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
        error_log("Delete from $table WHERE $where");
        error_log("Params: " . print_r($params, true));
    }
    
    // Giả lập 1 dòng bị ảnh hưởng
    return 1;
}

/**
 * Hàm kiểm tra sự tồn tại của một bản ghi
 * 
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE (không bao gồm từ WHERE)
 * @param array $params Tham số cho điều kiện WHERE
 * @return bool Tồn tại hay không
 */
function db_exists($table, $where, $params = []) {
    // Luôn trả về false để không can thiệp vào dữ liệu hiện có
    return false;
}

/**
 * Hàm đếm số bản ghi
 * 
 * @param string $table Tên bảng
 * @param string $where Điều kiện WHERE (không bao gồm từ WHERE, có thể để trống)
 * @param array $params Tham số cho điều kiện WHERE
 * @return int Số bản ghi
 */
function db_count($table, $where = '', $params = []) {
    // Mặc định trả về 0
    return 0;
}

/**
 * Hàm thiết lập cấu hình
 * 
 * @param string $key Khóa cấu hình
 * @param mixed $value Giá trị cấu hình
 * @return bool Thành công hay thất bại
 */
function set_config($key, $value) {
    // Không thực sự lưu vào database, chỉ cập nhật biến toàn cục
    global $configurations;
    $configurations[$key] = $value;
    return true;
}

/**
 * Hàm giả lập bắt đầu transaction
 */
function db_begin_transaction() {
    return true;
}

/**
 * Hàm giả lập commit transaction
 */
function db_commit() {
    return true;
}

/**
 * Hàm giả lập rollback transaction
 */
function db_rollback() {
    return true;
}

/**
 * Hàm giả lập escape chuỗi
 */
function db_escape($value) {
    // Trả về giá trị đã được xử lý đơn giản
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Hàm giả lập last insert ID
 */
function db_last_insert_id() {
    return mt_rand(1000, 9999);
}

/**
 * Hàm giả lập thực thi nhiều câu lệnh SQL
 */
function db_execute_batch($queries) {
    return true;
}

/**
 * Lớp giả lập PDOStatement
 */
class MockPDOStatement {
    private $data = [];
    private $position = 0;
    
    public function setData($data) {
        $this->data = $data;
        $this->position = 0;
    }
    
    public function execute($params = []) {
        return true;
    }
    
    public function fetchAll($fetchMode = null) {
        return $this->data;
    }
    
    public function fetch($fetchMode = null) {
        if ($this->position >= count($this->data)) {
            return false;
        }
        return $this->data[$this->position++];
    }
    
    public function rowCount() {
        return count($this->data);
    }
}

// Khai báo các biến toàn cục để tương thích với code cũ
$conn = $db_conn;
$pdo = $db_conn;