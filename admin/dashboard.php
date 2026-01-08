<?php
include '../includes/header.php';
include '../includes/auth_check.php';
require_login();
require_role('admin');
require '../config/db.php';

// Basit sayılar
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalEnrollments = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$totalReviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
?>

<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 mb-3">
        <?php include '../includes/admin_sidebar.php'; ?>
    </div>

    <!-- İçerik -->
    <div class="col-md-9">
        <h2 class="mb-4">Admin Dashboard</h2>

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <h3><?= $totalUsers ?></h3>
                        <p class="mb-0">Kullanıcı</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h3><?= $totalCourses ?></h3>
                        <p class="mb-0">Kurs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-warning">
                    <div class="card-body">
                        <h3><?= $totalEnrollments ?></h3>
                        <p class="mb-0">Kayıt</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <h3><?= $totalReviews ?></h3>
                        <p class="mb-0">Yorum</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-4">
            <a href="courses.php" class="btn btn-primary">Kurs Yönetimine Git</a>
            <a href="users.php" class="btn btn-outline-secondary ms-2">Kullanıcılar</a>
            <a href="categories.php" class="btn btn-outline-secondary ms-2">Kategoriler</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
