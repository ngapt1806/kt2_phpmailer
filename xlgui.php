<?php
$conn = new mysqli("localhost", "root", "", "khachang");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    // Kiểm tra email có tồn tại không
    $sql = "SELECT * FROM khachhang WHERE mail='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Tài khoản $email đã được xác nhận thành công!";
    } else {
        echo "Email không tồn tại!";
    }
} else {
    echo "Liên kết không hợp lệ!";
}
?>
