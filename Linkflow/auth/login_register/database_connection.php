<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "linkflow";

// Doğru: $connection değişkeni olarak tanımlıyoruz
$connection = new mysqli($host, $user, $password, $database);

// Hata kontrolü
if ($connection->connect_error) {
    die("Connection error: " . $connection->connect_error);

}
?>

