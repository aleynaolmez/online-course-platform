<?php
require '../includes/auth_check.php';
require_role('instructor');         // Eğitmen rolü zorunlu
require '../config/db.php';         // DB bağlantısı

include '../includes/header.php';   // Header daima en son gelir

// Kategorileri çek (sadece aktif olanlar)
$stmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Yeni Kurs Oluştur</h1>

<form action="/online_course_platform/actions/course_add.php"
      method="post"
      enctype="multipart/form-data">

    <div class="mb-3">
        <label class="form-label">Kurs Başlığı</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Açıklama</label>
        <textarea name="description" class="form-control" rows="4"></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Seviye</label>
        <select name="level" class="form-select" required>
            <option value="">Seviye Seçin</option>
            <option value="beginner">Başlangıç</option>
            <option value="intermediate">Orta</option>
            <option value="advanced">İleri</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Kategori</label>
        <select name="category_id" class="form-select" required>
            <option value="">Kategori Seçin</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id']; ?>">
                    <?= htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- 📌 YENİ EKLENEN: Kapak Görseli -->
    <div class="mb-3">
        <label class="form-label">Kapak Görseli (opsiyonel)</label>
        <input type="file" name="cover_image" class="form-control" accept="image/*">
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="/online_course_platform/instructor/courses.php" class="btn btn-secondary">İptal</a>
    </div>

</form>

<?php include '../includes/footer.php'; ?>
