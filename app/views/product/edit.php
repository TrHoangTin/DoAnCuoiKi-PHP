<?php include __DIR__ . '/../shares/header.php'; ?>

<div class="container mt-4">
    <h1>Sửa sản phẩm</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/WEBBANHANG/product/update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product->id ?>">
        
        <div class="mb-3">
            <label class="form-label">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product->name) ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product->description) ?></textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Giá</label>
            <input type="number" name="price" class="form-control" value="<?= $product->price ?>" min="0" step="1000" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Danh mục</label>
            <select name="category_id" class="form-select">
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category->id ?>" <?= $category->id == $product->category_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Hình ảnh hiện tại</label>
            <?php if ($product->image): ?>
                <img src="/WEBBANHANG/public/<?= $product->image ?>" class="img-thumbnail mb-2" style="max-height: 200px;">
                <input type="hidden" name="existing_image" value="<?= $product->image ?>">
            <?php else: ?>
                <p>Không có hình ảnh</p>
            <?php endif; ?>
            
            <input type="file" name="image" class="form-control">
        </div>
        
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>

<?php include __DIR__ . '/../shares/footer.php'; ?>