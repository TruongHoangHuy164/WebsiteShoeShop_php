<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Thanh toán</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate__animated animate__fadeIn">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <h5 class="mb-3">Thông tin thanh toán</h5>
            <form method="POST" action="/WebBanHang/Product/processCheckout" onsubmit="return validateForm();">
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email (không bắt buộc)</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ giao hàng</label>
                    <textarea id="address" name="address" class="form-control" rows="4" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú (không bắt buộc)</label>
                    <textarea id="note" name="note" class="form-control" rows="3"><?php echo isset($_POST['note']) ? htmlspecialchars($_POST['note']) : ''; ?></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-cute"><i class="fas fa-credit-card me-2"></i>Đặt hàng</button>
                    <a href="/WebBanHang/Product/cart" class="btn btn-secondary-cute">Quay lại giỏ hàng</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <h5 class="mb-3">Tóm tắt đơn hàng</h5>
            <?php if (!empty($cart)): ?>
                <ul class="list-group mb-3">
                    <?php $total = 0; ?>
                    <?php foreach ($cart as $id => $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <br>
                                <small>Số lượng: <?php echo $item['quantity']; ?></small>
                            </div>
                            <span><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</span>
                        </li>
                        <?php $total += $item['price'] * $item['quantity']; ?>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Tổng cộng</strong>
                        <strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong>
                    </li>
                </ul>
            <?php else: ?>
                <div class="alert alert-info">Giỏ hàng trống.</div>
            <?php endif; ?>
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
    .list-group-item {
        border: none;
        border-bottom: 1px solid #e9ecef;
    }
</style>
<script>
    function validateForm() {
        let name = document.getElementById('name').value.trim();
        let phone = document.getElementById('phone').value.trim();
        let address = document.getElementById('address').value.trim();
        let email = document.getElementById('email').value.trim();

        if (name.length < 3 || name.length > 100) {
            alert('Tên phải từ 3 đến 100 ký tự.');
            return false;
        }
        if (!/^[0-9]{10,11}$/.test(phone)) {
            alert('Số điện thoại phải có 10-11 chữ số.');
            return false;
        }
        if (address.length < 10) {
            alert('Địa chỉ phải ít nhất 10 ký tự.');
            return false;
        }
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            alert('Email không hợp lệ.');
            return false;
        }
        return true;
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>