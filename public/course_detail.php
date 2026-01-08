<?php
include '../includes/header.php';
require '../config/db.php';

$course_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($course_id <= 0) {
    echo "<p>Geçersiz kurs.</p>";
    include '../includes/footer.php';
    exit;
}

// Kurs bilgisi
$sql = "SELECT c.*,
               u.name AS instructor_name,
               cat.name AS category_name
        FROM courses c
        JOIN users u ON c.instructor_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE c.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    echo "<p>Kurs bulunamadı.</p>";
    include '../includes/footer.php';
    exit;
}

// Kapak görseli
$cover = 'https://via.placeholder.com/800x400?text=Kurs+Kapak';

if (!empty($course['cover_image'])) {
    $path = $course['cover_image'];

    if (strpos($path, 'http') === 0) {
        $cover = $path;
    } elseif (strpos($path, '/online_course_platform/') === 0) {
        $cover = $path;
    } else {
        $cover = '/online_course_platform/' . ltrim($path, '/');
    }
}

// Öğrenci kursa kayıtlı mı?
$isEnrolled = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
    $checkEnroll = $pdo->prepare("
        SELECT id FROM enrollments 
        WHERE course_id = :course_id AND student_id = :student_id
    ");
    $checkEnroll->execute([
        'course_id'  => $course_id,
        'student_id' => $_SESSION['user_id']
    ]);
    $isEnrolled = $checkEnroll->rowCount() > 0;
}

// Bölümler
$sectionsStmt = $pdo->prepare("
    SELECT * FROM sections
    WHERE course_id = :course_id
    ORDER BY sort_order ASC, id ASC
");
$sectionsStmt->execute(['course_id' => $course_id]);
$sections = $sectionsStmt->fetchAll(PDO::FETCH_ASSOC);

// 🔢 Toplam ders sayısı (bu kurs için)
$totalLessons = 0;
$totalLessonsStmt = $pdo->prepare("
    SELECT COUNT(*) AS total_lessons
    FROM lessons l
    JOIN sections s ON l.section_id = s.id
    WHERE s.course_id = :course_id
");
$totalLessonsStmt->execute(['course_id' => $course_id]);
$totalLessonsRow = $totalLessonsStmt->fetch(PDO::FETCH_ASSOC);
if ($totalLessonsRow) {
    $totalLessons = (int)$totalLessonsRow['total_lessons'];
}

// ✅ Öğrencinin tamamladığı dersler
$completedLessonIds = [];
$completedCount = 0;
$progressPercent = 0;

if ($isEnrolled && isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    $progStmt = $pdo->prepare("
        SELECT lesson_id 
        FROM lesson_progress
        WHERE student_id = :student_id AND course_id = :course_id
    ");
    $progStmt->execute([
        'student_id' => $_SESSION['user_id'],
        'course_id'  => $course_id
    ]);
    $completedLessonIds = $progStmt->fetchAll(PDO::FETCH_COLUMN);

    $completedCount = count($completedLessonIds);
    if ($totalLessons > 0) {
        $progressPercent = round($completedCount * 100 / $totalLessons);
    }
}

// Yorumlar
$reviewsStmt = $pdo->prepare("
    SELECT r.*, u.name AS student_name
    FROM reviews r
    JOIN users u ON r.student_id = u.id
    WHERE r.course_id = :course_id
    ORDER BY r.created_at DESC
");
$reviewsStmt->execute(['course_id' => $course_id]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-3"><?= htmlspecialchars($course['title']) ?></h2>

<div class="row mb-4">
    <!-- Kapak -->
    <div class="col-md-4 mb-3">
        <img src="<?= htmlspecialchars($cover) ?>" class="img-fluid rounded" alt="Kurs Kapak">
    </div>

    <!-- Bilgiler -->
    <div class="col-md-5">
        <p><strong>Kategori:</strong> <?= htmlspecialchars($course['category_name']) ?></p>
        <p><strong>Eğitmen:</strong> <?= htmlspecialchars($course['instructor_name']) ?></p>
        <p><strong>Seviye:</strong> <?= htmlspecialchars($course['level']) ?></p>
        <p><strong>Fiyat:</strong> <?= htmlspecialchars($course['price']) ?> ₺</p>
        <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

        <?php if ($isEnrolled && $totalLessons > 0 && $_SESSION['role'] === 'student'): ?>
            <div class="mt-3">
                <label class="form-label" id="courseProgressLabel">
                    İlerleme: <?= $completedCount ?>/<?= $totalLessons ?> ders (%<?= $progressPercent ?>)
                </label>
                <div class="progress">
                    <div id="courseProgressBar"
                         class="progress-bar"
                         role="progressbar"
                         style="width: <?= $progressPercent ?>%;"
                         aria-valuenow="<?= $progressPercent ?>"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        %<?= $progressPercent ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Kayıt durumu -->
    <div class="col-md-3">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
            <?php if ($isEnrolled): ?>
                <div class="alert alert-success">
                    Bu kursa kayıtlısınız.
                </div>
            <?php else: ?>
                <form action="../actions/enroll_course.php" method="post">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <button type="submit" class="btn btn-success w-100 mb-2">
                        Kursa Katıl
                    </button>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                Kursa kayıt olmak için lütfen giriş yapın.
            </div>
        <?php endif; ?>
    </div>
</div>

<hr>

<h4>Kurs İçeriği</h4>

<?php if (empty($sections)): ?>
    <p>Bu kurs için henüz bölüm eklenmemiş.</p>
<?php else: ?>
    <div class="accordion" id="sectionsAccordion">
        <?php foreach ($sections as $index => $section): ?>
            <?php
            // Her bölüm için dersler
            $lessonsStmt = $pdo->prepare("
                SELECT * FROM lessons
                WHERE section_id = :section_id
                ORDER BY sort_order ASC, id ASC
            ");
            $lessonsStmt->execute(['section_id' => $section['id']]);
            $lessons = $lessonsStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?= $section['id'] ?>">
                    <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse<?= $section['id'] ?>">
                        <?= htmlspecialchars($section['title']) ?>
                    </button>
                </h2>
                <div id="collapse<?= $section['id'] ?>"
                     class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                     data-bs-parent="#sectionsAccordion">
                    <div class="accordion-body">
                        <?php if (empty($lessons)): ?>
                            <p>Bu bölümde henüz ders yok.</p>
                        <?php else: ?>
                            <ul class="list-unstyled">
                                <?php foreach ($lessons as $lesson): ?>
                                    <li class="d-flex align-items-center justify-content-between mb-1">
                                        <!-- Ders adı: modal açıyor -->
                                        <button type="button"
                                                class="btn btn-link p-0 lesson-preview-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#lessonPreviewModal"
                                                data-title="<?= htmlspecialchars($lesson['title']) ?>"
                                                data-content="<?= htmlspecialchars($lesson['content'] ?? '') ?>"
                                                data-video="<?= htmlspecialchars($lesson['video_url'] ?? '') ?>">
                                            <?= htmlspecialchars($lesson['title']) ?>
                                        </button>

                                        <!-- Öğrenci ise Tamamla / Tamamlandı butonu -->
                                        <?php if ($isEnrolled && isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
                                            <form action="../actions/toggle_lesson_progress.php"
                                                  method="post"
                                                  class="lesson-progress-form ms-2">
                                                <input type="hidden" name="course_id" value="<?= $course_id ?>">
                                                <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">

                                                <?php $done = in_array($lesson['id'], $completedLessonIds); ?>

                                                <button type="submit"
                                                        class="btn btn-sm <?= $done ? 'btn-success' : 'btn-outline-secondary' ?>">
                                                    <?= $done ? 'Tamamlandı' : 'Tamamla' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Ders Önizleme Modalı -->
<div class="modal fade" id="lessonPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ders Önizleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <h5 class="mb-2" id="lessonPreviewTitle"></h5>
                <p id="lessonPreviewContent"></p>
                <div id="lessonPreviewVideo" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<hr>

<h4>Yorumlar</h4>

<?php if ($isEnrolled && isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
    <form action="../actions/add_review.php" method="post" class="mb-4 col-md-6">
        <input type="hidden" name="course_id" value="<?= $course_id ?>">

        <div class="mb-2">
            <label class="form-label">Puan (1–5)</label>
            <select name="rating" class="form-select" required>
                <option value="5">5 - Harika</option>
                <option value="4">4 - İyi</option>
                <option value="3">3 - Orta</option>
                <option value="2">2 - Kötü</option>
                <option value="1">1 - Çok kötü</option>
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label">Yorum</label>
            <textarea name="comment" rows="3" class="form-control" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Yorumu Gönder / Güncelle</button>
    </form>
<?php else: ?>
    <p class="text-muted">Yorum yapabilmek için bu kursa kayıtlı öğrenci olmanız gerekir.</p>
<?php endif; ?>

<?php if (empty($reviews)): ?>
    <p>Bu kurs için henüz yorum yapılmamış.</p>
<?php else: ?>
    <?php foreach ($reviews as $review): ?>
        <div class="mb-3 border rounded p-2">
            <strong><?= htmlspecialchars($review['student_name']) ?></strong>
            <span class="badge bg-warning text-dark ms-2">
                <?= (int)$review['rating'] ?>/5
            </span>
            <p class="mb-0"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
            <small class="text-muted"><?= $review['created_at'] ?></small>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
