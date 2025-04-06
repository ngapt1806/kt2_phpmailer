<?php
// verify.php

// Kết nối đến cơ sở dữ liệu của bạn
$conn = new mysqli('your_host', 'your_username', 'your_password', 'your_database');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Kiểm tra mã xác nhận trong cơ sở dữ liệu
    $sql = "SELECT * FROM users WHERE verification_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mã xác nhận hợp lệ, cập nhật trạng thái người dùng
        $sql = "UPDATE users SET verified = 1 WHERE verification_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();

        echo "Tài khoản của bạn đã được xác nhận thành công!";
    } else {
        echo "Mã xác nhận không hợp lệ.";
    }
}

$conn->close();
?>