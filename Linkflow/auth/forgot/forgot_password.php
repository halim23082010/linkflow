<?php
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/PHPMailer-master/src/Exception.php';
require '/PHPMailer-master/src/PHPMailer.php';
require '/PHPMailer-master/src/SMTP.php';

// === 1. VERİTABANI BAĞLANTISI ===
$host = 'localhost';
$dbname = 'linkflow';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// === 2. YALNIZCA POST METODU KABUL EDİLİR ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Only POST method allowed']);
    exit;
}

// === 3. VERİYİ AL ===
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// === 4. ŞİFRE SIFIRLAMA MAİLİ ===
if (isset($data['email']) && !isset($data['token'])) {
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email']);
        exit;
    }

    // E-posta veritabanında var mı?
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Email not found']);
        exit;
    }

    // Eski tokenları sil
    $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

    // Yeni token
    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", time() + 3600);

    $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
        ->execute([$email, $token, $expires]);

    $resetLink = "https://seninsiten.com/reset_password.html?token=$token";

    // === PHPMailer ile gönderim ===
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'abdulhalimabdulbaki23.08@gmail.com';
        $mail->Password = 'yrworwcizbuehcpk'; // Uygulama şifresi
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@seninsiten.com', 'Linkflow');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body = "Merhaba,<br><br>Şifrenizi sıfırlamak için aşağıdaki bağlantıya tıklayın:<br><a href='$resetLink'>$resetLink</a><br><br>Bu bağlantı 1 saat içinde geçerliliğini yitirecek.";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Reset link sent to your email']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Email could not be sent: ' . $mail->ErrorInfo]);
    }

    exit;
}

// === 5. YENİ ŞİFREYİ KAYDET ===
if (isset($data['token']) && isset($data['newPassword'])) {
    $token = $data['token'];
    $newPassword = $data['newPassword'];

    if (strlen($newPassword) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token']);
        exit;
    }

    $email = $row['email'];
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hashedPassword, $email]);
    $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);

    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
    exit;
}

// === 6. GEÇERSİZ İSTEK ===
echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
exit;
