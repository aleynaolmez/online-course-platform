<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/auth_check.php';
require_role('admin');
require '../config/db.php';

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';

if (trim($name) === '') {
    echo "Kategori adı zorunludur.";
    exit;
}

$sql = "INSERT INTO categories (name, description, is_active, created_at)
        VALUES (:name, :description, 1, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'name'        => $name,
    'description' => $description,
]);

header("Location: /online_course_platform/admin/categories.php");
exit;
