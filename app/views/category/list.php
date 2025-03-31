<?php include __DIR__ . '/../shares/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý Danh mục</h1>
        <a href="/WEBBANHANG/category/add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Thêm Danh mục
        </a>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Bảng danh sách danh mục -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">ID</th>
                            <th>Tên Danh mục</th>
                            <th>Mô tả</th>
                            <th>Số sản phẩm</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Không có danh mục nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category->id ?></td>
                                <td><?= htmlspecialchars($category->name) ?></td>
                                <td><?= htmlspecialchars($category->description ?? 'Không có mô tả') ?></td>
                                <td><?= $category->product_count ?? 0 ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/WEBBANHANG/category/edit/<?= $category->id ?>" 
                                           class="btn btn-outline-primary"
                                           title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/WEBBANHANG/category/delete/<?= $category->id ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Xóa"
                                           onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>