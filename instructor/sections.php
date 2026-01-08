<?php
// instructor/sections.php

include '../includes/header.php';
include '../includes/auth_check.php';
require_login();
require_role('instructor');
require '../config/db.php';

// 1) Hangi kursun bölümlerini gösterdiğimizi al
if (!isset($_GET['course_id'])) {
    echo "course_id parametresi eksik.";
    include '../includes/footer.php';
    exit;
}

$course_id = (int) $_GET['course_id'];

// 2) Kurs bilgisi (başlığı göstermek için)
$courseStmt = $pdo->prepare("SELECT * FROM courses WHERE id = :id");
$courseStmt->execute(['id' => $course_id]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    echo "Kurs bulunamadı.";
    include '../includes/footer.php';
    exit;
}

// 3) Yeni bölüm ekleme (form POST ise)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $sort_order = (int) ($_POST['sort_order'] ?? 0);

    $insert = $pdo->prepare("
        INSERT INTO sections (course_id, title, sort_order)
        VALUES (:course_id, :title, :sort_order)
    ");
    $insert->execute([
        'course_id'  => $course_id,
        'title'      => $title,
        'sort_order' => $sort_order
    ]);
}

// 4) Var olan bölümleri listele
$sectionsStmt = $pdo->prepare("
    SELECT * FROM sections 
    WHERE course_id = :course_id
    ORDER BY sort_order ASC, id ASC
");
$sectionsStmt->execute(['course_id' => $course_id]);
$sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Bölümler - <?= htmlspecialchars($course['title']) ?></h2>

<form method="post" class="mb-4 col-md-5">
    <div class="mb-3">
        <label class="form-label">Bölüm Başlığı</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Sıra</label>
        <input type="number" name="sort_order" class="form-control" value="1">
    </div>
    <button type="submit" class="btn btn-success">Ekle</button>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Sıra</th>
            <th>Başlık</th>
            <th>İşlem</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sections as $section): ?>
            <tr>
                <td><?= htmlspecialchars($section['sort_order']) ?></td>
                <td><?= htmlspecialchars($section['title']) ?></td>
                <td>
                    <!-- BURASI ÖNEMLİ: Dersler linki -->
                    <a href="lessons.php?section_id=<?= $section['id'] ?>" class="btn btn-primary btn-sm">
                        Dersler
                    </a>
                    <!-- Düzenle butonunu sonra yaparız -->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p><a href="courses.php">&larr; Kurslarıma Dön</a></p>

<?php include '../includes/footer.php'; ?>
