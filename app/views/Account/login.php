<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Đăng nhập</h1>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($errors['login'])): ?>
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($errors['login']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/WebBanHang/account/checkLogin">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên tài khoản</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['username']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if (isset($errors['password'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['password']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-cute"><i class="fas fa-sign-in-alt me-2"></i>Đăng nhập</button>
                    <a href="/WebBanHang/account/register" class="btn btn-secondary-cute">Chưa có tài khoản? Đăng ký</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #ff6b81;
        box-shadow: 0 0 5px rgba(255, 107, 129, 0.5);
    }
    .form-label {
        color: #4a4a4a;
        font-weight: 500;
    }
</style>

<?php include 'app/views/shares/footer.php'; ?>