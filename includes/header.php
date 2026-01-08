<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Online Kurs Platformu</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 🌈 Modern Slider CSS -->
    <style>
        .slider-bg {
            height: 420px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* Renkli gradient + koyu overlay */
        .slider-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                rgba(0, 112, 255, 0.50),
                rgba(0, 0, 0, 0.60)
            );
        }

        .slider-content {
            position: absolute;
            bottom: 90px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            max-width: 700px;
        }

        /* 🌈 Premium Purple Gradient Button */
        .btn-slider {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #fff !important;
            padding: 12px 30px;
            border-radius: 12px;
            font-size: 1.25rem;
            font-weight: 600;
            border: none;
            display: inline-block;
            transition: 0.25s ease-in-out;
            text-decoration: none;
            box-shadow: 0 0 14px rgba(0, 0, 0, 0.25);
        }

        .btn-slider:hover {
            transform: scale(1.08);
            box-shadow: 0 0 22px rgba(255, 255, 255, 0.45);
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded mb-4">
    <div class="container-fluid">

        <div class="ms-auto">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn btn-outline-primary me-2">Giriş Yap</a>
                <a href="register.php" class="btn btn-primary">Kayıt Ol</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn btn-outline-success me-2">Panel</a>
                <a href="logout.php" class="btn btn-danger">Çıkış</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

    <div class="container mt-4">
