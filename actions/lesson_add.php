<?php
// Hata görmeyi kolaylaştıralım (geliştirme aşaması)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/auth_check.php';
require_role('instructor');
require '../config/db.php';

$section_id = isset($_POST['section_id']) ? (int)$_POST['section_id'] : 0;
$title      = $_POST['title'] ?? '';
$content    = $_POST['content'] ?? '';
$video_url  = $_POST['video_url'] ?? '';
$position   = isset($_POST['position']) ? (int)$_POST['position'] : 1;

if ($section_id <= 0 || trim($title) === '') {
    echo "Zorunlu alanlar eksik.";
    exit;
}

// Bu section gerçekten bu eğitmene mi ait?
$sql = "SELECT s.*, c.instructor_id 
        FROM sections s
        JOIN courses c ON s.course_id = c.id
        WHERE s.id = :section_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['section_id' => $section_id]);
$section = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$section || (int)$section['instructor_id'] !== (int)($_SESSION['user_id'] ?? 0)) {
    echo "Bu bölüme ders ekleme yetkiniz yok.";
    exit;
}

try {
    $sqlInsert = "INSERT INTO lessons (section_id, title, content, video_url, position, created_at)
                  VALUES (:section_id, :title, :content, :video_url, :position, NOW())";

    $stmtIns = $pdo->prepare($sqlInsert);
    $stmtIns->execute([
        'section_id' => $section_id,
        'title'      => $title,
        'content'    => $content,
        'video_url'  => $video_url,
        'position'   => $position,
    ]);

    header("Location: /online_course_platform/instructor/lessons.php?section_id=" . $section_id);
    exit;

} catch (PDOException $e) {
    echo "Veritabanı hatası: " . htmlspecialchars($e->getMessage());
    exit;
}
