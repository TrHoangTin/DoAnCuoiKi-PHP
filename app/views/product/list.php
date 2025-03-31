<?php include __DIR__ . '/../shares/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between mb-4">
        <h1>Danh sách sản phẩm</h1>
        <a href="/WEBBANHANG/product/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm sản phẩm
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Danh mục</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product->id ?></td>
                <td><?= htmlspecialchars($product->name) ?></td>
                <td><?= number_format($product->price, 0, ',', '.') ?>₫</td>
                <td><?= htmlspecialchars($product->category_name ?? 'Không có') ?></td>
                <td>
                    <a href="/WEBBANHANG/product/edit/<?= $product->id ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/WEBBANHANG/product/delete/<?= $product->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>