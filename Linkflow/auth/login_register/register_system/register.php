<?php
var_dump($_POST);
// Veritabanı bağlantısı
$host = "localhost";
$user = "root";
$password = "";
$database = "linkflow";

$connection = new mysqli($host, $user, $password, $database);

if ($connection->connect_error) {
    die("Connection error: " . $connection->connect_error);
}

// Form işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstName"]);
    $lastname = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirmPassword)) {
        die("Lütfen tüm alanları doldurun.");
    }

    if ($password !== $confirmPassword) {
        die("Şifreler eşleşmiyor.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connection->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Bu e-posta adresi zaten kayıtlı.");
    }
    $stmt->close();

    $stmt = $connection->prepare("INSERT INTO users (email, password, firstname, lastname) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $hashedPassword, $firstname, $lastname);

    if ($stmt->execute()) {
        echo "Kayıt başarılı! <a href='login.html'>Giriş yap</a>";
    } else {
        echo "Hata oluştu: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();

} else {
    echo "Geçersiz istek.";
}
?>

