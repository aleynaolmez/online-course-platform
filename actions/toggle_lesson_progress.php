<?php
// Debug için (istersen sonra kapat)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/auth_check.php';
require_role('student');
require '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Giriş yapmanız gerekiyor.'
    ]);
    exit;
}

$studentId = (int) $_SESSION['user_id'];
$courseId  = isset($_POST['course_id']) ? (int) $_POST['course_id'] : 0;
$lessonId  = isset($_POST['lesson_id']) ? (int) $_POST['lesson_id'] : 0;

if ($courseId <= 0 || $lessonId <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Geçersiz ders veya kurs bilgisi.'
    ]);
    exit;
}

try {
    // 1) Bu ders için kayıt var mı?  (BURADA student_id KULLANIYORUZ!)
    $check = $pdo->prepare("
        SELECT id 
        FROM lesson_progress
        WHERE student_id = :student_id
          AND course_id  = :course_id
          AND lesson_id  = :lesson_id
    ");
    $check->execute([
        'student_id' => $studentId,
        'course_id'  => $courseId,
        'lesson_id'  => $lessonId,
    ]);

    if ($check->rowCount() > 0) {
        // Varsa -> sil = TAMAMLANMADI
        $row = $check->fetch(PDO::FETCH_ASSOC);

        $delete = $pdo->prepare("DELETE FROM lesson_progress WHERE id = :id");
        $delete->execute(['id' => $row['id']]);

        $isCompleted = false;
    } else {
        // Yoksa -> ekle = TAMAMLANDI
        $insert = $pdo->prepare("
            INSERT INTO lesson_progress (student_id, course_id, lesson_id, completed_at)
            VALUES (:student_id, :course_id, :lesson_id, NOW())
        ");
        $insert->execute([
            'student_id' => $studentId,
            'course_id'  => $courseId,
            'lesson_id'  => $lessonId,
        ]);

        $isCompleted = true;
    }

    // 2) Toplam ders sayısı
    $totalStmt = $pdo->prepare("
        SELECT COUNT(*) AS total_lessons
        FROM lessons l
        JOIN sections s ON l.section_id = s.id
        WHERE s.course_id = :course_id
    ");
    $totalStmt->execute(['course_id' => $courseId]);
    $totalRow     = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalLessons = (int)($totalRow['total_lessons'] ?? 0);

    // 3) Öğrencinin tamamladığı ders sayısı
    $completedStmt = $pdo->prepare("
        SELECT COUNT(*) AS completed_count
        FROM lesson_progress
        WHERE student_id = :student_id
          AND course_id  = :course_id
    ");
    $completedStmt->execute([
        'student_id' => $studentId,
        'course_id'  => $courseId,
    ]);
    $completedRow   = $completedStmt->fetch(PDO::FETCH_ASSOC);
    $completedCount = (int)($completedRow['completed_count'] ?? 0);

    $percent = 0;
    if ($totalLessons > 0) {
        $percent = round($completedCount * 100 / $totalLessons);
    }

    echo json_encode([
        'status'          => 'ok',
        'completed'       => $isCompleted,
        'completedCount'  => $completedCount,
        'totalLessons'    => $totalLessons,
        'progressPercent' => $percent,
    ]);
    exit;

} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage(),
    ]);
    exit;
}
