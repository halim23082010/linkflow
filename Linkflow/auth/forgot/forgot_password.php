<?php
ob_start(); // Her türlü çıktıyı tamponla
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$host = 'localhost';
$database = 'linkflow';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı bağlantısı başarısız']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Sadece POST metodu kullanılabilir']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz JSON']);
    exit;
}

// Şifre sıfırlama bağlantısı isteği
if (isset($data['email']) && !isset($data['token'])) {
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz e-posta']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Bu e-posta sistemde bulunamadı']);
        exit;
    }

    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 3600); // 1 saat geçerli

    $pdo->prepare("UPDATE users SET token = ?, expires_at = ? WHERE email = ?")
        ->execute([$token, $expires, $email]);

    $resetLink = "https://localhost:63342/Linkflow/auth/forgot/forgot_reset/forgot_reset.html?token=$token";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'abdulhalimabdulbaki23.08@gmail.com';
        $mail->Password = 'yrworwcizbuehcpk'; // Uygulama şifresi
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->SMTPDebug = 0; // Hata detaylarını verir
        $mail->Debugoutput = 'html'; // HTML olarak göster


        $mail->setFrom('no-reply@seninsiten.com', 'Linkflow');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Şifre Sıfırlama Bağlantısı';
        $mail->Body = "Merhaba,<br><br>Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:<br><a href='$resetLink'>$resetLink</a><br><br>Bu bağlantı 1 saat içinde geçerliliğini yitirecektir.";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'E-posta gönderilemedi: ' . $mail->ErrorInfo]);
    }

    exit;
}

// Yeni şifre belirleme
if (isset($data['token']) && isset($data['newPassword'])) {
    $token = $data['token'];
    $newPassword = $data['newPassword'];

    if (strlen($newPassword) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Şifre en az 6 karakter olmalı']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT email FROM users WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz veya süresi dolmuş token']);
        exit;
    }

    $email = $row['email'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $pdo->prepare("UPDATE users SET password = ?, token = NULL, expires_at = NULL WHERE email = ?")
        ->execute([$hashedPassword, $email]);

    echo json_encode(['status' => 'success', 'message' => 'Şifreniz başarıyla güncellendi']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek']);
ob_end_clean(); // HTML çıktısını temizle

exit;
