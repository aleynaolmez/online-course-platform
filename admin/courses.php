<?php
// Yetki kontrolü ve DB bağlantısı
include '../includes/auth_check.php';
require_login();
require_role('admin');
require '../config/db.php';

// Kurs onayla/pasif yap işlemleri (REDIRECT ÖNCESİ)
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($id > 0) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE courses SET status = 'approved' WHERE id = :id");
            $stmt->execute(['id' => $id]);
        } elseif ($action === 'deactivate') {
            $stmt = $pdo->prepare("UPDATE courses SET status = 'inactive' WHERE id = :id");
            $stmt->execute(['id' => $id]);
        }
    }

    header('Location: courses.php');
    exit;
}

// Kurs listesini çek
$sql = "SELECT c.*, 
               u.name AS instructor_name,
               cat.name AS category_name
        FROM courses c
        JOIN users u ON c.instructor_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        ORDER BY c.created_at DESC";
$stmt = $pdo->query($sql);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Header (navbar + container açılışı)
include '../includes/header.php';
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-3">
        <?php include '../includes/admin_sidebar.php'; ?>
    </div>

    <!-- İçerik -->
    <div class="col-md-9">
        <h2 class="mb-4">Kurs Yönetimi</h2>

        <?php if (empty($courses)): ?>
            <p>Henüz kurs bulunmamaktadır.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Eğitmen</th>
                            <th>Kategori</th>
                            <th>Durum</th>
                            <th>Oluşturma</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?= $course['id'] ?></td>
                                <td><?= htmlspecialchars($course['title']) ?></td>
                                <td><?= htmlspecialchars($course['instructor_name']) ?></td>
                                <td><?= htmlspecialchars($course['category_name']) ?></td>
                                <td>
                                    <?php
                                    $status = $course['status'];
                                    $badgeClass = 'secondary';
                                    if ($status === 'approved') $badgeClass = 'success';
                                    elseif ($status === 'inactive') $badgeClass = 'danger';
                                    elseif ($status === 'pending') $badgeClass = 'warning';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                                <td><?= $course['created_at'] ?></td>
                                <td>
                                    <?php if ($course['status'] === 'approved'): ?>
                                        <a href="courses.php?action=deactivate&id=<?= $course['id'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Kursu pasif yapmak istediğinize emin misiniz?');">
                                            Pasif Yap
                                        </a>
                                    <?php else: ?>
                                        <a href="courses.php?action=approve&id=<?= $course['id'] ?>"
                                           class="btn btn-sm btn-outline-success">
                                            Onayla
                                        </a>
                                    <?php endif; ?>

                                    <a href="../public/course_detail.php?id=<?= $course['id'] ?>"
                                       class="btn btn-sm btn-outline-primary ms-1"
                                       target="_blank">
                                        Görüntüle
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
