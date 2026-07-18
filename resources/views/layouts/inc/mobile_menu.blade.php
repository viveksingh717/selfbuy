<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="icon-close"></i></span>

        <form action="{{ route('search') }}" method="get" class="mobile-search">
            <label for="mobile-search" class="sr-only">Search</label>
            <input type="search" class="form-control" name="q" id="mobile-search" value="{{ request('q') }}"
                placeholder="Search products..." required>
            <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
        </form>

        <nav class="mobile-nav">
            <ul class="mobile-menu">
                <li class="active">
                    <a href="{{ route('home') }}">Home</a>
                </li>
                @if (isset($mobileCategories) && $mobileCategories->isNotEmpty())
                    @foreach ($mobileCategories as $category)
                        <li>
                            <a href="{{ url($category->category_slug) }}">
                                {{ $category->category_name }}
                                @if ($category->is_featured)
                                    <span class="tip tip-new">New</span>
                                @endif
                            </a>
                            @if ($category->subcategories->isNotEmpty())
                                <ul>
                                    @foreach ($category->subcategories as $subcategory)
                                        <li><a
                                                href="{{ url($category->category_slug . '/' . $subcategory->subcategory_slug) }}">{{ $subcategory->subcategory_name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @endif
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
