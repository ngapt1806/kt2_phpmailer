<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "khachhang");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenkh = $_POST['tenkh'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];

    // Sử dụng Prepared Statement để tránh lỗi SQL Injection
    $stmt = $conn->prepare("INSERT INTO khachhang (tenkh, email, sdt, email_status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("sss", $tenkh, $email, $sdt);

    if ($stmt->execute()) {
        $makh = $stmt->insert_id; // Lấy ID khách hàng vừa thêm
        sendEmail($makh, $email, $tenkh, $conn);
        echo "Đăng ký thành công! Kiểm tra email của bạn.";
    } else {
        echo "Lỗi: " . $conn->error;
    }
    $stmt->close();
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

        $mail->setFrom("hongnhungn682@gmail.com", "Admin");
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = "Xác nhận đăng ký tài khoản";

        // Chèn hình ảnh theo dõi mở email
        $tracking_link = "http://localhost/mail/xltrangthai.php?makh=$makh";
        $mail->Body = "Xin chào <b>$toName</b>,<br> 
                      Cảm ơn bạn đã đăng ký!<br> 
                      Vui lòng xác nhận tài khoản bằng cách <a href='http://localhost/mail/verify.php?email=$toEmail'>nhấp vào đây</a>.<br>
                      <img src='$tracking_link' width='1' height='1' style='display: none;'>";

        $mail->send();

        // Cập nhật trạng thái email_status thành 'sent'
        $stmt = $conn->prepare("UPDATE khachhang SET email_status = 'sent' WHERE makh = ?");
        $stmt->bind_param("i", $makh);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        echo "Email không gửi được. Lỗi: " . $mail->ErrorInfo;
    }
}
?>

