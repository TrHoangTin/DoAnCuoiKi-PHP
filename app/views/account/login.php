<?php include(__DIR__ . '/../../views/shares/header.php'); ?>

<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
        height: 100vh;
        width: 100vw;
        background: linear-gradient(145deg, #4e54c8, #8f94fb);
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
    }

    .background-shapes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
    }

    .shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: floatShape 15s infinite ease-in-out;
    }

    .shape:nth-child(1) {
        width: 200px;
        height: 200px;
        top: 10%;
        left: 15%;
        animation-delay: 0s;
    }

    .shape:nth-child(2) {
        width: 150px;
        height: 150px;
        top: 60%;
        left: 70%;
        animation-delay: 3s;
    }

    .shape:nth-child(3) {
        width: 100px;
        height: 100px;
        top: 80%;
        left: 20%;
        animation-delay: 6s;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 30px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 450px;
        padding: 2.5rem;
        position: relative;
        z-index: 1;
        animation: zoomIn 0.8s ease-out;
        backdrop-filter: blur(12px);
    }

    .card-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .card-header h3 {
        margin: 0;
        color: #4e54c8;
        font-weight: 700;
        font-size: 2.3rem;
        letter-spacing: 1px;
    }

    .form-group {
        position: relative;
        margin-bottom: 2rem;
    }

    .form-control {
        width: 100%;
        padding: 12px 20px;
        border: 2px solid rgba(78, 84, 200, 0.2);
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.5);
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #4e54c8;
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 15px rgba(78, 84, 200, 0.2);
    }

    .form-label {
        position: absolute;
        top: -10px;
        left: 20px;
        background: #fff;
        padding: 0 8px;
        color: #4e54c8;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(90deg, #4e54c8, #8f94fb);
        border: none;
        border-radius: 15px;
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.4s ease;
    }

    .btn-login:hover {
        background: linear-gradient(90deg, #8f94fb, #4e54c8);
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(78, 84, 200, 0.3);
    }

    .alert {
        border-radius: 15px;
        margin-bottom: 2rem;
        background: rgba(255, 255, 255, 0.8);
        border: none;
        animation: fadeIn 0.5s ease;
    }

    .register-link {
        color: #4e54c8;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .register-link:hover {
        color: #8f94fb;
        text-decoration: underline;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes floatShape {
        0%, 100% {
            transform: translate(0, 0);
        }
        50% {
            transform: translate(50px, 50px);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>


<div class="login-container">
    <div class="background-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <div class="login-card">
        <div class="card-header">
            <h3>Đăng Nhập</h3>
        </div>
        <div class="card-body">
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

            <form action="/webbanhang/Account/login" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" required>
                    <label for="username" class="form-label">Tên đăng nhập</label>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <label for="password" class="form-label">Mật khẩu</label>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login">Đăng Nhập</button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p>Chưa có tài khoản? 
                    <a href="/webbanhang/Account/register" class="register-link">Đăng ký ngay</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../../views/shares/footer.php'); ?>

<script>
    // Hiệu ứng focus cho input
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
</script>