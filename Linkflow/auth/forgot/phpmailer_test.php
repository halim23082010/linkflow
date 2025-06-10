<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);


try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'abdulhalimabdulbaki23.08@gmail.com';
    $mail->Password = 'yrworwcizbuehcpk'; // Gmail uygulama şifresi
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('no-reply@seninsiten.com', 'Linkflow');
    $mail->addAddress('abdulhalimabdulbaki08.23@gmail.com'); // Kendine gönder

    $mail->isHTML(true);
    $mail->Subject = 'Test Maili';
    $mail->Body = 'Bu bir test e-postasıdır. PHPMailer çalışıyor!';

    $mail->send();
    echo '✅ E-posta başarıyla gönderildi';
} catch (Exception $e) {
    echo '❌ Hata oluştu: ' . $mail->ErrorInfo;
}
