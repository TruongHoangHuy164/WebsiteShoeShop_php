<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Thêm người dùng mới</h1>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="/WebBanHang/account/addUser" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="username" class="form-label">Tên tài khoản</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    <?php if (isset($errors['username'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['username']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Họ và tên</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                    <?php if (isset($errors['fullname'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['fullname']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <?php if (isset($errors['email'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['email']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['phone']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="avatar" class="form-label">Avatar</label>
                    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/jpeg,image/png,image/gif">
                    <?php if (isset($errors['avatar'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['avatar']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <?php if (isset($errors['password'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['password']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="confirmpassword" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" class="form-control" required>
                    <?php if (isset($errors['confirmPass'])): ?>
                        <small class="text-danger"><?php echo htmlspecialchars($errors['confirmPass']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Vai trò</label>
                    <select id="role" name="role" class="form-select">
                        <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] === 'user') ? 'selected' : ''; ?>>Người dùng</option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Quản trị viên</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-cute"><i class="fas fa-user-plus me-2"></i>Thêm người dùng</button>
                    <a href="/WebBanHang/account/listUsers" class="btn btn-secondary-cute">Quay lại danh sách</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>