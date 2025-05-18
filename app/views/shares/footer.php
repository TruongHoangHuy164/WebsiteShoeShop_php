<?php
$base_url = '/WebBanHang/';
?>
</div>
<footer class="bg-light text-center text-lg-start mt-4">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase">Quản lý sản phẩm</h5>
                <p>Hệ thống quản lý sản phẩm giúp bạn theo dõi và cập nhật thông tin sản phẩm dễ dàng.</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase">Liên kết nhanh</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?php echo $base_url; ?>Product" class="text-dark">Danh sách sản phẩm</a></li>
                    <li><a href="<?php echo $base_url; ?>Product/add" class="text-dark">Thêm sản phẩm</a></li>
                    <li><a href="<?php echo $base_url; ?>Category/list" class="text-dark">Danh sách danh mục</a></li>
                    <li><a href="<?php echo $base_url; ?>Product/cart" class="text-dark">Giỏ hàng</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase">Kết nối với chúng tôi</h5>
                <a href="#" class="text-dark me-3 social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-dark me-3 social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-dark me-3 social-icon"><i class="fab fa-instagram"></i></a>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase">Địa chỉ</h5>
                <p>24 Linh Trung, Linh Trung, Thủ Đức, TP. Hồ Chí Minh</p>
                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=24+Linh+Trung,+Linh+Trung,+Thủ+Đức,+TP.+Hồ+Chí+Minh,+Vietnam&zoom=15"
                        width="100%" height="200" style="border:0; border-radius: 10px;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: #ffccde; color: #4a4a4a;">
        © 2025 Quản lý sản phẩm. All rights reserved.
    </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
    .social-icon {
        transition: transform 0.3s ease, color 0.3s ease;
    }
    .social-icon:hover {
        transform: scale(1.2);
        color: #ff6b81 !important;
    }
    .map-container {
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .map-container:hover {
        transform: scale(1.02);
    }
</style>
</body>
</html>