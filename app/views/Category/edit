<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Sửa danh mục</h1>

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
    <form method="POST" action="/WebBanHang/Category/update" onsubmit="return validateForm();">
        <input type="hidden" name="id" value="<?php echo $category->id; ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($category->description ?: '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-cute"><i class="fas fa-save me-2"></i>Lưu thay đổi</button>
            <a href="/WebBanHang/Category/list" class="btn btn-secondary-cute">Quay lại</a>
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
        if (name.length < 3 || name.length > 100) {
            alert('Tên danh mục phải từ 3 đến 100 ký tự.');
            return false;
        }
        return true;
    }
</script>

<?php include 'app/views/shares/footer.php'; ?>