<?php
session_start();
require '../includes/auth_check.php';
require_login();
require_role('student');
require '../config/db.php';

if (!isset($_POST['course_id'], $_POST['rating'], $_POST['comment'])) {
    die("Geçersiz istek.");
}

$course_id = (int) $_POST['course_id'];
$student_id = (int) $_SESSION['user_id'];
$rating = (int) $_POST['rating'];
$comment = trim($_POST['comment']);

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

// Bu kursa kayıtlı mı?
$checkEnroll = $pdo->prepare("
    SELECT id FROM enrollments
    WHERE course_id = :course_id AND student_id = :student_id
");
$checkEnroll->execute([
    'course_id'  => $course_id,
    'student_id' => $student_id
]);

if ($checkEnroll->rowCount() === 0) {
    die("Bu kursa kayıtlı değilsiniz.");
}

// Daha önce yorum yapmış mı? (her öğrenci kurs başına 1 yorum)
$checkReview = $pdo->prepare("
    SELECT id FROM reviews
    WHERE course_id = :course_id AND student_id = :student_id
");
$checkReview->execute([
    'course_id'  => $course_id,
    'student_id' => $student_id
]);

if ($checkReview->rowCount() > 0) {
    // Güncelle
    $review = $checkReview->fetch(PDO::FETCH_ASSOC);
    $update = $pdo->prepare("
        UPDATE reviews 
        SET rating = :rating, comment = :comment, created_at = NOW()
        WHERE id = :id
    ");
    $update->execute([
        'rating'  => $rating,
        'comment' => $comment,
        'id'      => $review['id']
    ]);
} else {
    // Yeni yorum
    $insert = $pdo->prepare("
        INSERT INTO reviews (course_id, student_id, rating, comment, created_at)
        VALUES (:course_id, :student_id, :rating, :comment, NOW())
    ");
    $insert->execute([
        'course_id'  => $course_id,
        'student_id' => $student_id,
        'rating'     => $rating,
        'comment'    => $comment
    ]);
}

header("Location: ../public/course_detail.php?id=" . $course_id);
exit;
