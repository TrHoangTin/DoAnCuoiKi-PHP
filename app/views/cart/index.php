<?php include(__DIR__ . '/../../views/shares/header.php'); ?>

<div class="container mt-4">
    <h1 class="mb-4">Giỏ Hàng</h1>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            Giỏ hàng của bạn đang trống. <a href="/webbanhang/Product">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <form action="/webbanhang/Cart/update" method="post">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($product->image): ?>
                                            <img src="/webbanhang/<?= htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8') ?>" 
                                                 alt="<?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>" 
                                                 class="img-thumbnail me-3" style="width: 80px;">
                                        <?php endif; ?>
                                        <div>
                                            <h5><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></h5>
                                            <p class="text-muted mb-0"><?= htmlspecialchars($product->category_name ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle"><?= number_format($product->price, 0, ',', '.') ?>₫</td>
                                <td class="align-middle">
                                    <input type="number" name="quantity[<?= $product->id ?>]" 
                                           value="<?= $product->quantity ?>" min="1" class="form-control" style="width: 80px;">
                                </td>
                                <td class="align-middle"><?= number_format($product->subtotal, 0, ',', '.') ?>₫</td>
                                <td class="align-middle">
                                    <a href="/webbanhang/Cart/remove/<?= $product->id ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                            <td colspan="2" class="fw-bold"><?= number_format($total, 0, ',', '.') ?>₫</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mb-4">
                <a href="/webbanhang/Product" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
                <div>
                    <button type="submit" class="btn btn-warning me-2">
                        <i class="fas fa-sync-alt"></i> Cập nhật giỏ hàng
                    </button>
                    <a href="/webbanhang/Cart/checkout" class="btn btn-success">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include(__DIR__ . '/../../views/shares/footer.php'); ?>