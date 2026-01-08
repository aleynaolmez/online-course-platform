<?php include '../includes/header.php'; ?>

<h2 class="mb-3">Kayıt Ol</h2>

<form action="../actions/register_action.php" method="post" class="col-md-4">
    <div class="mb-3">
        <label for="name" class="form-label">Ad Soyad</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">E-posta</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Şifre</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Kayıt Ol</button>
</form>

<?php include '../includes/footer.php'; ?>
