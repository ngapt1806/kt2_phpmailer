<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "khachang");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenkh = $_POST['tenkh'];
    $email = $_POST['email'];
    $sdt = $_POST['sdt'];

    // Thêm dữ liệu vào CSDL
    $sql = "INSERT INTO khachhang (tenkh, mail, sdt) VALUES ('$tenkh', '$email', '$sdt')";

    if ($conn->query($sql) === TRUE) {
        sendEmail($email, $tenkh);
        echo "Đăng ký thành công! Kiểm tra email của bạn.";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Hàm gửi email
function sendEmail($toEmail, $toName) {
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
        $mail->Body = "Xin chào <b>$toName</b>,<br> 
                      Cảm ơn bạn đã đăng ký!<br> 
                      Vui lòng xác nhận tài khoản bằng cách <a href='http://localhost/mail/verify.php?email=$toEmail'>nhấp vào đây</a>.";

        $mail->send();
    } catch (Exception $e) {
        echo "Email không gửi được. Lỗi: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
</head>
<body>
    <h2>Đăng ký tài khoản</h2>
    <form method="POST" action="">
        Tên: <input type="text" name="tenkh" required><br>
        Email: <input type="email" name="email" required><br>
        SĐT: <input type="text" name="sdt" required><br>
        <button type="submit">Đăng ký</button>
    </form>
</body>
</html>