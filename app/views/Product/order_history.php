<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Lịch sử đặt hàng</h1>

<?php if (empty($orders)): ?>
    <div class="alert alert-info animate__animated animate__fadeIn">Bạn chưa có đơn hàng nào.</div>
    <a href="/WebBanHang/Product" class="btn btn-cute">Tiếp tục mua sắm</a>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <?php foreach ($orders as $order): ?>
                <div class="card mb-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Đơn hàng #<?php echo $order->id; ?> - <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order->name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order->phone, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order->address, ENT_QUOTES, 'UTF-8'); ?></p>
                        <h6>Chi tiết đơn hàng:</h6>
                        <?php $details = $orderModel->getOrderDetails($order->id); ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; ?>
                                <?php foreach ($details as $detail): ?>
                                    <tr>
                                        <td>
                                            <img src="/WebBanHang/<?php echo htmlspecialchars($detail->product_image ?: 'uploads/placeholder.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        </td>
                                        <td><?php echo htmlspecialchars($detail->product_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo number_format($detail->price, 0, ',', '.'); ?> VNĐ</td>
                                        <td><?php echo $detail->quantity; ?></td>
                                        <td><?php echo number_format($detail->price * $detail->quantity, 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <?php $total += $detail->price * $detail->quantity; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end">
                            <h5>Tổng cộng: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ</h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }
</style>

<?php include 'app/views/shares/footer.php'; ?>