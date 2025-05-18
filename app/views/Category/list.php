<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Danh sách danh mục</h1>

<?php if (isset($error)): ?>
    <div class="alert alert-danger animate__animated animate__fadeIn">
        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<a href="/WebBanHang/Category/add" class="btn btn-cute mb-4"><i class="fas fa-plus me-2"></i>Thêm danh mục mới</a>

<div class="row">
    <?php if (empty($categories)): ?>
        <div class="col-12">
            <div class="alert alert-info animate__animated animate__fadeIn">Chưa có danh mục nào.</div>
        </div>
    <?php else: ?>
        <?php foreach ($categories as $category): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 category-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </h5>
                        <p class="card-text">
                            <?php echo htmlspecialchars($category->description ?: 'Không có mô tả', ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="d-flex justify-content-between">
                            <a href="/WebBanHang/Category/edit/<?php echo $category->id; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>Sửa</a>
                            <a href="/WebBanHang/Category/delete/<?php echo $category->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');"><i class="fas fa-trash me-1"></i>Xóa</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        animation: fadeIn 0.5s ease-in;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<?php include 'app/views/shares/footer.php'; ?>