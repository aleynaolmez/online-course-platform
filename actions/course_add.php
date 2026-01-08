
<?php
// Hata ayarları (debug için, proje bittiğinde silebilirsin)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../includes/auth_check.php';
require_role('instructor');
require '../config/db.php';

$title         = $_POST['title']        ?? '';
$description   = $_POST['description']  ?? '';
$price         = $_POST['price']        ?? '0';
$level         = $_POST['level']        ?? '';
$category_id   = $_POST['category_id']  ?? null;
$instructor_id = $_SESSION['user_id']   ?? null;

// ------------------------
// 1) Basit doğrulama
// ------------------------
if (trim($title) === '' || $category_id === null || trim($level) === '') {
    echo "Zorunlu alanlar eksik.";
    exit;
}

$price = (float) $price;

// ------------------------
// 2) Kapak görseli upload
// ------------------------
$coverPath = null; // Varsayılan: kapak yok

if (!empty($_FILES['cover_image']['name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {

    // Fiziksel klasör (sunucudaki gerçek yol)
    $uploadDir = __DIR__ . '/../uploads/courses/';

    // Klasör yoksa oluştur
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Dosya uzantısı
    $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));

    // Güvenlik için sadece belli uzantılara izin verebilirsin (opsiyonel)
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        echo "Sadece resim dosyalarına izin veriliyor (jpg, png, gif, webp).";
        exit;
    }

    // Benzersiz dosya adı
    $newName  = 'course_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $fullPath = $uploadDir . $newName;

    // Geçici dosyadan kalıcı yere taşı
    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $fullPath)) {
        // Tarayıcıda kullanılacak WEB PATH:
        // http://localhost/online_course_platform/uploads/courses/....
        $coverPath = '/online_course_platform/uploads/courses/' . $newName;
    } else {
        echo "Kapak görseli yüklenirken bir hata oluştu.";
        exit;
    }
}

// ------------------------
// 3) Veritabanına kaydet
// ------------------------

// courses tablosunda cover_image kolonu olduğunu varsayıyoruz:
// ALTER TABLE courses ADD cover_image VARCHAR(255) NULL AFTER description;
$sql = "INSERT INTO courses 
            (title, description, cover_image, price, level, category_id, instructor_id, is_active, created_at)
        VALUES 
            (:title, :description, :cover_image, :price, :level, :category_id, :instructor_id, 1, NOW())";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title'         => $title,
        'description'   => $description,
        'cover_image'   => $coverPath,          // 🔴 ÖNEMLİ: URL buraya yazılıyor
        'price'         => $price,
        'level'         => $level,
        'category_id'   => (int) $category_id,
        'instructor_id' => (int) $instructor_id,
    ]);

    // Başarılıysa eğitmen kurs listesine dön
    header("Location: /online_course_platform/instructor/courses.php");
    exit;

} catch (PDOException $e) {
    // Geçici debug çıktısı
    echo "Veritabanı hatası: " . htmlspecialchars($e->getMessage());
    exit;
}
