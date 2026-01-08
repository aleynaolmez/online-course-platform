<?php
session_start();
require '../config/db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Kullanıcıyı email'e göre bul
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Kullanıcı yoksa hata
if (!$user) {
    echo "Bu e-posta ile kayıtlı kullanıcı bulunamadı.";
    exit;
}

// Şifre doğru mu?
if (!password_verify($password, $user['password'])) {
    echo "Şifre hatalı!";
    exit;
}

// ---- BURASI ÖNEMLİ: ROLE_ID'DEN STRING ÜRETİYORUZ ----
$role = 'student';
if ($user['role_id'] == 1) {
    $role = 'admin';
} elseif ($user['role_id'] == 2) {
    $role = 'instructor';
}
// -----------------------------------------------------


// Giriş başarılı → session başlat
$_SESSION['user_id'] = $user['id'];
$_SESSION['name']    = $user['name'];
$_SESSION['role']    = $role;

// Rolüne göre yönlendir
if ($role === 'admin') {
    header("Location: ../admin/dashboard.php");
} elseif ($role === 'instructor') {
    header("Location: ../instructor/courses.php");
} else {
    header("Location: ../public/index.php");
}

exit;
