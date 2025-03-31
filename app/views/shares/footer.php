</main>

<footer class="footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Web Ban Hang</h5>
                <p>A simple e-commerce application built with PHP and MySQL</p>
            </div>
            <div class="col-md-3">
                <h5>Links</h5>
                <ul class="list-unstyled">
                    <li><a href="/webbanhang/Product">Products</a></li>
                    <li><a href="/webbanhang/Category">Categories</a></li>
                    <li><a href="/webbanhang/About">About Us</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-envelope"></i> info@webbanhang.com</li>
                    <li><i class="fas fa-phone"></i> +123 456 7890</li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> Web Ban Hang. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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