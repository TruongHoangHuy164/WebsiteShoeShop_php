<?php include 'app/views/shares/header.php'; ?>

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
        <a href="/WebBanHang/Product/add" class="btn-primary-custom">
            <i class="fas fa-plus"></i>
            <span>Thêm sản phẩm mới</span>
        </a>
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
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="price_min" class="filter-label">
                        <i class="fas fa-coins"></i>
                        Giá tối thiểu
                    </label>
                    <input type="number" id="price_min" name="price_min" class="filter-input" min="0" 
                           value="<?php echo isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                           placeholder="0 VNĐ">
                </div>
                
                <div class="filter-group">
                    <label for="price_max" class="filter-label">
                        <i class="fas fa-coins"></i>
                        Giá tối đa
                    </label>
                    <input type="number" id="price_max" name="price_max" class="filter-input" min="0" 
                           value="<?php echo isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                           placeholder="1,000,000 VNĐ">
                </div>
                
                <div class="filter-group filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fas fa-search"></i>
                        Tìm kiếm
                    </button>
                    <button type="button" class="btn-reset" onclick="resetFilter()">
                        <i class="fas fa-undo"></i>
                        Đặt lại
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="products-section">
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Không tìm thấy sản phẩm</h3>
                <p>Hãy thử điều chỉnh bộ lọc để tìm kiếm sản phẩm phù hợp</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $index => $product): ?>
                    <div class="product-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                        <div class="product-image-container">
                            <?php if (!empty($product->image)): ?>
                                <img src="/WebBanHang/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="product-image" 
                                     alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <img src="/WebBanHang/uploads/placeholder.jpg" 
                                     class="product-image" 
                                     alt="No image">
                            <?php endif; ?>
                            <div class="product-overlay">
                                <a href="/WebBanHang/Product/show/<?php echo $product->id; ?>" class="overlay-btn">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                            <?php if (($product->quantity ?? 0) <= 10): ?>
                                <div class="stock-badge low-stock">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Sắp hết hàng
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-content">
                            <div class="product-category">
                                <i class="fas fa-tag"></i>
                                <?php echo htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            
                            <h3 class="product-title">
                                <a href="/WebBanHang/Product/show/<?php echo $product->id; ?>">
                                    <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h3>
                            
                            <p class="product-description">
                                <?php echo htmlspecialchars(substr($product->description, 0, 100), ENT_QUOTES, 'UTF-8') . (strlen($product->description) > 100 ? '...' : ''); ?>
                            </p>
                            <div class="product-stock">
                            <i class="fas fa-box"></i>
                             <span class="<?php echo ($product->quantity ?? 0) <= 10 ? 'low-quantity' : ''; ?>">
                                  <?php echo htmlspecialchars($product->quantity ?? '0', ENT_QUOTES, 'UTF-8'); ?> sản phẩm
                              </span>
                                </div>
                              <div class="product-meta">
                                <div class="product-price">
                                    <span class="price-value"><?php echo number_format($product->price, 0, ',', '.'); ?></span>
                                    <span class="price-currency">VNĐ</span>
                                </div>
                                
                            </div>
                            
                            <div class="product-actions">
                                <a href="/WebBanHang/Product/addToCart/<?php echo $product->id; ?>" class="btn-cart">
                                    <i class="fas fa-cart-plus"></i>
                                    Thêm vào giỏ
                                </a>
                                <div class="action-buttons">
                                    <a href="/WebBanHang/Product/edit/<?php echo $product->id; ?>" class="btn-action edit" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/WebBanHang/Product/delete/<?php echo $product->id; ?>" 
                                       class="btn-action delete" 
                                       title="Xóa"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</div>

<style>
/* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #ffccde, #b4e4e9 100%);
    padding: 4rem 0;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="30" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="70" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    text-align: center;
    margin-bottom: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    animation: slideInDown 1s ease-out;
}

.paw-icon {
    color: #ffd700;
    margin: 0 1rem;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.hero-subtitle {
    font-size: 1.2rem;
    color: rgba(255,255,255,0.9);
    text-align: center;
    animation: slideInUp 1s ease-out 0.3s both;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.main-content {
    padding: 0 1rem;
}

/* Action Bar */
.action-bar {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 2rem;
    animation: slideInRight 0.8s ease-out;
}

.btn-primary-custom {
    background: linear-gradient(45deg, #ff6b6b, #ffd93d);
    border: none;
    padding: 1rem 2rem;
    border-radius: 50px;
    color: white;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    color: white;
}

/* Alert */
.alert-success {
    background: linear-gradient(45deg, #4CAF50, #8BC34A);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    animation: slideInLeft 0.8s ease-out;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

/* Filter Container */
.filter-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin-bottom: 3rem;
    overflow: hidden;
    animation: slideInUp 0.8s ease-out;
}

.filter-header {
    background: linear-gradient(45deg, #ffccde, #b4e4e9);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.filter-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-toggle {
    transition: transform 0.3s ease;
}

.filter-toggle.rotated {
    transform: rotate(180deg);
}

.filter-form {
    padding: 2rem;
    max-height: 300px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.filter-form.collapsed {
    max-height: 0;
    padding: 0 2rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    color: #333;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-select,
.filter-input {
    padding: 0.75rem 1rem;
    border: 2px solid #e1e8ed;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-actions {
    display: flex;
    gap: 1rem;
}

.btn-filter,
.btn-reset {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}

.btn-filter {
    background: linear-gradient(45deg, #ffccde, #b4e4e9);
    color: white;
}

.btn-reset {
    background: #f8f9fa;
    color: #6c757d;
    border: 2px solid #e1e8ed;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-reset:hover {
    background: #e9ecef;
}

/* Products Section */
.products-section {
    animation: fadeInUp 1s ease-out;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    padding: 1rem 0;
}

/* Product Card */
.product-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    animation: slideInUp 0.6s ease-out both;
    position: relative;
}

.product-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.product-image-container {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.1);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.overlay-btn {
    background: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    text-decoration: none;
    transform: scale(0);
    transition: all 0.3s ease;
}

.product-card:hover .overlay-btn {
    transform: scale(1);
}

.stock-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.low-stock {
    background: #ff4757;
    color: white;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Product Content */
.product-content {
    padding: 1.5rem;
}

.product-category {
    color: #667eea;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.product-title {
    margin-bottom: 1rem;
}

.product-title a {
    color: #333;
    text-decoration: none;
    font-size: 1.3rem;
    font-weight: 700;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: #667eea;
}

.product-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 15px;
}

.product-price {
    display: flex;
    align-items: baseline;
    gap: 0.3rem;
}

.price-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ff6b6b;
}

.price-currency {
    font-size: 1rem;
    color: #666;
}

.product-stock {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
}

.low-quantity {
    color: #ff4757;
    font-weight: 600;
}

/* Product Actions */
.product-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.btn-cart {
    background: linear-gradient(45deg, #ff6b6b, #ffd93d);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    flex: 1;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
}

.btn-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
    color: white;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-action.edit {
    background: #17a2b8;
    color: white;
}

.btn-action.delete {
    background: #dc3545;
    color: white;
}

.btn-action:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #666;
}

.empty-icon {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    color: #333;
}

/* Animations */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .paw-icon {
        margin: 0 0.5rem;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .product-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-cart {
        width: 100%;
    }
    
    .action-buttons {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 2rem 0;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
    
    .container {
        padding: 0 0.5rem;
    }
    
    .filter-header {
        padding: 1rem;
    }
    
    .filter-form {
        padding: 1rem;
    }
    
    .product-content {
        padding: 1rem;
    }
}
</style>

<script>
function toggleFilter() {
    const form = document.getElementById('filter-form');
    const arrow = document.getElementById('filter-arrow');
    const toggle = document.querySelector('.filter-toggle');
    
    form.classList.toggle('collapsed');
    toggle.classList.toggle('rotated');
}

function resetFilter() {
    document.getElementById('category_id').selectedIndex = 0;
    document.getElementById('price_min').value = '';
    document.getElementById('price_max').value = '';
}

// Initialize filter as collapsed on mobile
if (window.innerWidth <= 768) {
    document.addEventListener('DOMContentLoaded', function() {
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

// Add loading animation for images
document.querySelectorAll('.product-image').forEach(img => {
    img.addEventListener('load', function() {
        this.style.opacity = '1';
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>