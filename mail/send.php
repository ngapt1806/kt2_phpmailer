<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "khachhang");
$message = "";
$alertType = "";

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenkh = $_POST['tenkh'] ?? '';
    $email = $_POST['email'] ?? '';
    $sdt = $_POST['sdt'] ?? '';

    if (empty($tenkh) || empty($email) || empty($sdt)) {
        $message = "⚠️ Vui lòng nhập đầy đủ thông tin.";
        $alertType = "warning";
    } else {
        $check_stmt = $conn->prepare("SELECT * FROM khachhang WHERE sdt = ?");
        $check_stmt->bind_param("s", $sdt);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "❌ Số điện thoại này đã được đăng ký!";
            $alertType = "error";
        } else {
        // Sử dụng Prepared Statement để tránh lỗi SQL Injection
    $stmt = $conn->prepare("INSERT INTO khachhang (tenkh, email, sdt, email_status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("sss", $tenkh, $email, $sdt);

    if ($stmt->execute()) {
        $makh = $stmt->insert_id;
        sendEmail($makh, $email, $tenkh, $conn);
        $message = "✅ Đăng ký thành công! Vui lòng kiểm tra email.";
        $alertType = "success";
    } else {
        $message = "❌ Lỗi khi thêm dữ liệu.";
        $alertType = "error";
    }
    $stmt->close();
}
$check_stmt->close();
}
}

// Hàm gửi email
function sendEmail($makh, $toEmail, $toName, $conn) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->CharSet = "UTF-8";
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "hongnhungn682@gmail.com";  // Thay bằng email của bạn
        $mail->Password = "bllk hakd zpti isvi";   // Mật khẩu ứng dụng của bạn
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("hongnhungn682@gmail.com", "Hồng Nhung");
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = "Xác nhận đăng ký tài khoản";

        // Chèn hình ảnh theo dõi mở email
        $tracking_link = "http://localhost/mail/xltrangthai.php?makh=$makh";
        ob_start();
        include 'fromgui.php';
        $mail->Body = ob_get_clean();
        $mail->send();

        // Cập nhật trạng thái email_status thành 'sent'
        $stmt = $conn->prepare("UPDATE khachhang SET email_status = 'sent' WHERE makh = ?");
        $stmt->bind_param("i", $makh);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
       
    }

}
?>
<!-- HTML hiển thị thông báo -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thông báo</title>
    <style>
        body {
            font-family: Arial; background: #f8f8f8;
            display: flex; justify-content: center; align-items: center;
            height: 100vh;
        }
        .alert {
            padding: 20px; border-radius: 10px;
            font-size: 18px; font-weight: bold;
            text-align: center; width: 400px;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    </style>
</head>
<body>
    <div class="alert <?= $alertType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
</body>
</html>

