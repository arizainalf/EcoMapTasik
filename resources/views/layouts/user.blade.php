<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ getSetting()->app_name }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="{{ asset('storage/' . getSetting()->logo) }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('eshopper/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('eshopper/css/style.css') }}" rel="stylesheet">
    <style>
        .offcanvas-custom {
            position: fixed;
            top: 0;
            right: -420px;
            /* Geser ke kanan off-screen */
            width: 420px;
            height: 100%;
            z-index: 1050;
            transition: right 0.3s ease;
        }

        .offcanvas-custom.show {
            right: 0;
        }

        @media (max-width: 576px) {
            .offcanvas-custom {
                width: 100%;
                /* Fullscreen di layar kecil */
                right: -100%;
            }

            .offcanvas-custom.show {
                right: 0;
            }
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.0/dist/sweetalert2.all.min.js"></script>
    @stack('styles')
</head>

<body>

    @include('components.user.topbar')
    @include('components.user.header')

    @yield('content')

    @if (auth()->check() && auth()->user()->role == 'user')
        <div id="offcanvasSidebar" class="offcanvas-custom bg-light shadow" style="height:100vh; overflow-y:auto;">

        </div>
    @endif

    @include('components.user.footer')

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('eshopper/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('eshopper/lib/easing/easing.min.js') }}"></script>

    <!-- Contact Javascript File -->
    <script src="{{ asset('eshopper/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('eshopper/lib/easing/easing.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('eshopper/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Buka cart sidebar
            $(document).on('click', '#cartButton',
                function(e) {
                    e.preventDefault();
                    var cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
                    cartOffcanvas.show();
                    loadCartItems();
                });

            $(document).on('click', '.btn-plus-cart', function() {
                const input = $(this).siblings('.cart-quantity');
                input.val((parseInt(input.val()) || 1) + 1);
                updateCartQuantity(input);
            });

            $(document).on('click', '.btn-minus-cart', function() {
                const input = $(this).siblings('.cart-quantity');
                let current = parseInt(input.val()) || 1;
                if (current > 1) {
                    input.val(current - 1);
                    updateCartQuantity(input);
                }
            });

            $(document).on('change', '.cart-quantity', function(e) {
                e.preventDefault();
                updateCartQuantity($(this));
            });

            function updateCartQuantity(input) {
                const quantity = parseInt(input.val()) || 1;
                const cartProductId = input.data('id');
                const cartId = input.data('cart_id');

                const data = {
                    quantity: quantity,
                    cart_id: cartId,
                    _method: 'PUT',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                const url = `/cart/${cartProductId}/quantity`;
                const method = 'POST';

                const successCallback = function(response) {
                    loadCartItems(); // fungsi kamu untuk refresh isi keranjang
                };

                const errorCallback = function(error) {
                    errorToast(error.responseJSON?.message || 'Gagal memperbarui jumlah');
                };

                ajaxCall(url, method, data, successCallback, errorCallback);
            }

            $(document).on('click', '.delete-cart-product', function(e) {
                console.log('diklik')
                e.preventDefault();

                const cartProductId = $(this).data('id');

                const url = `/cart/${cartProductId}`;

                console.log(url)

                const method = 'DELETE';

                const successCallback = function(response) {
                    successToast(response);
                    loadCartItems();
                }

                const errorCallback = function(error) {
                    errorToast(error);
                }

                ajaxCall(url, method, null, successCallback, errorCallback);
            });


            $(document).on('click', '#openOffcanvas', function() {
                $('#offcanvasSidebar').addClass('show');
            });

            $(document).on('click', '#closeOffcanvas', function() {
                $('#offcanvasSidebar').removeClass('show');
            });

            // Optional: Close when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#offcanvasSidebar, #openOffcanvas').length) {
                    $('#offcanvasSidebar').removeClass('show');
                }
            });
            loadCartItems();

        });
    </script>
    @stack('scripts')
</body>

</html>
