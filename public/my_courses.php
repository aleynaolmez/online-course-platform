<?php
include '../includes/header.php';
include '../includes/auth_check.php';
require_login();
require_role('student');
require '../config/db.php';

$student_id = (int) $_SESSION['user_id'];

$sql = "SELECT c.*, 
               u.name AS instructor_name,
               cat.name AS category_name,
               e.enrolled_at
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        JOIN users u ON c.instructor_id = u.id
        JOIN categories cat ON c.category_id = cat.id
        WHERE e.student_id = :student_id
        ORDER BY e.enrolled_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['student_id' => $student_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Kayıtlı Kurslarım</h2>

<?php if (empty($courses)): ?>
    <p>Henüz herhangi bir kursa kayıt olmadınız.</p>
<?php else: ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Kurs</th>
                <th>Eğitmen</th>
                <th>Kategori</th>
                <th>Kayıt Tarihi</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?= htmlspecialchars($course['title']) ?></td>
                <td><?= htmlspecialchars($course['instructor_name']) ?></td>
                <td><?= htmlspecialchars($course['category_name']) ?></td>
                <td><?= htmlspecialchars($course['enrolled_at']) ?></td>
                <td>
                    <a href="course_detail.php?id=<?= $course['id'] ?>" class="btn btn-sm btn-primary">
                        Kursa Git
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
