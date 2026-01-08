<?php
// config/db.php
// Veritabanı bağlantısı (DB'yi birazdan oluşturacağız)
$host = "localhost";
$dbname = "online_course_db"; // phpMyAdmin'de bu isimle DB açacağız
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
