<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Danh sách người dùng</h1>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <a href="/WebBanHang/account/addUser" class="btn btn-cute mb-3"><i class="fas fa-user-plus me-2"></i>Thêm người dùng</a>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên tài khoản</th>
                        <th>Họ và tên</th>
                        <th>Vai trò</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Avatar</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user->id); ?></td>
                            <td><?php echo htmlspecialchars($user->username); ?></td>
                            <td><?php echo htmlspecialchars($user->fullname); ?></td>
                            <td><?php echo htmlspecialchars($user->role); ?></td>
                            <td><?php echo htmlspecialchars($user->email ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($user->phone ?? ''); ?></td>
                            <td>
                                <?php if ($user->avatar): ?>
                                    <img src="/WebBanHang/<?php echo htmlspecialchars($user->avatar); ?>" alt="Avatar" style="max-width: 50px; border-radius: 5px;">
                                <?php else: ?>
                                    Không có
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/WebBanHang/account/editUser/<?php echo $user->id; ?>" class="btn btn-sm btn-cute"><i class="fas fa-edit"></i> Sửa</a>
                                <a href="/WebBanHang/account/deleteUser/<?php echo $user->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?');"><i class="fas fa-trash"></i> Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>