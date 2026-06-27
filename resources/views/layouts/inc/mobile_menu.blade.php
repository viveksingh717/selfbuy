<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="icon-close"></i></span>

        <form action="#" method="get" class="mobile-search">
            <label for="mobile-search" class="sr-only">Search</label>
            <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..."
                required>
            <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
        </form>

        <nav class="mobile-nav">
            <ul class="mobile-menu">
                <li class="active">
                    <a href="{{ route('home') }}">Home</a>
                </li>
                <li>
                    <a href="category.html">Shop</a>
                    <ul>
                        <li><a href="#">Shop List</a></li>
                    </ul>
                </li>
                <li>
                    <a href="product.html" class="sf-with-ul">Product</a>
                    <ul>
                        <li><a href="product.html">Default</a></li>
                    </ul>
                </li>
            </ul>
        </nav><!-- End .mobile-nav -->

        <div class="social-icons">
            <a href="https://www.facebook.com/" class="social-icon" target="_blank" title="Facebook"><i
                    class="icon-facebook-f"></i></a>
            <a href="https://twitter.com/" class="social-icon" target="_blank" title="Twitter"><i
                    class="icon-twitter"></i></a>
            <a href="https://www.instagram.com/" class="social-icon" target="_blank" title="Instagram"><i
                    class="icon-instagram"></i></a>
            <a href="https://www.youtube.com/" class="social-icon" target="_blank" title="Youtube"><i
                    class="icon-youtube"></i></a>
        </div><!-- End .social-icons -->
    </div><!-- End .mobile-menu-wrapper -->
</div>
