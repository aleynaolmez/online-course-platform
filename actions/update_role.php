<?php
session_start();
require '../includes/auth_check.php';
require_role('admin');
require '../config/db.php';

$userId = $_POST['user_id'] ?? null;
$newRoleId = $_POST['role_id'] ?? null;

if (!$userId || !$newRoleId) {
    echo "Geçersiz istek.";
    exit;
}

// Sadece 1,2,3 geçerli
$allowedRoles = [1, 2, 3];
if (!in_array((int)$newRoleId, $allowedRoles, true)) {
    echo "Geçersiz rol.";
    exit;
}

// Güncelle
$sql = "UPDATE users SET role_id = :role_id WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'role_id' => (int)$newRoleId,
    'id'      => (int)$userId,
]);

// Eğer kendi rolünü değiştirdiyse, session'ı da güncelle
if ($_SESSION['user_id'] == $userId) {
    $role = 'student';
    if ($newRoleId == 1) {
        $role = 'admin';
    } elseif ($newRoleId == 2) {
        $role = 'instructor';
    }
    $_SESSION['role'] = $role;
}

// Geri admin kullanıcı listesine
header("Location: /online_course_platform/admin/users.php");
exit;
