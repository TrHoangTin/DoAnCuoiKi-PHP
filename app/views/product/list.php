<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Product List</h1>
        <a href="/webbanhang/Product/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">No products found.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product->id, ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <?php if ($product->image): ?>
                                    <img src="/webbanhang/<?= htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="Product Image" 
                                         class="img-thumbnail" 
                                         style="max-width: 100px;">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(substr($product->description, 0, 50), ENT_QUOTES, 'UTF-8') ?>...</td>
                            <td>$<?= number_format($product->price, 2) ?></td>
                            <td><?= htmlspecialchars($product->category_name ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="/webbanhang/Product/show/<?= $product->id ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/webbanhang/Product/edit/<?= $product->id ?>" 
                                       class="btn btn-warning btn-sm" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/webbanhang/Product/delete/<?= $product->id ?>" 
                                       class="btn btn-danger btn-sm" 
                                       title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="/webbanhang/Product/addToCart/<?= $product->id ?>" 
                                       class="btn btn-success btn-sm" 
                                       title="Add to Cart">
                                        <i class="fas fa-cart-plus"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>