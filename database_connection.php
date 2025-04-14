<?php
/**
 * File kết nối đến database
 */

// Đầu tiên include file config nếu chưa được include
if (!defined('DEVELOPMENT_MODE')) {
    require_once 'config.php';
}

// Khai báo biến kết nối database
$db_conn = null;

/**
 * Hàm kết nối tới database
 * 
 * @return PDO Đối tượng PDO kết nối tới database
 */
function db_connect() {
    global $db_conn;
    
    // Nếu đã kết nối rồi thì trả về kết nối đó
    if ($db_conn !== null) {
        return $db_conn;
    }
    
    try {
        // Lấy thông tin kết nối từ file config
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $dbname = defined('DB_NAME') ? DB_NAME : 'webck';
        $username = defined('DB_USER') ? DB_USER : 'root';
        $password = defined('DB_PASS') ? DB_PASS : '';
        $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
        
        // Tạo DSN
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        
        // Thiết lập options
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        // Tạo kết nối
        $db_conn = new PDO($dsn, $username, $password, $options);
        
        return $db_conn;
    } catch (PDOException $e) {
        // Ghi log lỗi
        error_log("Lỗi kết nối database: " . $e->getMessage());
        
        // Hiển thị lỗi nếu ở chế độ phát triển
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "Lỗi kết nối database: " . $e->getMessage();
        }
        
        // Dừng chương trình
        die("Không thể kết nối tới database. Vui lòng thử lại sau.");
    }
}

/**
 * Hàm thực hiện câu lệnh SQL
 * 
 * @param string $sql Câu lệnh SQL với tham số ẩn dạng :name
 * @param array $params Mảng các tham số
 * @param bool $return_stmt Trả về PDOStatement thay vì thực thi ngay
 * @return object|false Trả về đối tượng PDOStatement hoặc false nếu có lỗi
 */
function db_query($sql, $params = [], $return_stmt = false) {
    try {
        // Kết nối tới database
        $conn = db_connect();
        
        // Chuẩn bị câu lệnh
        $stmt = $conn->prepare($sql);
        
        // Ghi log câu lệnh SQL để debug nếu cần
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            error_log("SQL Query: " . $sql);
            error_log("Params: " . print_r($params, true));
        }
        
        // Nếu chỉ cần prepare statement
        if ($return_stmt) {
            return $stmt;
        }
        
        // Thực thi câu lệnh
        $stmt->execute($params);
        
        // Trả về statement
        return $stmt;
    } catch (PDOException $e) {
        // Ghi log lỗi
        error_log("Lỗi thực thi SQL: " . $e->getMessage());
        error_log("SQL: " . $sql);
        error_log("Params: " . print_r($params, true));
        
        // Hiển thị lỗi nếu ở chế độ phát triển
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "Lỗi thực thi SQL: " . $e->getMessage();
        }
        
        return false;
    }
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
    if (empty($data)) {
        return false;
    }
    
    try {
        // Kết nối tới database
        $conn = db_connect();
        
        // Tạo câu lệnh SQL
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        // Chuẩn bị câu lệnh
        $stmt = $conn->prepare($sql);
        
        // Ghi log để debug
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            error_log("Insert into $table: " . print_r($data, true));
            error_log("SQL: $sql");
        }
        
        // Thực thi câu lệnh
        $stmt->execute(array_values($data));
        
        // Trả về ID mới được tạo
        return $conn->lastInsertId();
    } catch (PDOException $e) {
        // Ghi log lỗi
        error_log("Lỗi thêm dữ liệu: " . $e->getMessage());
        
        // Hiển thị lỗi nếu ở chế độ phát triển
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "Lỗi thêm dữ liệu: " . $e->getMessage();
        }
        
        return false;
    }
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
    if (empty($data)) {
        return 0;
    }
    
    try {
        // Kết nối tới database
        $conn = db_connect();
        
        // Tạo phần SET của câu lệnh
        $set_parts = [];
        foreach (array_keys($data) as $column) {
            $set_parts[] = "$column = ?";
        }
        $set_clause = implode(', ', $set_parts);
        
        // Tạo câu lệnh SQL
        $sql = "UPDATE $table SET $set_clause WHERE $where";
        
        // Chuẩn bị câu lệnh
        $stmt = $conn->prepare($sql);
        
        // Ghi log để debug
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            error_log("Update $table: " . print_r($data, true));
            error_log("WHERE: $where, Params: " . print_r($where_params, true));
            error_log("SQL: $sql");
        }
        
        // Kết hợp tham số dữ liệu và tham số WHERE
        $params = array_merge(array_values($data), $where_params);
        
        // Thực thi câu lệnh
        $stmt->execute($params);
        
        // Trả về số dòng bị ảnh hưởng
        return $stmt->rowCount();
    } catch (PDOException $e) {
        // Ghi log lỗi
        error_log("Lỗi cập nhật dữ liệu: " . $e->getMessage());
        
        // Hiển thị lỗi nếu ở chế độ phát triển
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "Lỗi cập nhật dữ liệu: " . $e->getMessage();
        }
        
        return 0;
    }
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
    try {
        // Kết nối tới database
        $conn = db_connect();
        
        // Tạo câu lệnh SQL
        $sql = "DELETE FROM $table WHERE $where";
        
        // Chuẩn bị câu lệnh
        $stmt = $conn->prepare($sql);
        
        // Ghi log để debug
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            error_log("Delete from $table WHERE $where");
            error_log("Params: " . print_r($params, true));
            error_log("SQL: $sql");
        }
        
        // Thực thi câu lệnh
        $stmt->execute($params);
        
        // Trả về số dòng bị ảnh hưởng
        return $stmt->rowCount();
    } catch (PDOException $e) {
        // Ghi log lỗi
        error_log("Lỗi xóa dữ liệu: " . $e->getMessage());
        
        // Hiển thị lỗi nếu ở chế độ phát triển
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE === true) {
            echo "Lỗi xóa dữ liệu: " . $e->getMessage();
        }
        
        return 0;
    }
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
    $sql = "SELECT 1 FROM $table WHERE $where LIMIT 1";
    $result = db_get_value($sql, $params, false);
    return $result !== false;
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
    $sql = "SELECT COUNT(*) FROM $table";
    if (!empty($where)) {
        $sql .= " WHERE $where";
    }
    
    return (int) db_get_value($sql, $params, 0);
}

/**
 * Hàm thiết lập cấu hình
 * 
 * @param string $key Khóa cấu hình
 * @param mixed $value Giá trị cấu hình
 * @return bool Thành công hay thất bại
 */
function set_config($key, $value) {
    // Kiểm tra xem cấu hình đã tồn tại chưa
    $exists = db_exists('configurations', 'config_key = ?', [$key]);
    
    if ($exists) {
        // Cập nhật cấu hình
        return db_update('configurations', ['config_value' => $value], 'config_key = ?', [$key]) > 0;
    } else {
        // Thêm cấu hình mới
        return db_insert('configurations', [
            'config_key' => $key,
            'config_value' => $value
        ]) !== false;
    }
}

/**
 * Hàm bắt đầu transaction
 */
function db_begin_transaction() {
    try {
        $conn = db_connect();
        $conn->beginTransaction();
        return true;
    } catch (PDOException $e) {
        error_log("Lỗi bắt đầu transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm commit transaction
 */
function db_commit() {
    try {
        $conn = db_connect();
        $conn->commit();
        return true;
    } catch (PDOException $e) {
        error_log("Lỗi commit transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm rollback transaction
 */
function db_rollback() {
    try {
        $conn = db_connect();
        $conn->rollBack();
        return true;
    } catch (PDOException $e) {
        error_log("Lỗi rollback transaction: " . $e->getMessage());
        return false;
    }
}

/**
 * Hàm escape chuỗi
 */
function db_escape($value) {
    $conn = db_connect();
    return $conn->quote($value);
}

/**
 * Hàm lấy ID cuối cùng được chèn
 */
function db_last_insert_id() {
    $conn = db_connect();
    return $conn->lastInsertId();
}

/**
 * Hàm thực thi nhiều câu lệnh SQL
 */
function db_execute_batch($queries) {
    // Khai báo biến trước try-catch để đảm bảo tồn tại trong toàn phạm vi
    $conn = null;
    
    try {
        // Lưu kết nối vào biến
        $conn = db_connect();
        
        // Bắt đầu transaction
        $conn->beginTransaction();
        
        // Thực thi từng câu lệnh
        foreach ($queries as $sql) {
            // Sử dụng biến $conn từ phạm vi bên ngoài
            $conn->exec($sql);
        }
        
        // Commit transaction
        $conn->commit();
        
        return true;
    } catch (PDOException $e) {
        // Rollback nếu có lỗi và kết nối đã được thiết lập
        if ($conn !== null && $conn->inTransaction()) {
            $conn->rollBack();
        }
        
        // Ghi log lỗi
        error_log("Lỗi thực thi batch: " . $e->getMessage());
        
        return false;
    }
}

// Khai báo các biến toàn cục để tương thích với code cũ
$conn = $db_conn;
$pdo = $db_conn;