<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Thêm sản phẩm mới</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger animate__animated animate__fadeIn">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <form method="POST" action="/WebBanHang/Product/save" enctype="multipart/form-data" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Giá (VNĐ)</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" id="quantity" name="quantity" class="form-control" min="0" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Chọn danh mục</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category->id; ?>">
                        <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <small class="form-text text-muted">Định dạng: JPG, JPEG, PNG, GIF. Tối đa 10MB.</small>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-cute"><i class="fas fa-save me-2"></i>Thêm sản phẩm</button>
            <a href="/WebBanHang/Product" class="btn btn-secondary-cute">Quay lại</a>
        </div>
    </form>
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
<script>
    function validateForm() {
        let name = document.getElementById('name').value.trim();
        let description = document.getElementById('description').value.trim();
        let price = document.getElementById('price').value;
        let quantity = document.getElementById('quantity').value;
        let category = document.getElementById('category_id').value;
        let image = document.getElementById('image').files[0];

        if (name.length < 3 || name.length > 100) {
            alert('Tên sản phẩm phải từ 3 đến 100 ký tự.');
            return false;
        }
        if (!description) {
            alert('Mô tả là bắt buộc.');
            return false;
        }
        if (price <= 0) {
            alert('Giá phải là số dương.');
            return false;
        }
        if (quantity < 0) {
            alert('Số lượng phải là số không âm.');
            return false;
        }
        if (!category) {
            alert('Vui lòng chọn danh mục.');
            return false;
        }
        if (image && image.size > 10 * 1024 * 1024) {
            alert('Hình ảnh không được vượt quá 10MB.');
            return false;
        }
        return true;
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>z