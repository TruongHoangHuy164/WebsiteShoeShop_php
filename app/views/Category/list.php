<?php include 'app/views/shares/header.php'; ?>
<!-- Add twinkling and shooting stars containers -->
<div class="twinkling-stars" id="twinklingStars"></div>
<div class="shooting-stars" id="shootingStars"></div>

<link rel="stylesheet" href="../public/css/list.css">

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
                    <label for="search" class="filter-label">
                        <i class="fas fa-search"></i>
                        Tìm kiếm
                    </label>
                    <input type="text" id="search" name="search" class="filter-input" 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8') : ''; ?>" 
                           placeholder="Nhập tên sản phẩm...">
                </div>
                
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
                
                <div class="filter-group">
                    <label for="sort" class="filter-label">
                        <i class="fas fa-sort"></i>
                        Sắp xếp
                    </label>
                    <select id="sort" name="sort" class="filter-select">
                        <option value="">Mặc định</option>
                        <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>
                            Giá: Thấp đến cao
                        </option>
                        <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>
                            Giá: Cao đến thấp
                        </option>
                    </select>
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
    document.getElementById('search').value = '';
    document.getElementById('category_id').selectedIndex = 0;
    document.getElementById('price_min').value = '';
    document.getElementById('price_max').value = '';
    document.getElementById('sort').selectedIndex = 0;
    document.getElementById('filter-form').submit();
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

// Initialize star effects
document.addEventListener('DOMContentLoaded', function() {
    createTwinklingStars();
    
    // Create shooting stars periodically
    setInterval(createShootingStar, 1000);
    
    // Create burst of shooting stars occasionally
    setInterval(() => {
        for (let i = 0; i < 2; i++) {
            setTimeout(createShootingStar, i * 300);
        }
    }, 6000);
});

// Handle resize for twinkling stars
window.addEventListener('resize', () => {
    const twinklingContainer = document.getElementById('twinklingStars');
    twinklingContainer.innerHTML = '';
    createTwinklingStars();
});
</script>

<?php include 'app/views/shares/footer.php'; ?>