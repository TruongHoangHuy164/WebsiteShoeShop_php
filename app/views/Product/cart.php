<?php include 'app/views/shares/header.php'; ?>
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Giỏ hàng</h1>

<?php if (empty($cart)): ?>
    <div class="alert alert-info animate__animated animate__fadeIn">Giỏ hàng của bạn đang trống.</div>
    <a href="/WebBanHang/Product" class="btn btn-cute">Tiếp tục mua sắm</a>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                <table class="tableведение
table table-hover">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach ($cart as $id => $item): ?>
                            <tr data-product-id="<?php echo $id; ?>">
                                <td>
                                    <img src="/WebBanHang/<?php echo htmlspecialchars($item['image'] ?: 'uploads/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <input type="number" class="form-control quantity-input" style="width: 80px;" min="0" value="<?php echo $item['quantity']; ?>" data-product-id="<?php echo $id; ?>">
                                </td>
                                <td class="subtotal"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <a href="/WebBanHang/Product/removeFromCart/<?php echo $id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php $total += $item['price'] * $item['quantity']; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-3">
                    <h5>Tổng cộng: <span id="cart-total"><?php echo number_format($total, 0, ',', '.'); ?></span> VNĐ</h5>
                    <div>
                        <a href="/WebBanHang/Product" class="btn btn-secondary-cute me-2">Tiếp tục mua sắm</a>
                        <a href="/WebBanHang/Product/checkout" class="btn btn-cute"><i class="fas fa-credit-card me-2"></i>Thanh toán</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .table img {
        transition: transform 0.3s ease;
    }
    .table img:hover {
        transform: scale(1.1);
    }
    .quantity-input {
        display: inline-block;
    }
</style>
<script>
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);

            // Gửi yêu cầu AJAX để cập nhật số lượng
            fetch('/WebBanHang/Product/updateCartQuantity', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.redirect) {
                        // Nếu giỏ hàng rỗng, chuyển hướng về trang chủ
                        window.location.href = '/WebBanHang/Product';
                    } else {
                        // Cập nhật tổng tiền của sản phẩm
                        const row = this.closest('tr');
                        const price = parseFloat(<?php echo json_encode($cart); ?>[productId].price);
                        const subtotal = price * quantity;
                        row.querySelector('.subtotal').textContent = subtotal.toLocaleString('vi-VN') + ' VNĐ';

                        // Cập nhật tổng cộng của giỏ hàng
                        document.getElementById('cart-total').textContent = data.total.toLocaleString('vi-VN') + ' VNĐ';
                    }
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi cập nhật số lượng.');
                    this.value = <?php echo json_encode($cart); ?>[productId].quantity; // Khôi phục số lượng cũ
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kết nối đến server.');
                this.value = <?php echo json_encode($cart); ?>[productId].quantity; // Khôi phục số lượng cũ
            });
        });
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>