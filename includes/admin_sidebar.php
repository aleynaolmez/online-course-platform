<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="list-group mb-4">
    <a href="/online_course_platform/admin/dashboard.php" 
       class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
        Dashboard
    </a>
    <a href="/online_course_platform/admin/courses.php" 
       class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) === 'courses.php' ? 'active' : '' ?>">
        Kurslar
    </a>
    <a href="/online_course_platform/admin/users.php" 
       class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
        Kullanıcılar
    </a>
    <a href="/online_course_platform/admin/categories.php" 
       class="list-group-item list-group-item-action <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>">
        Kategoriler
    </a>
    <a href="/online_course_platform/actions/logout.php" 
       class="list-group-item list-group-item-action">
        Çıkış
    </a>
</div>
