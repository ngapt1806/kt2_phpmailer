<?php
// Biến động để sử dụng trong nội dung email
$tracking_link = htmlspecialchars($tracking_link, ENT_QUOTES, 'UTF-8');
$toName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
$toEmail = htmlspecialchars($toEmail, ENT_QUOTES, 'UTF-8');
?>
Xin chào <b><?php echo $toName; ?></b>,<br> 
Cảm ơn bạn đã đăng ký!<br> 
Vui lòng xác nhận tài khoản bằng cách <a href='http://localhost/mail/verify.php?email=<?php echo $toEmail; ?>'>nhấp vào đây</a>.<br>
<img src='<?php echo $tracking_link; ?>' width='1' height='1' style='display: none;'>