<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Danh sách sản phẩm</h1>
<a href="/WebBanHang/Product/add" class="btn btn-cute mb-4"><i class="fas fa-plus me-2"></i>Thêm sản phẩm mới</a>

<div class="row">
    <?php if (empty($products)): ?>
        <div class="col-12">
            <div class="alert alert-info animate__animated animate__fadeIn">Chưa có sản phẩm nào.</div>
        </div>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 product-card">
                    <?php if (!empty($product->image)): ?>
                        <img src="/WebBanHang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" style="height: 200px; object-fit: cover; border-radius: 15px 15px 0 0;">
                    <?php else: ?>
                        <img src="/WebBanHang/uploads/placeholder.jpg" class="card-img-top" alt="No image" style="height: 200px; object-fit: cover; border-radius: 15px 15px 0 0;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="/WebBanHang/Product/show/<?php echo $product->id; ?>" class="text-decoration-none" style="color: #4a4a4a;">
                                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h5>
                        <p class="card-text"><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="card-text"><strong>Giá:</strong> <?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?> VNĐ</p>
                        <p class="card-text"><strong>Danh mục:</strong> <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <a href="/WebBanHang/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>Sửa</a>
                            <a href="/WebBanHang/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');"><i class="fas fa-trash me-1"></i>Xóa</a>
                            <a href="/WebBanHang/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-cute btn-sm"><i class="fas fa-cart-plus me-1"></i>Thêm vào giỏ</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.5s ease-in;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }
    .card-img-top {
        transition: opacity 0.3s ease;
    }
    .card-img-top:hover {
        opacity: 0.9;
    }
</style>

<?php include 'app/views/shares/footer.php'; ?>