<?php
header("Content-Type: application/json");

// Veritabanı bağlantısı
$host = 'localhost';
$database = 'linkflow';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// JSON isteği al
$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$passwordInput = $data['password'] ?? '';

// Giriş kontrolü
if (!$email || !$passwordInput) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

// Veritabanında kullanıcıyı bul
$stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($passwordInput, $user['password'])) {
    echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
} else {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}
