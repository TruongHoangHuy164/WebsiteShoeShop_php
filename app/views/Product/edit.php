<?php include 'app/views/shares/header.php'; ?>

<h1 class="mb-4 text-center" style="color: #ff6b81;">Sửa sản phẩm</h1>

<div id="error-alert" class="alert alert-danger animate__animated animate__fadeIn d-none"></div>
<div id="success-alert" class="alert alert-success animate__animated animate__fadeIn d-none"></div>

<div class="card p-4" style="border-radius: 15px; background: #fff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <form id="edit-product-form" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($product->id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" id="existing_image" name="existing_image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="mb-3">
            <label for="name" class="form-label">Tên sản phẩm</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Giá (VNĐ)</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input type="number" id="quantity" name="quantity" class="form-control <?php echo ($product->quantity ?? 0) <= 10 ? 'low-quantity' : ''; ?>" min="0" step="1" value="<?php echo htmlspecialchars($product->quantity ?? '0', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="category_id" class="form-label">Danh mục</label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">Chọn danh mục</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Hình ảnh</label>
            <?php if (!empty($product->image)): ?>
                <div class="mb-2">
                    <img id="imagePreview" src="/WebBanHang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" alt="Current image" style="max-width: 150px; border-radius: 10px;">
                </div>
            <?php else: ?>
                <div class="mb-2">
                    <img id="imagePreview" src="/WebBanHang/Uploads/placeholder.jpg" alt="No image" style="max-width: 150px; border-radius: 10px;">
                </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <small class="form-text text-muted">Định dạng: JPG, JPEG, PNG, GIF. Tối đa 10MB. Để trống để giữ ảnh hiện tại.</small>
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-cute"><i class="fas fa-save me-2"></i>Lưu thay đổi</button>
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
    .low-quantity {
        color: red;
        font-weight: bold;
    }
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        max-width: 400px;
        animation: slideIn 0.5s ease-out;
    }
    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Lấy ID sản phẩm từ URL
    const urlSegments = window.location.pathname.split('/');
    const productId = urlSegments[urlSegments.length - 1];

    if (!productId || isNaN(productId)) {
        $('#error-alert').removeClass('d-none').text('ID sản phẩm không hợp lệ');
        return;
    }

    // Lấy danh mục
    $.ajax({
        url: '/WebBanHang/api/categories',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data) {
                const categorySelect = $('#category_id');
                categorySelect.empty();
                categorySelect.append('<option value="">Chọn danh mục</option>');
                $.each(response.data, function(index, category) {
                    const option = $('<option></option>')
                        .val(category.id)
                        .text(category.name);
                    categorySelect.append(option);
                });
                // Nếu có category_id từ sản phẩm, chọn nó
                if (window._categoryId) {
                    categorySelect.val(window._categoryId);
                }
            } else {
                $('#error-alert').removeClass('d-none').text('Không thể tải danh mục: ' + (response.error || 'Lỗi server'));
            }
        },
        error: function(xhr) {
            $('#error-alert').

removeClass('d-none').text('Không thể tải danh mục: ' + (xhr.responseJSON?.error || 'Lỗi server'));
        }
    });

    // Lấy thông tin sản phẩm
    $.ajax({
        url: `/WebBanHang/api/products/${productId}`,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data) {
                const product = response.data;
                $('#id').val(product.id);
                $('#name').val(product.name || '');
                $('#description').val(product.description || '');
                $('#price').val(product.price || '');
                $('#quantity').val(product.quantity || 0);
                $('#existing_image').val(product.image || '');
                $('#imagePreview').attr('src', product.image ? `/WebBanHang/${product.image}` : '/WebBanHang/Uploads/placeholder.jpg');
                window._categoryId = product.category_id || '';
                if ($('#category_id').length) {
                    $('#category_id').val(window._categoryId);
                }
            } else {
                $('#error-alert').removeClass('d-none').text('Không thể tải thông tin sản phẩm: ' + (response.error || 'Lỗi server'));
            }
        },
        error: function(xhr) {
            $('#error-alert').removeClass('d-none').text('Không thể tải thông tin sản phẩm: ' + (xhr.responseJSON?.error || 'Lỗi server'));
        }
    });

    // Xử lý submit form
    $('#edit-product-form').on('submit', function(e) {
        e.preventDefault();

        const productId = $('#id').val();
        if (!productId) {
            $('#error-alert').removeClass('d-none').text('Không tìm thấy ID sản phẩm');
            return;
        }

        // Validate dữ liệu
        const name = $('#name').val().trim();
        const description = $('#description').val().trim();
        const price = $('#price').val();
        const quantity = $('#quantity').val();
        const category_id = $('#category_id').val();

        if (!name || name.length < 3 || name.length > 100) {
            $('#error-alert').removeClass('d-none').text('Tên sản phẩm phải từ 3 đến 100 ký tự');
            return;
        }
        if (!description) {
            $('#error-alert').removeClass('d-none').text('Mô tả là bắt buộc');
            return;
        }
        if (!price || isNaN(price) || parseFloat(price) <= 0) {
            $('#error-alert').removeClass('d-none').text('Giá phải là số dương');
            return;
        }
        if (!quantity || isNaN(quantity) || parseInt(quantity) < 0) {
            $('#error-alert').removeClass('d-none').text('Số lượng phải là số nguyên không âm');
            return;
        }
        if (!category_id) {
            $('#error-alert').removeClass('d-none').text('Vui lòng chọn danh mục');
            return;
        }

        // Xử lý upload ảnh
        const imageFile = $('#image')[0].files[0];
        if (imageFile) {
            if (imageFile.size > 10 * 1024 * 1024) {
                $('#error-alert').removeClass('d-none').text('Kích thước ảnh không được vượt quá 10MB');
                return;
            }
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(imageFile.type)) {
                $('#error-alert').removeClass('d-none').text('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)');
                return;
            }

            const formData = new FormData();
            formData.append('image', imageFile);
            $.ajax({
                url: '/WebBanHang/api/upload', // Adjust this endpoint if needed
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.image_path) {
                        updateProduct(productId, name, description, price, quantity, category_id, response.image_path);
                    } else {
                        $('#error-alert').removeClass('d-none').text('Không thể upload ảnh: ' + (response.error || 'Lỗi không xác định'));
                    }
                },
                error: function(xhr) {
                    $('#error-alert').removeClass('d-none').text('Không thể upload ảnh: ' + (xhr.responseJSON?.error || 'Lỗi server'));
                }
            });
        } else {
            updateProduct(productId, name, description, price, quantity, category_id, $('#existing_image').val());
        }
    });

    // Hàm cập nhật sản phẩm
    function updateProduct(productId, name, description, price, quantity, category_id, image) {
        $('#error-alert').addClass('d-none');
        $('#success-alert').addClass('d-none');
        const loadingAlert = $('<div class="alert alert-info">Đang cập nhật sản phẩm...</div>');
        $('.card').prepend(loadingAlert);

        const formData = {
            name: name,
            description: description,
            price: parseFloat(price),
            quantity: parseInt(quantity),
            category_id: parseInt(category_id),
            image: image
        };

        $.ajax({
            url: `/WebBanHang/api/products/${productId}`,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                loadingAlert.remove();
                $('#success-alert').removeClass('d-none').text('Cập nhật sản phẩm thành công!');
            },
            error: function(xhr) {
                loadingAlert.remove();
                $('#error-alert').removeClass('d-none').text('Cập nhật sản phẩm thất bại: ' + (xhr.responseJSON?.error || 'Lỗi server'));
            }
        });
    }

    // Xử lý preview ảnh
    $('#image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) {
                $('#error-alert').removeClass('d-none').text('Kích thước ảnh không được vượt quá 10MB');
                $(this).val('');
                return;
            }
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                $('#error-alert').removeClass('d-none').text('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF)');
                $(this).val('');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Tự động ẩn toast sau 3 giây
    function hideToast() {
        const toasts = $('#success-alert, #error-alert');
        toasts.each(function() {
            if (!$(this).hasClass('d-none')) {
                const toast = $(this);
                setTimeout(() => {
                    toast.removeClass('animate__fadeIn').addClass('animate__fadeOut');
                    setTimeout(() => toast.addClass('d-none').removeClass('animate__fadeOut'), 500);
                }, 3000);
            }
        });
    }

    // Gọi hideToast khi load trang và sau mỗi lần hiển thị toast
    hideToast();
    $(document).ajaxComplete(hideToast);
});
</script>

<?php include 'app/views/shares/footer.php'; ?>