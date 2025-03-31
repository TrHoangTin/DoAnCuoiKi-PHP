<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0">Đặt hàng thành công!</h2>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="card-title">Cảm ơn bạn đã đặt hàng</h3>
                    <p class="card-text">Chúng tôi đã nhận được đơn hàng của bạn và sẽ xử lý trong thời gian sớm nhất.</p>
                    <p class="card-text">Mã đơn hàng của bạn: <strong>#<?= rand(1000, 9999) ?></strong></p>
                    
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <a href="/webbanhang/Product" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                        </a>
                        <a href="/webbanhang/Account/profile" class="btn btn-outline-secondary">
                            <i class="fas fa-user"></i> Xem đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>