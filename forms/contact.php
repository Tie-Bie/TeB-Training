<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

// =====================
// reCAPTCHA
// =====================
$secretKey = "YOUR_SECRET_KEY";
$response  = $_POST['g-recaptcha-response'] ?? '';

$verify = file_get_contents(
  "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response"
);
$captcha = json_decode($verify);

if (!$captcha->success) {
  echo json_encode(["status"=>"error","message"=>"กรุณายืนยันว่าไม่ใช่บอท"]);
  exit;
}

// =====================
// รับค่าฟอร์ม
// =====================
$name    = htmlspecialchars($_POST['name']);
$email   = htmlspecialchars($_POST['email']);
$subject = htmlspecialchars($_POST['subject']);
$message = nl2br(htmlspecialchars($_POST['message']));

try {
    $mail = new PHPMailer(true);

    // SMTP Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'YOUR_GMAIL@gmail.com';
    $mail->Password   = 'APP_PASSWORD';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($mail->Username, 'Website Contact');
    $mail->addAddress('sirinthiya.ch@gmail.com'); // ✔ แก้ domain ถูกแล้ว

    $mail->isHTML(true);
    $mail->Subject = "Contact Form: $subject";
    $mail->Body    = "
        <b>ชื่อ:</b> $name <br>
        <b>อีเมล:</b> $email <br><br>
        <b>ข้อความ:</b><br>$message
    ";

    $mail->send();

    echo json_encode(["status"=>"success","message"=>"ส่งข้อความเรียบร้อยแล้ว"]);

} catch (Exception $e) {
    echo json_encode(["status"=>"error","message"=>"ไม่สามารถส่งอีเมลได้"]);
}
