<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/auth_check.php';
require_role('instructor');
require '../config/db.php';

$courseId = $_POST['course_id'] ?? null;
$title    = $_POST['title'] ?? '';
$position = $_POST['position'] ?? 1;

if (!$courseId || trim($title) === '') {
    echo "Zorunlu alanlar eksik.";
    exit;
}

$position = (int)$position;

$sql = "INSERT INTO sections (course_id, title, position, created_at)
        VALUES (:course_id, :title, :position, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'course_id' => (int)$courseId,
    'title'     => $title,
    'position'  => $position,
]);

header("Location: /online_course_platform/instructor/sections.php?course_id=" . (int)$courseId);
exit;
