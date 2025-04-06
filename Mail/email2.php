<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require __DIR__ . '/../vendor/autoload.php';
$connect = new PDO("mysql:host=localhost;dbname=email_data", "root", "");
$base_url = "http://localhost/tutorials/how-to-track-email-open-or-not-using-php/";
$message = '';

if(isset($_POST["send"]))
{
$mail = new PHPMailer(true);

    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->SMTPAuth = true;
    $mail->Username = $_POST["sender_email"];  // Email gửi đi từ người đăng ký
    $mail->Password = $_POST["sender_password"];  // Mật khẩu ứng dụng của họ
    $mail->From = $_POST["sender_email"];    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->From = "huyenjuly25082003@gmail.com";
    $mail->AddAddress($_POST["receiver_email"]);
    $mail->WordWrap = 50;
    $mail->isHTML(true);
    $mail->Subject = $_POST["email_subject"];
    $track_code = md5(uniqid(rand()));
    $message_body = $_POST["email_body"].'<img src="'.$base_url.'track.php?code='.$track_code.'" width="1" height="1" />';
    $mail->Body = $message_body;

    if($mail->send())
    {
        $data = array(
            ':email_subject' => $_POST["email_subject"],
            ':receiver_email' => $_POST["receiver_email"],
            ':email_body' => $_POST["email_body"],
            ':track_code' => $track_code,
        );

        $query = "
        INSERT INTO email_data
        (email_subject, receiver_email, email_body, email_track_code)
        VALUES (:email_subject, :receiver_email, :email_body, :track_code)
        ";
        $statement = $connect->prepare($query);

        if($statement->execute($data))
        {
            $message = '<label class="text-success">Email Sent</label>';
        }
    }
}

function fetch_email_data($connect)
{
    $query = "
    SELECT * FROM email_data
    ORDER BY email_id DESC
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $total_row = $statement->rowCount();
    
    $output = '
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
    <tr>
        <th width="30%">Email</th>
        <th width="30%">Subject</th>
        <th width="20%">Status</th>
        <th width="20%">Open Datetime</th>
    </tr>
    ';

    if($total_row > 0)
    {
        foreach($result as $row)
        {
            $status = ($row["email_status"] == 'yes') ? 
                '<span class="label label-success">Open</span>' : 
                '<span class="label label-danger">Not Open</span>';

            $output .= '
            <tr>
                <td>'.$row["receiver_email"].'</td>
                <td>'.$row["email_subject"].'</td>
                <td>'.$status.'</td>
                <td>'.$row["email_open_date"].'</td>
            </tr>';
        }
    }
    else
    {
        $output .= '
        <tr>
            <td colspan="4" align="center">No Data Found</td>
        </tr>';
    }
    
    $output .= '</table></div>';
    return $output;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>How to Track Email Open or Not Using PHP</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="bootstrap.min.js"></script>
</head>
<body>
    <br/>
    <div class="container">
        <h3 align="center">How to Track Email Open or Not Using PHP</h3>
        <br/>
        <?php echo $message; ?>
        
        <form method="post">
        <div class="form-group">
           <label>Enter Sender Email</label>
             <input type="email" name="sender_email" class="form-control" required>
           </div>
         <div class="form-group">
           <label>Enter App Password</label>
         <input type="password" name="sender_password" class="form-control" required>
        </div>

            <div class="form-group">
                <label>Enter Email Subject</label>
                <input type="text" name="email_subject" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Enter Receiver Email</label>
                <input type="email" name="receiver_email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Enter Email Body</label>
                <textarea name="email_body" rows="5" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="send" class="btn btn-info" value="Send Email">
            </div>
            
        </form>
        
        <br/>
        <h4 align="center">Email Open Status</h4>
        <?php echo fetch_email_data($connect); ?>
    </div>
</body>
</html>