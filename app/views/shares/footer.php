</main>

<footer class="footer mt-5 py-5 bg-dark text-light">
    <div class="container">
        <div class="row gy-4">
            <!-- Left Section -->
            <div class="col-md-6">
                <h5 class="fw-bold text-uppercase mb-3">Web Ban Hang</h5>
                <p class="text-muted">
                    A simple e-commerce application crafted with PHP and MySQL
                </p>
            </div>

            <!-- Links Section -->
            <div class="col-md-3">
                <h5 class="fw-bold text-uppercase mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="/webbanhang/Product" class="text-decoration-none text-muted hover-link">Products</a>
                    </li>
                    <li class="mb-2">
                        <a href="/webbanhang/Category" class="text-decoration-none text-muted hover-link">Categories</a>
                    </li>
                    <li class="mb-2">
                        <a href="/webbanhang/About" class="text-decoration-none text-muted hover-link">About Us</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Section -->
            <div class="col-md-3">
                <h5 class="fw-bold text-uppercase mb-3">Get in Touch</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:info@webbanhang.com" class="text-muted text-decoration-none hover-link">info@webbanhang.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <span class="text-muted">+123 456 7890</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-4 bg-secondary">

        <!-- Copyright -->
        <div class="text-center">
            <p class="mb-0 text-muted">© <?= date('Y') ?> Web Ban Hang. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom CSS -->
<style>
    .footer {
        background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%);
        transition: all 0.3s ease;
    }

    .hover-link {
        transition: color 0.3s ease;
    }

    .hover-link:hover {
        color: #ffffff !important;
    }

    hr.bg-secondary {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    @media (max-width: 768px) {
        .footer {
            text-align: center;
        }
    }
</style>

<!-- Custom JS -->
<script>
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Confirm before delete
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Handle AJAX requests
    function handleAjaxResponse(response, successCallback, errorCallback) {
        if (response.success) {
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
        } else {
            if (typeof errorCallback === 'function') {
                errorCallback(response);
            } else {
                alert(response.message || 'An error occurred');
            }
        }
    }
</script>
</body>
</html>