{{-- Add-to-cart / wishlist buttons on home page product cards (same endpoints as the shop grid). --}}
<script>
    $(document).on('click', '.home-add-to-cart', function (e) {
        var $btn = $(this);

        // Products with variants link through to the details page to pick options.
        if ($btn.hasClass('disabled') || $btn.data('has-variant') == 1) {
            if ($btn.hasClass('disabled')) e.preventDefault();
            return;
        }

        e.preventDefault();

        $.ajax({
            url: '{{ route('cart.add') }}',
            method: 'POST',
            data: { product_id: $btn.data('product-id'), qty: 1 },
            success: function (res) {
                if (res.success) {
                    $('.cart-count').text(res.data.cart_count);
                    $('#cart-dropdown-content').html(res.data.cart_dropdown_html);
                    Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: res.message });
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to add product to cart';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
            }
        });
    });

    $(document).on('click', '.home-add-to-wishlist', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var productId = $btn.data('product-id');

        $.ajax({
            url: '{{ route('wishlist.toggle') }}',
            method: 'POST',
            data: { product_id: productId },
            success: function (res) {
                if (!res.success) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: res.message });
                    return;
                }

                // The same product can appear in several tabs - keep every copy in sync.
                $('.home-add-to-wishlist[data-product-id="' + productId + '"]').toggleClass('in-wishlist', res.data.action === 'added');
                $('.wishlist-count').text(res.data.wishlist_count);
                Swal.fire({ icon: 'success', title: res.message, timer: 1200, showConfirmButton: false });
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to update wishlist';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
            }
        });
    });
</script>
