<?php
include '../includes/auth_check.php';
require_login();
require_role('admin');
require '../config/db.php';

// Kategori ekleme / güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');

    if ($name !== '') {
        if ($id > 0) {
            // Güncelle
            $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'id'   => $id
            ]);
        } else {
            // Ekle
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->execute(['name' => $name]);
        }
    }

    header('Location: categories.php');
    exit;
}

// Silme
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    if ($deleteId > 0) {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $deleteId]);
    }
    header('Location: categories.php');
    exit;
}

// Düzenleme için tek kayıt çek
$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $editId]);
        $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Tüm kategoriler
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-3">
        <?php include '../includes/admin_sidebar.php'; ?>
    </div>

    <!-- İçerik -->
    <div class="col-md-9">
        <h2 class="mb-4">Kategori Yönetimi</h2>

        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="card">
                    <div class="card-header">
                        <?= $editCategory ? 'Kategoriyi Düzenle' : 'Yeni Kategori Ekle' ?>
                    </div>
                    <div class="card-body">
                        <form action="categories.php" method="post">
                            <?php if ($editCategory): ?>
                                <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label">Kategori Adı</label>
                                <input type="text" name="name" class="form-control"
                                       value="<?= $editCategory ? htmlspecialchars($editCategory['name']) : '' ?>"
                                       required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <?= $editCategory ? 'Güncelle' : 'Ekle' ?>
                            </button>

                            <?php if ($editCategory): ?>
                                <a href="categories.php" class="btn btn-secondary ms-2">Vazgeç</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-4">
                <div class="card">
                    <div class="card-header">
                        Kategoriler
                    </div>
                    <div class="card-body">
                        <?php if (empty($categories)): ?>
                            <p>Henüz kategori bulunmamaktadır.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ad</th>
                                            <th>İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $cat): ?>
                                            <tr>
                                                <td><?= $cat['id'] ?></td>
                                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                                <td>
                                                    <a href="categories.php?edit=<?= $cat['id'] ?>"
                                                       class="btn btn-sm btn-outline-primary">
                                                        Düzenle
                                                    </a>
                                                    <a href="categories.php?delete=<?= $cat['id'] ?>"
                                                       class="btn btn-sm btn-outline-danger ms-1"
                                                       onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                                        Sil
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
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
