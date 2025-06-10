<?php
// Veritabanı bilgileri
$host = "localhost";
$user = "root";
$password = "";
$database = "linkflow";

// Bağlantıyı kur
$connection = new mysqli($host, $user, $password, $database);


// Bağlantı kontrolü
if ($connection->connect_error) {
    die("<h2 style='color: red;'>❌ MySQL bağlantı hatası:</h2><p>" . $connection->connect_error . "</p>");
} else {
    echo "<h2 style='color: green;'>✅ MySQL bağlantısı başarılı!</h2>";
}

// Veritabanından kullanıcıları çek
$sql = "SELECT id, firstname, lastname, email FROM users";
$result = $connection->query($sql);

// Sonuç var mı?
if ($result->num_rows > 0) {
    echo "<h3>Kayıtlı kullanıcılar:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Ad</th><th>Soyad</th><th>Email</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["firstname"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["lastname"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Hiç kullanıcı bulunamadı.</p>";
}

$connection->close();
?>

