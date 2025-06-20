<?php include 'app/views/shares/header.php'; ?>
<!-- Add twinkling and shooting stars containers -->
<div class="twinkling-stars" id="twinklingStars"></div>
<div class="shooting-stars" id="shootingStars"></div>

<link rel="stylesheet" href="../public/css/list.css">

<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">
            <i class="fas fa-paw paw-icon"></i>
            Danh mục sản phẩm
            <i class="fas fa-paw paw-icon"></i>
        </h1>
        <p class="hero-subtitle">Quản lý các danh mục cho cửa hàng thú cưng của bạn</p>
    </div>
</div>

<div class="container main-content">
    <div class="action-bar">
        <a href="/WebBanHang/Category/add" class="btn-primary-custom">
            <i class="fas fa-plus"></i>
            <span>Thêm danh mục mới</span>
        </a>
    </div>

    <div id="success-container" class="alert-success" style="display: none;">
        <i class="fas fa-check-circle"></i>
        <span id="success-message"></span>
    </div>

    <!-- Categories List -->
    <div class="categories-section" id="categories-section">
        <div class="empty-state" id="empty-state" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Không tìm thấy danh mục</h3>
            <p>Hãy thêm một danh mục mới để bắt đầu</p>
        </div>
        <div class="categories-grid" id="categories-grid"></div>
    </div>
</div>

<style>
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }
    .category-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: transform 0.3s ease;
        opacity: 0;
        animation: fadeInUp 0.5s ease forwards;
    }
    .category-card:hover {
        transform: translateY(-5px);
    }
    .category-title {
        font-size: 1.5rem;
        color: #4a4a4a;
        margin-bottom: 10px;
    }
    .category-description {
        color: #6c757d;
        font-size: 1rem;
        margin-bottom: 15px;
    }
    .category-actions {
        display: flex;
        gap: 10px;
    }
    .btn-action {
        padding: 8px 12px;
        border-radius: 8px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .btn-action.edit {
        background-color: #ff6b81;
    }
    .btn-action.edit:hover {
        background-color: #e65b73;
    }
    .btn-action.delete {
        background-color: #dc3545;
    }
    .btn-action.delete:hover {
        background-color: #c82333;
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
</style>

<script>
async function fetchCategories() {
    try {
        const response = await fetch('/WebBanHang/api/categories');
        const result = await response.json();
        if (response.ok) {
            renderCategories(result.data);
        } else {
            renderCategories([]);
        }
    } catch (error) {
        console.error('Lỗi khi tải danh mục:', error);
        renderCategories([]);
    }
}

function renderCategories(categories) {
    const grid = document.getElementById('categories-grid');
    const emptyState = document.getElementById('empty-state');
    grid.innerHTML = '';

    if (categories.length === 0) {
        emptyState.style.display = 'block';
        return;
    }

    emptyState.style.display = 'none';
    categories.forEach((category, index) => {
        const card = document.createElement('div');
        card.className = 'category-card';
        card.style.animationDelay = `${index * 0.1}s`;
        card.innerHTML = `
            <h3 class="category-title">${escapeHtml(category.name)}</h3>
            <p class="category-description">${escapeHtml(category.description || 'Không có mô tả')}</p>
            <div class="category-actions">
                <a href="/WebBanHang/Category/edit/${category.id}" class="btn-action edit" title="Chỉnh sửa">
                    <i class="fas fa-edit"></i> Sửa
                </a>
                <a href="#" class="btn-action delete" title="Xóa" data-id="${category.id}">
                    <i class="fas fa-trash"></i> Xóa
                </a>
            </div>
        `;
        grid.appendChild(card);

        // Add delete event listener
        card.querySelector('.delete').addEventListener('click', async (e) => {
            e.preventDefault();
            if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
                try {
                    const response = await fetch(`/WebBanHang/api/categories/${category.id}`, {
                        method: 'DELETE'
                    });
                    const result = await response.json();
                    if (response.ok) {
                        showSuccess(result.message || 'Danh mục đã được xóa thành công!');
                        fetchCategories();
                    } else {
                        alert(result.error || 'Lỗi khi xóa danh mục.');
                    }
                } catch (error) {
                    alert('Lỗi kết nối server.');
                }
            }
        });
    });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function showSuccess(message) {
    const successContainer = document.getElementById('success-container');
    const successMessage = document.getElementById('success-message');
    successMessage.textContent = message;
    successContainer.style.display = 'block';
    setTimeout(() => successContainer.style.display = 'none', 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    fetchCategories();
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

document.addEventListener('DOMContentLoaded', () => {
    createTwinklingStars();
    setInterval(createShootingStar, 1000);
    setInterval(() => {
        for (let i = 0; i < 2; i++) {
            setTimeout(createShootingStar, i * 300);
        }
    }, 6000);
});

window.addEventListener('resize', () => {
    const twinklingContainer = document.getElementById('twinklingStars');
    twinklingContainer.innerHTML = '';
    createTwinklingStars();
});
</script>

<?php include 'app/views/shares/footer.php'; ?>