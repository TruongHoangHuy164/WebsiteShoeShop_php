<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Chi tiết sản phẩm</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate__animated animate__fadeIn">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($product)): ?>
    <?php
    $errors = ['Sản phẩm không tìm thấy.'];
    include 'app/views/product/notfound.php';
    ?>
<?php else: ?>
    <div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); animation: fadeIn 0.5s ease-in;">
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($product->image)): ?>
                    <img src="/WebBanHang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" style="border-radius: 15px; max-height: 300px; object-fit: cover;">
                <?php else: ?>
                    <img src="/WebBanHang/uploads/placeholder.jpg" class="img-fluid" alt="No image" style="border-radius: 15px; max-height: 300px; object-fit: cover;">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h3><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Giá:</strong> <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ</p>
                <p><strong>Số lượng:</strong> 
                    <span class="<?php echo ($product->quantity ?? 0) <= 10 ? 'low-quantity' : ''; ?>">
                        <?php echo htmlspecialchars($product->quantity ?? '0', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </p>
                <p><strong>Danh mục:</strong> <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="d-flex gap-2">
                    <a href="/WebBanHang/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-cute"><i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ</a>
                    <a href="/WebBanHang/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning"><i class="fas fa-edit me-1"></i>Sửa</a>
                    <a href="/WebBanHang/Product" class="btn btn-secondary-cute">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }
    .img-fluid {
        transition: opacity 0.3s ease;
    }
    .img-fluid:hover {
        opacity: 0.9;
    }
    .low-quantity {
        color: red;
        font-weight: bold;
    }
</style>

<?php include 'app/views/shares/footer.php'; ?>