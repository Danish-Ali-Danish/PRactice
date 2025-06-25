<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ecommerce')</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --bg-light: #ffffff;
            --text-light: #111827;
            --bg-dark: #1e293b;
            --text-dark: #f1f5f9;
            --primary-color: #6366f1;
        }

        [data-theme='light'] {
            background-color: var(--bg-light);
            color: var(--text-light);
        }

        [data-theme='dark'] {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .theme-toggle {
            cursor: pointer;
        }

        .footer {
            margin-top: auto;
            padding: 1rem 0;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.05);
        }
        .breadcrumb a {
    text-decoration: none;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-4">
        <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">ShopNow</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item me-3">
                    <div class="position-relative">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search products..." autocomplete="off">
                        <div id="searchResults" class="list-group position-absolute w-100 z-3 shadow-sm" style="top: 100%; display: none;"></div>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/products') }}">Products</a></li>
                <li class="nav-item position-relative">
    <a class="nav-link" href="{{ url('/cart') }}">
        <i class="fas fa-shopping-cart"></i> Cart
        <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75rem;">
            0
        </span>
    </a>
</li>

                <li class="nav-item"><a class="nav-link" href="{{ url('/checkout') }}">Checkout</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/orders') }}">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/wishlist') }}"><i class="fas fa-heart text-danger me-1"></i>Wishlist</a></li>

                <li class="nav-item">
                    <button class="btn btn-outline-secondary ms-3 theme-toggle" id="toggleTheme">
                        <i class="fas fa-moon"></i>
                    </button>
                </li>
            </ul>

        </div>
    </nav>

    <!-- Content Section -->
    <main class="container py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0">&copy; {{ date('Y') }} ShopNow. All rights reserved.</p>
    </footer>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<div id="ajax-loader" class="position-fixed top-50 start-50 translate-middle d-none z-3">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Script -->
    <script>
        const themeToggle = document.getElementById('toggleTheme');
        const htmlEl = document.documentElement;

        // Apply saved theme
        const currentTheme = localStorage.getItem('theme') || 'light';
        htmlEl.setAttribute('data-theme', currentTheme);
        updateIcon(currentTheme);

        // Toggle event
        themeToggle.addEventListener('click', () => {
            const newTheme = htmlEl.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            htmlEl.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            const icon = themeToggle.querySelector('i');
            icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    </script>

    @stack('scripts')
    <script>
    // Show/hide loader
    $(document).ajaxStart(function () {
        $('#ajax-loader').removeClass('d-none');
    }).ajaxStop(function () {
        $('#ajax-loader').addClass('d-none');
    });

    // Toast utility
    function showToast(message, type = 'success') {
        const $toast = $('#toastMessage');
        $toast.removeClass('bg-success bg-danger').addClass(`bg-${type}`);
        $toast.find('.toast-body').text(message);
        const toast = new bootstrap.Toast($toast[0]);
        toast.show();
    }
</script>

    <script>
    function updateCartCount() {
        $.ajax({
            url: '/api/cart/count',
            method: 'GET',
            success: function (res) {
                $('#cart-count').text(res.count);
            }
        });
    }

    $(document).ready(function () {
        updateCartCount();

        // Update after adding to cart (triggered globally)
        $(document).on('cart:updated', updateCartCount);
    });
</script>

    <script>
    $(document).ready(function () {
        const $searchInput = $('#searchInput');
        const $searchResults = $('#searchResults');

        let typingTimer;
        const delay = 300; // milliseconds

        $searchInput.on('keyup', function () {
            clearTimeout(typingTimer);
            const query = $(this).val().trim();

            if (query.length < 2) {
                $searchResults.hide().empty();
                return;
            }

            typingTimer = setTimeout(() => {
                $.ajax({
                    url: '/api/search',
                    method: 'GET',
                    data: { q: query },
                    success: function (products) {
                        let html = '';
                        if (products.length === 0) {
                            html = `<a class="list-group-item list-group-item-action disabled">No products found</a>`;
                        } else {
                            products.forEach(product => {
                                html += `
                                    <a href="/product/${product.id}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                                        <img src="${product.image}" width="40" height="40" style="object-fit:cover; border-radius: 6px;">
                                        <div>
                                            <div>${product.name}</div>
                                            <small class="text-muted">PKR ${product.price}</small>
                                        </div>
                                    </a>
                                `;
                            });
                        }
                        $searchResults.html(html).fadeIn();
                    },
                    error: function () {
                        $searchResults.html('<a class="list-group-item text-danger">Error loading search</a>').fadeIn();
                    }
                });
            }, delay);
        });

        // Hide results when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#searchInput, #searchResults').length) {
                $searchResults.fadeOut();
            }
        });
    });
</script>
 <!-- Optional JS injection for child views -->
</body>
</html>
