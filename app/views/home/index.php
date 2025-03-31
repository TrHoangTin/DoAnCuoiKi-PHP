<?php include __DIR__ . '/../shares/header.php'; ?>

<div class="container">
    <h1>Trang chủ đang hoạt động!</h1>
    
    <?php if (!empty($featuredProducts)): ?>
    <h2>Sản phẩm nổi bật</h2>
    <div class="row">
        <?php foreach ($featuredProducts as $product): ?>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product->name) ?></h5>
                    <p class="text-danger"><?= number_format($product->price, 0, ',', '.') ?>₫</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>