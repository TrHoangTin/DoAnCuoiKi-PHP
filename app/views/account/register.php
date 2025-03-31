<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center">Đăng Ký Tài Khoản</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
                            <?php unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/webbanhang/Account/register" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control <?= isset($_SESSION['register_errors']['username']) ? 'is-invalid' : '' ?>" 
                                   id="username" name="username" 
                                   value="<?= htmlspecialchars($_SESSION['old_input']['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            <?php if (isset($_SESSION['register_errors']['username'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($_SESSION['register_errors']['username'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php unset($_SESSION['register_errors']['username']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control <?= isset($_SESSION['register_errors']['password']) ? 'is-invalid' : '' ?>" 
                                   id="password" name="password" required>
                            <?php if (isset($_SESSION['register_errors']['password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($_SESSION['register_errors']['password'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php unset($_SESSION['register_errors']['password']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control <?= isset($_SESSION['register_errors']['confirm_password']) ? 'is-invalid' : '' ?>" 
                                   id="confirm_password" name="confirm_password" required>
                            <?php if (isset($_SESSION['register_errors']['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($_SESSION['register_errors']['confirm_password'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php unset($_SESSION['register_errors']['confirm_password']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control <?= isset($_SESSION['register_errors']['fullname']) ? 'is-invalid' : '' ?>" 
                                   id="fullname" name="fullname" 
                                   value="<?= htmlspecialchars($_SESSION['old_input']['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                            <?php if (isset($_SESSION['register_errors']['fullname'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($_SESSION['register_errors']['fullname'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                                <?php unset($_SESSION['register_errors']['fullname']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Đăng Ký</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>Đã có tài khoản? <a href="/webbanhang/Account/login">Đăng nhập ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
unset($_SESSION['register_errors']);
unset($_SESSION['old_input']);
include 'app/views/shares/footer.php'; 
?>