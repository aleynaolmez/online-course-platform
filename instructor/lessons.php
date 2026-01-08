<?php
// instructor/lessons.php

include '../includes/header.php';
include '../includes/auth_check.php';
require_login();
require_role('instructor');
require '../config/db.php';

// 1) Hangi bölümün derslerini göstereceğiz?
if (!isset($_GET['section_id'])) {
    echo "section_id parametresi eksik.";
    include '../includes/footer.php';
    exit;
}

$section_id = (int) $_GET['section_id'];

// 2) Bölüm + Kurs bilgisi (başlık için)
$sectionStmt = $pdo->prepare("
    SELECT s.*, c.title AS course_title
    FROM sections s
    JOIN courses c ON s.course_id = c.id
    WHERE s.id = :id
");
$sectionStmt->execute(['id' => $section_id]);
$section = $sectionStmt->fetch(PDO::FETCH_ASSOC);

if (!$section) {
    echo "Bölüm bulunamadı.";
    include '../includes/footer.php';
    exit;
}

// 3) Yeni ders ekleme (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = $_POST['title'];
    $content   = $_POST['content'];
    $video_url = $_POST['video_url'] ?? null;
    $sort_order = (int) ($_POST['sort_order'] ?? 0);

    $insert = $pdo->prepare("
        INSERT INTO lessons (section_id, title, video_url, content, sort_order)
        VALUES (:section_id, :title, :video_url, :content, :sort_order)
    ");
    $insert->execute([
        'section_id' => $section_id,
        'title'      => $title,
        'video_url'  => $video_url,
        'content'    => $content,
        'sort_order' => $sort_order
    ]);
}

// 4) Mevcut dersleri listele
$lessonsStmt = $pdo->prepare("
    SELECT * FROM lessons
    WHERE section_id = :section_id
    ORDER BY sort_order ASC, id ASC
");
$lessonsStmt->execute(['section_id' => $section_id]);
$lessons = $lessonsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Dersler - <?= htmlspecialchars($section['course_title']) ?> / <?= htmlspecialchars($section['title']) ?></h2>

<form method="post" class="mb-4 col-md-6">
    <div class="mb-3">
        <label class="form-label">Ders Başlığı</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">İçerik / Açıklama</label>
        <textarea name="content" rows="4" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Video URL (opsiyonel)</label>
        <input type="text" name="video_url" class="form-control">
    </div>

    <div class="mb-3">
        <label class="form-label">Sıra</label>
        <input type="number" name="sort_order" class="form-control" value="1">
    </div>

    <button type="submit" class="btn btn-success">Ders Ekle</button>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Sıra</th>
            <th>Başlık</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lessons as $lesson): ?>
            <tr>
                <td><?= htmlspecialchars($lesson['sort_order']) ?></td>
                <td><?= htmlspecialchars($lesson['title']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p>
    <a href="sections.php?course_id=<?= $section['course_id'] ?>">&larr; Bölümlere Dön</a>
</p>

<?php include '../includes/footer.php'; ?>
