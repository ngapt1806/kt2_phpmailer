<?php
require 'ketnoi.php';

if (isset($_GET['makh'])) {
    $makh = intval($_GET['makh']);

    // Kiểm tra kết nối cơ sở dữ liệu
    if (!$conn) {
        error_log("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
        http_response_code(500); // Trả về mã lỗi 500
        exit;
    }

    // Cập nhật trạng thái email_status thành 'opened'
    $stmt = $conn->prepare("UPDATE khachhang SET email_status = 'opened' WHERE makh = ?");
    if ($stmt) {
        $stmt->bind_param("i", $makh);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                error_log("Cập nhật trạng thái thành công cho makh: $makh");
            } else {
                error_log("Không có bản ghi nào được cập nhật cho makh: $makh");
            }
        } else {
            error_log("Lỗi khi cập nhật trạng thái cho makh: $makh - " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Lỗi khi chuẩn bị truy vấn: " . $conn->error);
    }
} else {
    error_log("Tham số 'makh' không được cung cấp.");
}

// Tạo ảnh vô hình 1x1 pixel
header("Content-Type: image/png");

// Tạo ảnh 1x1 pixel với nền trong suốt
$img = imagecreatetruecolor(1, 1);

// Tạo màu trong suốt (RGBA)
$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127); // Màu trong suốt
imagefill($img, 0, 0, $transparent);

// Gửi ảnh ra trình duyệt
imagepng($img); 

// Giải phóng bộ nhớ
imagedestroy($img);
?>