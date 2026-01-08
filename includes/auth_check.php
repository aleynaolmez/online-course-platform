<?php
// Her yerden çağrılabilsin diye, session açık mı kontrol
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kullanıcının giriş yapıp yapmadığını kontrol eder.
 * Giriş yoksa login sayfasına gönderir.
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /online_course_platform/public/login.php');
        exit;
    }
}

/**
 * Belirli bir role sahip mi kontrol eder.
 * Önce require_login() çağrılır.
 * Rol uyuşmazsa anasayfaya gönderir (istersen hata sayfasına da yönlendirebilirsin).
 */
function require_role(string $roleName) {
    require_login();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $roleName) {
        header('Location: /online_course_platform/public/index.php');
        exit;
    }
}
