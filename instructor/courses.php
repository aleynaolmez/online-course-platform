<?php
include '../includes/header.php';
include '../includes/auth_check.php';
require_role('instructor');
require '../config/db.php';

// O an giriş yapmış eğitmenin id'si
$instructorId = $_SESSION['user_id'];

// Eğitmenin kurslarını çek
$sql = "SELECT c.*, cat.name AS category_name
        FROM courses c
        LEFT JOIN categories cat ON c.category_id = cat.id
        WHERE c.instructor_id = :iid
        ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['iid' => $instructorId]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="mb-4">Kurslarım</h1>

<a href="course_add.php" class="btn btn-success mb-3">Yeni Kurs Oluştur</a>

<?php if (empty($courses)): ?>
    <div class="alert alert-info">
        Henüz hiç kurs oluşturmadınız.
    </div>
<?php else: ?>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Kategori</th>
                <th>Fiyat</th>
                <th>Seviye</th>
                <th>Durum</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['id']); ?></td>
                <td><?php echo htmlspecialchars($course['title']); ?></td>
                <td><?php echo htmlspecialchars($course['category_name'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($course['price']); ?> ₺</td>
                <td><?php echo htmlspecialchars($course['level']); ?></td>
                <td><?php echo $course['is_active'] ? 'Aktif' : 'Pasif'; ?></td>
                <td>
                    <a href="course_edit.php?id=<?php echo (int)$course['id']; ?>" class="btn btn-sm btn-primary">Düzenle</a>
                    <a href="sections.php?course_id=<?php echo (int)$course['id']; ?>" class="btn btn-sm btn-secondary">Bölümler</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
