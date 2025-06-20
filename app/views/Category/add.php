<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Thêm danh mục mới</h1>

<div id="error-container" class="alert alert-danger animate__animated animate__fadeIn" style="display: none;">
    <ul id="error-list"></ul>
</div>

<div id="success-container" class="alert alert-success animate__animated animate__fadeIn" style="display: none;">
    <p id="success-message"></p>
</div>

<div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <form id="add-category-form" onsubmit="return false;">
        <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-cute"><i class="fas fa-save me-2"></i>Thêm danh mục</button>
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
        showErrors(['Tên danh mục phải từ 3 đến 100 ký tự.']);
        return false;
    }
    return true;
}

function showErrors(errors) {
    const errorContainer = document.getElementById('error-container');
    const errorList = document.getElementById('error-list');
    errorList.innerHTML = '';
    errors.forEach(error => {
        const li = document.createElement('li');
        li.textContent = error;
        errorList.appendChild(li);
    });
    errorContainer.style.display = 'block';
}

function showSuccess(message) {
    const successContainer = document.getElementById('success-container');
    const successMessage = document.getElementById('success-message');
    successMessage.textContent = message;
    successContainer.style.display = 'block';
}

document.getElementById('add-category-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validateForm()) return;

    const name = document.getElementById('name').value.trim();
    const description = document.getElementById('description').value.trim();

    try {
        const response = await fetch('/WebBanHang/api/categories', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, description })
        });

        const result = await response.json();
        if (response.ok) {
            showSuccess(result.message || 'Danh mục đã được thêm thành công!');
            setTimeout(() => window.location.href = '/WebBanHang/Category/list', 2000);
        } else {
            showErrors(result.errors || ['Lỗi khi thêm danh mục.']);
        }
    } catch (error) {
        showErrors(['Lỗi kết nối server.']);
    }
});
</script>

<?php include 'app/views/shares/footer.php'; ?>