<?php include '../includes/header.php'; ?>

<h2 class="mb-3">Giriş Yap</h2>

<form action="../actions/login_action.php" method="post" class="col-md-4">
    <div class="mb-3">
        <label for="email" class="form-label">E-posta</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Şifre</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Giriş Yap</button>
</form>

<?php include '../includes/footer.php'; ?>
