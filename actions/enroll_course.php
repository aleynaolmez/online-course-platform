<?php
session_start();
require '../includes/auth_check.php';
require_login();
require_role('student');
require '../config/db.php';

if (!isset($_POST['course_id'])) {
    die("Geçersiz istek.");
}

$course_id = (int) $_POST['course_id'];
$student_id = (int) $_SESSION['user_id'];

// Zaten kayıtlı mı kontrol et
$check = $pdo->prepare("
    SELECT id FROM enrollments
    WHERE course_id = :course_id AND student_id = :student_id
");
$check->execute([
    'course_id'  => $course_id,
    'student_id' => $student_id
]);

if ($check->rowCount() === 0) {
    // Kayıt ekle
    $insert = $pdo->prepare("
        INSERT INTO enrollments (course_id, student_id, enrolled_at)
        VALUES (:course_id, :student_id, NOW())
    ");
    $insert->execute([
        'course_id'  => $course_id,
        'student_id' => $student_id
    ]);
}

// Kurs detay sayfasına geri dön
header("Location: ../public/course_detail.php?id=" . $course_id);
exit;
