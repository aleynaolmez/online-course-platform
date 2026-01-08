<?php
session_start();
require '../config/db.php';

// Formdan gelen veriler
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Şifre hash'leme
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Yeni kullanıcı daima 'student' rolüyle başlasın
// roles tablosunda student = 3 idi
$role_id = 3;

// Aynı e-posta var mı kontrolü
$check = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$check->execute(['email' => $email]);

if ($check->rowCount() > 0) {
    echo "Bu e-posta ile daha önce kayıt olunmuş!";
    exit;
}

// Kullanıcı ekleme
$sql = "INSERT INTO users (name, email, password, role_id, created_at)
        VALUES (:name, :email, :password, :role_id, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'name'     => $name,
    'email'    => $email,
    'password' => $hashedPassword,
    'role_id'  => $role_id
]);

// Kayıt sonrası otomatik giriş
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['name']    = $name;
$_SESSION['role']    = 'student';

header("Location: ../public/index.php");
exit;
