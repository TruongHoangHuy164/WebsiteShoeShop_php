<?php include 'app/views/shares/header.php'; ?>
<!-- Add twinkling and shooting stars containers -->
<div class="twinkling-stars" id="twinklingStars"></div>
<div class="shooting-stars" id="shootingStars"></div>
<link href="/WebBanHang/public/css/list.css" rel="stylesheet">

<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">
            <i class="fas fa-paw paw-icon"></i>
            Sản phẩm cho thú cưng
            <i class="fas fa-paw paw-icon"></i>
        </h1>
        <p class="hero-subtitle">Tìm những sản phẩm tốt nhất cho người bạn bốn chân của bạn</p>
    </div>
</div>

<div class="container main-content">
    <div class="action-bar">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <button class="btn-primary-custom" onclick="showAddProductForm()">
            <i class="fas fa-plus"></i>
            <span>Thêm sản phẩm mới</span>
        </button>
        <?php endif; ?>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- Advanced Filter Section -->
    <div class="filter-container">
        <div class="filter-header">
            <h3><i class="fas fa-filter"></i> Lọc sản phẩm</h3>
            <div class="filter-toggle" onclick="toggleFilter()">
                <i class="fas fa-chevron-up" id="filter-arrow"></i>
            </div>
        </div>
        <form method="GET" action="/WebBanHang/Product" class="filter-form" id="filter-form">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="category_id" class="filter-label">
                        <i class="fas fa-tags"></i>
                        Danh mục
                    </label>
                    <select id="category_id" name="category_id" class="filter-select">
                        <option value="">Tất cả danh mục</option>
                        <!-- Categories will be populated via JavaScript -->
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="price_min" class="filter-label">
                        <i class="fas fa-coins"></i>
                        Giá tối thiểu
                    </label>
                    <input type="number" id="price_min" name="price_min" class="filter-input" min="0" 
                           placeholder="0 VNĐ">
                </div>
                
                <div class="filter-group">
                    <label for="price_max" class="filter-label">
                        <i class="fas fa-coins"></i>
                        Giá tối đa
                    </label>
                    <input type="number" id="price_max" name="price_max" class="filter-input" min="0" 
                           placeholder="1,000,000 VNĐ">
                </div>
                
                <div class="filter-group">
                    <label for="search_name" class="filter-label">
                        <i class="fas fa-search"></i>
                        Tên sản phẩm
                    </label>
                    <input type="text" id="search_name" name="search_name" class="filter-input" 
                           placeholder="Nhập tên sản phẩm">
                </div>
                
                <div class="filter-group">
                    <label for="sort" class="filter-label">
                        <i class="fas fa-sort"></i>
                        Sắp xếp
                    </label>
                    <select id="sort" name="sort" class="filter-select">
                        <option value="">Mặc định</option>
                        <option value="price_asc">Giá: Thấp đến cao</option>
                        <option value="price_desc">Giá: Cao đến thấp</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i>
                    Tìm kiếm
                </button>
                <button type="button" class="btn-reset" onclick="resetFilter()">
                    <i class="fas fa-undo"></i>
                    Đặt lại
                </button>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="products-section" id="products-section">
        <div class="empty-state" id="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Không tìm thấy sản phẩm</h3>
            <p>Hãy thử điều chỉnh bộ lọc để tìm kiếm sản phẩm phù hợp</p>
        </div>
        <div class="products-grid" id="products-grid"></div>
    </div>

    <!-- Add/Edit Product Modal -->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeProductModal()">&times;</span>
            <h2 id="modalTitle">Thêm sản phẩm mới</h2>
            <form id="productForm">
                <div class="form-group">
                    <label for="product_name">Tên sản phẩm</label>
                    <input type="text" id="product_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="product_description">Mô tả</label>
                    <textarea id="product_description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="product_price">Giá (VNĐ)</label>
                    <input type="number" id="product_price" name="price" min="0" required>
                </div>
                <div class="form-group">
                    <label for="product_quantity">Số lượng</label>
                    <input type="number" id="product_quantity" name="quantity" min="0" required>
                </div>
                <div class="form-group">
                    <label for="product_category_id">Danh mục</label>
                    <select id="product_category_id" name="category_id" required>
                        <!-- Populated via JavaScript -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_image">Hình ảnh (URL)</label>
                    <input type="text" id="product_image" name="image">
                </div>
                <input type="hidden" id="product_id" name="id">
                <button type="submit" class="btn-submit">Lưu</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Utility function to show alerts
function showAlert(message, type = 'error') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-${type}`;
    alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
    document.querySelector('.main-content').prepend(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}

// Fetch categories and populate the dropdown
async function fetchCategories() {
    try {
        const response = await fetch('/WebBanHang/api/categories');
        const result = await response.json();
        if (response.ok && result.data) {
            const categorySelect = document.getElementById('category_id');
            const modalCategorySelect = document.getElementById('product_category_id');
            categorySelect.innerHTML = '<option value="">Tất cả danh mục</option>';
            modalCategorySelect.innerHTML = '';
            result.data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                if (new URLSearchParams(window.location.search).get('category_id') == category.id) {
                    option.selected = true;
                }
                categorySelect.appendChild(option.cloneNode(true));
                modalCategorySelect.appendChild(option);
            });
        } else {
            showAlert(result.error || 'Không thể tải danh mục');
        }
    } catch (error) {
        showAlert('Lỗi khi tải danh mục: ' + error.message);
    }
}

// Fetch products based on filters
async function fetchProducts() {
    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams();
    
    formData.forEach((value, key) => {
        if (value) params.append(key, value);
    });

    try {
        const response = await fetch(`/WebBanHang/api/products?${params.toString()}`);
        const result = await response.json();
        const productsGrid = document.getElementById('products-grid');
        const emptyState = document.getElementById('empty-state');
        
        if (response.ok && result.data) {
            if (result.data.length === 0) {
                emptyState.style.display = 'block';
                productsGrid.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                productsGrid.style.display = 'grid';
                renderProducts(result.data);
            }
        } else {
            emptyState.style.display = 'block';
            productsGrid.style.display = 'none';
            showAlert(result.error || 'Không thể tải sản phẩm');
        }
    } catch (error) {
        emptyState.style.display = 'block';
        productsGrid.style.display = 'none';
        showAlert('Lỗi khi tải sản phẩm: ' + error.message);
    }
}

// Render products in the grid
function renderProducts(products) {
    const productsGrid = document.getElementById('products-grid');
    productsGrid.innerHTML = '';
    
    products.forEach((product, index) => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.style.animationDelay = `${index * 0.1}s`;
        
        const isAdmin = <?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'true' : 'false'; ?>;
        
        productCard.innerHTML = `
            <div class="product-image-container">
                <img src="/WebBanHang/${product.image || 'Uploads/placeholder.jpg'}" 
                     class="product-image" 
                     alt="${product.name}">
                <div class="product-overlay">
                    <a href="/WebBanHang/Product/show/${product.id}" class="overlay-btn">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
                ${product.quantity <= 10 ? `
                <div class="stock-badge low-stock">
                    <i class="fas fa-exclamation-triangle"></i>
                    Sắp hết hàng
                </div>` : ''}
            </div>
            <div class="product-content">
                <div class="product-category">
                    <i class="fas fa-tag"></i>
                    ${product.category_name || 'Unknown'}
                </div>
                <h3 class="product-title">
                    <a href="/WebBanHang/Product/show/${product.id}">
                        ${product.name}
                    </a>
                </h3>
                <p class="product-description">
                    ${product.description.length > 100 ? product.description.substring(0, 100) + '...' : product.description}
                </p>
                <div class="product-stock">
                    <i class="fas fa-box"></i>
                    <span class="${product.quantity <= 10 ? 'low-quantity' : ''}">
                        ${product.quantity || 0} sản phẩm
                    </span>
                </div>
                <div class="product-meta">
                    <div class="product-price">
                        <span class="price-value">${new Intl.NumberFormat('vi-VN').format(product.price)}</span>
                        <span class="price-currency">VNĐ</span>
                    </div>
                </div>
                <div class="product-actions">
                    <a href="/WebBanHang/Product/addToCart/${product.id}" class="btn-cart">
                        <i class="fas fa-cart-plus"></i>
                        Thêm vào giỏ
                    </a>
                    ${isAdmin ? `
                    <div class="action-buttons">
                        <button class="btn-action edit" title="Chỉnh sửa" onclick="showEditProductForm(${product.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action delete" title="Xóa" onclick="deleteProduct(${product.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>` : ''}
                </div>
            </div>
        `;
        productsGrid.appendChild(productCard);
    });

    // Re-apply image load animation
    document.querySelectorAll('.product-image').forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
    });
}

// Show add product modal
function showAddProductForm() {
    document.getElementById('modalTitle').textContent = 'Thêm sản phẩm mới';
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('productModal').style.display = 'block';
}

// Show edit product modal
async function showEditProductForm(id) {
    try {
        const response = await fetch(`/WebBanHang/api/product/${id}`);
        const result = await response.json();
        if (response.ok && result.data) {
            const product = result.data;
            document.getElementById('modalTitle').textContent = 'Chỉnh sửa sản phẩm';
            document.getElementById('product_id').value = product.id;
            document.getElementById('product_name').value = product.name;
            document.getElementById('product_description').value = product.description;
            document.getElementById('product_price').value = product.price;
            document.getElementById('product_quantity').value = product.quantity;
            document.getElementById('product_category_id').value = product.category_id;
            document.getElementById('product_image').value = product.image || '';
            document.getElementById('productModal').style.display = 'block';
        } else {
            showAlert(result.error || 'Không thể tải thông tin sản phẩm');
        }
    } catch (error) {
        showAlert('Lỗi khi tải thông tin sản phẩm: ' + error.message);
    }
}

// Close product modal
function closeProductModal() {
    document.getElementById('productModal').style.display = 'none';
}

// Handle product form submission
document.getElementById('productForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const id = formData.get('id');
    const data = {
        name: formData.get('name'),
        description: formData.get('description'),
        price: parseFloat(formData.get('price')),
        quantity: parseInt(formData.get('quantity')),
        category_id: parseInt(formData.get('category_id')),
        image: formData.get('image')
    };

    try {
        const url = id ? `/WebBanHang/api/product/${id}` : '/WebBanHang/api/products';
        const method = id ? 'PUT' : 'POST';
        const response = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (response.ok) {
            showAlert(result.message, 'success');
            closeProductModal();
            fetchProducts();
        } else {
            showAlert(result.errors?.join(', ') || result.error || 'Lỗi khi lưu sản phẩm');
        }
    } catch (error) {
        showAlert('Lỗi khi lưu sản phẩm: ' + error.message);
    }
});

// Delete product
async function deleteProduct(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) return;
    try {
        const response = await fetch(`/WebBanHang/api/product/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();
        if (response.ok) {
            showAlert(result.message, 'success');
            fetchProducts();
        } else {
            showAlert(result.error || 'Không thể xóa sản phẩm');
        }
    } catch (error) {
        showAlert('Lỗi khi xóa sản phẩm: ' + error.message);
    }
}

// Handle form submission
document.getElementById('filter-form').addEventListener('submit', (e) => {
    e.preventDefault();
    fetchProducts();
});

// Handle filter reset
function resetFilter() {
    const form = document.getElementById('filter-form');
    form.reset();
    fetchProducts();
}

// Toggle filter visibility
function toggleFilter() {
    const form = document.getElementById('filter-form');
    const arrow = document.getElementById('filter-arrow');
    const toggle = document.querySelector('.filter-toggle');
    
    form.classList.toggle('collapsed');
    toggle.classList.toggle('rotated');
}

// Initialize filter as collapsed on mobile
if (window.innerWidth <= 768) {
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('filter-form');
        const toggle = document.querySelector('.filter-toggle');
        form.classList.add('collapsed');
        toggle.classList.add('rotated');
    });
}

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add twinkling and shooting star effects
function createTwinklingStars() {
    const container = document.getElementById('twinklingStars');
    const starCount = 40;

    for (let i = 0; i < starCount; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.left = Math.random() * 100 + '%';
        star.style.top = Math.random() * 100 + '%';
        star.style.animationDelay = Math.random() * 2 + 's';
        star.style.animationDuration = (Math.random() * 2 + 1) + 's';
        container.appendChild(star);
    }
}

function createShootingStar() {
    const container = document.getElementById('shootingStars');
    const star = document.createElement('div');
    
    const sizes = ['small', 'medium', 'large'];
    const colors = ['gold', 'orange-gold', 'light-gold'];
    const size = sizes[Math.floor(Math.random() * sizes.length)];
    const color = colors[Math.floor(Math.random() * colors.length)];
    
    star.className = `shooting-star ${size} ${color}`;
    
    star.style.left = Math.random() * window.innerWidth + 'px';
    star.style.top = Math.random() * (window.innerHeight / 2) + 'px';
    
    star.style.animationDelay = Math.random() * 2 + 's';
    star.style.animationDuration = (Math.random() * 2 + 2) + 's';
    
    container.appendChild(star);

    setTimeout(() => {
        if (star.parentNode) {
            star.parentNode.removeChild(star);
        }
    }, 5000);
}

// Initialize star effects and fetch initial data
document.addEventListener('DOMContentLoaded', () => {
    createTwinklingStars();
    setInterval(createShootingStar, 1000);
    setInterval(() => {
        for (let i = 0; i < 2; i++) {
            setTimeout(createShootingStar, i * 300);
        }
    }, 6000);
    fetchCategories();
    fetchProducts();
});

// Handle resize for twinkling stars
window.addEventListener('resize', () => {
    const twinklingContainer = document.getElementById('twinklingStars');
    twinklingContainer.innerHTML = '';
    createTwinklingStars();
});

// Modal close on click outside
document.getElementById('productModal')?.addEventListener('click', (e) => {
    if (e.target === document.getElementById('productModal')) {
        closeProductModal();
    }
});
</script>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}
.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
}
.close {
    position: absolute;
    right: 20px;
    top: 10px;
    font-size: 24px;
    cursor: pointer;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
}
.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.form-group textarea {
    height: 100px;
}
.btn-submit {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-submit:hover {
    background-color: #218838;
}
.alert-error, .alert-success {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
}
</style>

<?php include 'app/views/shares/footer.php'; ?>