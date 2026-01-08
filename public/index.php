<?php
include '../includes/header.php';
require '../config/db.php';

// Kategori listesini al
$catSql = "SELECT id, name FROM categories ORDER BY name ASC";
$catStmt = $pdo->query($catSql);
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Kursları al
$sql = "SELECT c.*, 
               u.name AS instructor_name,
               cat.name AS category_name,
               (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS student_count,
               (SELECT AVG(rating) FROM reviews r WHERE r.course_id = c.id) AS avg_rating
        FROM courses c
        JOIN users u ON c.instructor_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        ORDER BY c.created_at DESC";

$stmt = $pdo->query($sql);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Online Kurs Platformu</h1>

<!-- Modern Hero Slider -->
<!-- Modern Hero Slider -->
<div id="modernSlider" class="carousel slide carousel-fade mb-4" data-bs-ride="carousel">

    <div class="carousel-indicators">
        <button type="button" data-bs-target="#modernSlider" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#modernSlider" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#modernSlider" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">

        <!-- SLIDE 1 -->
        <div class="carousel-item active">
            <div class="slider-bg" style="background-image: url('uploads/course_web.jpg');">
                <div class="slider-overlay"></div>

                <div class="slider-content text-white">
                    <h1 class="fw-bold display-5">Kariyerine Yön Verecek Kurslar</h1>
                    <p class="lead">Web geliştirme, veri bilimi ve çok daha fazlası senin için hazır.</p>

                    <a href="#coursesSection" class="btn-slider mt-3">
                        ⭐ Kursları Keşfet
                    </a>
                </div>
            </div>
        </div>

        <!-- SLIDE 2 -->
        <div class="carousel-item">
            <div class="slider-bg" style="background-image: url('uploads/course_python.jpg');">
                <div class="slider-overlay"></div>

                <div class="slider-content text-white">
                    <h1 class="fw-bold display-5">Gerçek Projelerle Öğren</h1>
                    <p class="lead">Modern eğitim sistemi ile adım adım ilerle.</p>

                    <a href="#coursesSection" class="btn-slider mt-3">
                        ⭐ Kursları Keşfet
                    </a>
                </div>
            </div>
        </div>

        <!-- SLIDE 3 -->
        <div class="carousel-item">
            <div class="slider-bg" style="background-image: url('uploads/course_flutter.jpg');">
                <div class="slider-overlay"></div>

                <div class="slider-content text-white">
                    <h1 class="fw-bold display-5">Uzman Eğitmenlerden Dersler</h1>
                    <p class="lead">Kendini geliştir, en iyi versiyonuna ulaş.</p>

                    <a href="#coursesSection" class="btn-slider mt-3">
                        ⭐ Kursları Keşfet
                    </a>
                </div>
            </div>
        </div>

    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#modernSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#modernSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Arama + kategori filtresi -->
<div class="row mb-3">
    <div class="col-md-6 mb-2">
        <input type="text" id="courseSearch" class="form-control" placeholder="Kurs ara...">
    </div>
    <div class="col-md-4 mb-2">
        <select id="categoryFilter" class="form-select">
            <option value="">Tüm Kategoriler</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<?php if (empty($courses)): ?>
    <p>Henüz sistemde kurs bulunmuyor.</p>
<?php else: ?>
    <div class="row" id="coursesSection">
        <?php foreach ($courses as $course): ?>
            ...

        ...

            <?php
                // Ortalama puan
                $avgRating = $course['avg_rating'] ? number_format($course['avg_rating'], 1) : null;

                // Kapak görseli (DB'de uploads/... şeklinde tutuluyor)
                // Kapak görseli yolu
if (!empty($course['cover_image'])) {
    // DB: uploads/course_web.jpg
    // index.php konumu: /online_course_platform/public/
    // doğru görüntü yolu: /online_course_platform/uploads/course_web.jpg
    $relativePath = ltrim($course['cover_image'], '/'); 
    $cover = '../' . $relativePath;   // ../uploads/course_web.jpg
} else {
    $cover = 'https://via.placeholder.com/600x300?text=Kurs+Kapak';
}

            ?>

            <div class="col-md-4 mb-4 course-card"
                 data-title="<?= strtolower(htmlspecialchars($course['title'])) ?>"
                 data-category="<?= (int)$course['category_id'] ?>">

                <div class="card h-100">
                    <img src="<?= htmlspecialchars($cover) ?>" class="card-img-top" alt="Kapak">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">
                            <?= htmlspecialchars($course['title']) ?>
                        </h5>

                        <p class="card-text small text-muted mb-1">
                            Eğitmen: <?= htmlspecialchars($course['instructor_name']) ?>
                        </p>
                        <p class="card-text small text-muted mb-1">
                            Kategori: <?= htmlspecialchars($course['category_name']) ?>
                        </p>

                        <p class="card-text">
                            <?= nl2br(htmlspecialchars(mb_substr($course['description'], 0, 120))) ?>...
                        </p>

                        <p class="mt-auto mb-1">
                            <strong>Seviye:</strong> <?= htmlspecialchars($course['level']) ?><br>
                            <strong>Fiyat:</strong> <?= htmlspecialchars($course['price']) ?> ₺
                        </p>

                        <p class="small text-muted mb-2">
                            Öğrenci: <?= (int)$course['student_count'] ?> |
                            Puan: <?= $avgRating !== null ? $avgRating : '-' ?>/5
                        </p>

                        <!-- Butonlar -->
                        <div class="d-flex gap-2">
                            <!-- Önizleme butonu (modal açacak) -->
                            <button type="button"
                                    class="btn btn-outline-secondary w-50 btn-preview-course"
                                    data-title="<?= htmlspecialchars($course['title']) ?>"
                                    data-description="<?= htmlspecialchars($course['description']) ?>"
                                    data-image="<?= htmlspecialchars($cover) ?>"
                                    data-instructor="<?= htmlspecialchars($course['instructor_name']) ?>"
                                    data-category="<?= htmlspecialchars($course['category_name']) ?>"
                                    data-level="<?= htmlspecialchars($course['level']) ?>"
                                    data-price="<?= htmlspecialchars($course['price']) ?>"
                                    data-rating="<?= $avgRating !== null ? $avgRating : '-' ?>">
                                Önizle
                            </button>

                            <!-- Detay sayfasına giden buton -->
                            <a href="course_detail.php?id=<?= $course['id'] ?>"
                               class="btn btn-primary w-50">
                                Kursu Görüntüle
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- 🔹 Kurs Önizleme Modalı -->
<div class="modal fade" id="coursePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Kurs Önizleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <!-- Sol: Görsel -->
                    <div class="col-md-5 mb-3">
                        <img id="previewImage" src="" alt="Kurs Kapak" class="img-fluid rounded">
                    </div>

                    <!-- Sağ: Bilgiler -->
                    <div class="col-md-7">
                        <p class="mb-1"><strong>Eğitmen:</strong> <span id="previewInstructor"></span></p>
                        <p class="mb-1"><strong>Kategori:</strong> <span id="previewCategory"></span></p>
                        <p class="mb-1"><strong>Seviye:</strong> <span id="previewLevel"></span></p>
                        <p class="mb-1"><strong>Fiyat:</strong> <span id="previewPrice"></span> ₺</p>
                        <p class="mb-3"><strong>Puan:</strong> <span id="previewRating"></span>/5</p>

                        <p id="previewDescription" class="small text-muted"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Kapat
                </button>
            </div>

        </div>
    </div>
</div>



<?php include '../includes/footer.php'; ?>
