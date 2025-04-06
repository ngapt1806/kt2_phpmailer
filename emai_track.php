<?php
// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối database
try {
    $connect = new PDO("mysql:host=localhost;dbname=email_data", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Tạo mã tracking mới khi gửi email
function generateTrackingCode($connect, $receiver_email) {
    $tracking_code = md5(uniqid(rand(), true)); // Tạo mã tracking
    $query = "INSERT INTO email_data (receiver_email, email_track_code, email_status) VALUES (:receiver_email, :email_track_code, 'no')";
    $statement = $connect->prepare($query);
    $statement->execute([
        ':receiver_email' => $receiver_email,
        ':email_track_code' => $tracking_code
    ]);
    return $tracking_code;
}

$receiver_email = "phamthinga593@gmail.com"; // Địa chỉ email người nhận
$tracking_code = generateTrackingCode($connect, $receiver_email);

// Cập nhật URL tracking với ngrok
$tracking_url = "https://f913-104-28-254-75.ngrok-free.app/Mail/email_track.php?code=" . $tracking_code;

$email_content = "
    <p>Chào bạn,</p>
    <p>Đây là nội dung email của bạn.</p>
    <img src='$tracking_url' width='1' height='1' style='display:none;' />
";


// Gửi email bằng PHPMailer hoặc mail()
echo "Email gửi thành công với tracking code: $tracking_code";

// ----------------- CODE TRACKING -----------------
if (isset($_GET["code"])) {
    $email_track_code = $_GET["code"];

    // Kiểm tra mã tracking có tồn tại không
    $checkQuery = "SELECT * FROM email_data WHERE email_track_code = :email_track_code";
    $checkStmt = $connect->prepare($checkQuery);
    $checkStmt->execute([':email_track_code' => $email_track_code]);

    if ($checkStmt->rowCount() > 0) {
        // Cập nhật trạng thái email
        $query = "
            UPDATE email_data
            SET email_status = 'yes', email_open_date = NOW()
            WHERE email_track_code = :email_track_code
            AND email_status = 'no'
        ";

        $statement = $connect->prepare($query);
        if ($statement->execute([':email_track_code' => $email_track_code])) {
            error_log("✅ Cập nhật thành công mã: " . $email_track_code);
        } else {
            error_log("❌ Lỗi khi cập nhật email_data.");
        }
    } else {
        error_log("⚠️ Mã tracking không tồn tại: " . $email_track_code);
    }
}

// Trả về ảnh tracking hợp lệ
header("Content-Type: image/png");
$image = imagecreatetruecolor(1, 1);
$white = imagecolorallocate($image, 255, 255, 255);
imagesetpixel($image, 0, 0, $white);
imagepng($image);
imagedestroy($image);
?>

