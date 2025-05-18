<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Giỏ hàng</h1>

<?php if (empty($cart)): ?>
    <div class="alert alert-info animate__animated animate__fadeIn">Giỏ hàng của bạn đang trống.</div>
    <a href="/WebBanHang/Product" class="btn btn-cute">Tiếp tục mua sắm</a>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                <table class="table table-hover">
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
                            <tr>
                                <td>
                                    <img src="/WebBanHang/<?php echo htmlspecialchars($item['image'] ?: 'uploads/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <a href="/WebBanHang/Product/removeFromCart/<?php echo $id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php $total += $item['price'] * $item['quantity']; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-3">
                    <h5>Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</h5>
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
</style>

<?php include 'app/views/shares/footer.php'; ?>