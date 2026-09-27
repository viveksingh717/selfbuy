<footer class="footer footer-dark">
    <div class="footer-middle">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="widget widget-about">
                        <img src="{{ asset('light_logo.png') }}" class="footer-logo" alt="Footer Logo" width="106"
                            height="30">
                        <p>{{ setting('footer_about_text', 'SelfBuy is your one-stop online shopping destination for quality products, exclusive deals, and a seamless shopping experience. Shop Smart, Buy Better with SelfBuy.') }}</p>

                        {{-- Profile links from Admin > System Settings > Social; an icon only shows once its URL is set --}}
                        @php
                            $socials = collect([
                                'facebook_url'  => ['Facebook', 'icon-facebook-f'],
                                'twitter_url'   => ['Twitter', 'icon-twitter'],
                                'instagram_url' => ['Instagram', 'icon-instagram'],
                                'youtube_url'   => ['Youtube', 'icon-youtube'],
                                'linkedin_url'  => ['LinkedIn', 'icon-linkedin'],
                            ])->filter(fn ($meta, $key) => filled(setting($key)));
                        @endphp
                        @if ($socials->isNotEmpty())
                            <div class="social-icons">
                                @foreach ($socials as $key => [$label, $icon])
                                    <a href="{{ setting($key) }}" class="social-icon" title="{{ $label }}" target="_blank" rel="noopener"><i class="{{ $icon }}"></i></a>
                                @endforeach
                            </div><!-- End .soial-icons -->
                        @endif
                    </div><!-- End .widget about-widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Useful Links</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('about') }}">About Us</a></li>
                            <li><a href="{{ route('faq') }}">FAQ</a></li>
                            <li><a href="{{ route('contact') }}">Contact Us</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">Customer Service</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="{{ route('payment') }}">Payment Methods</a></li>
                            <li><a href="{{ route('money_back_guarantee') }}">Money-back Guarantee</a></li>
                            <li><a href="{{ route('refund_policy') }}">Returns &amp; Refunds</a></li>
                            <li><a href="{{ route('shipping') }}">Shipping</a></li>
                            <li><a href="{{ route('terms_conditions') }}">Terms &amp; Conditions</a></li>
                            <li><a href="{{ route('privacy_policy') }}">Privacy Policy</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->

                <div class="col-sm-6 col-lg-3">
                    <div class="widget">
                        <h4 class="widget-title">My Account</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            @guest
                                <li><a href="#signin-modal" data-toggle="modal">Sign In</a></li>
                                <li><a href="{{ route('track_order') }}">Track My Order</a></li>
                                <li><a href="{{ route('cart.index') }}">View Cart</a></li>
                                <li><a href="{{ route('wishlist.index') }}">My Wishlist</a></li>
                            @else
                                <li><a href="{{ route('myaccount') }}">My Account</a></li>
                                <li><a href="{{ route('track_order') }}">Track My Order</a></li>
                                <li><a href="{{ route('cart.index') }}">View Cart</a></li>
                                <li><a href="{{ route('wishlist.index') }}">My Wishlist</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" style="background:none;border:0;padding:0;font:inherit;color:inherit;cursor:pointer;">Logout</button>
                                    </form>
                                </li>
                            @endguest
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-6 col-lg-3 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .footer-middle -->

    <div class="footer-bottom">
        <div class="container">
            <p class="footer-copyright">Copyright © {{ date('Y') }} {{ setting('footer_copyright', 'SelfBuy Store. All Rights Reserved.') }}</p>
            <!-- End .footer-copyright -->
            <figure class="footer-payments">
                <img src="{{ asset('assets/images/payments.png') }}" alt="Payment methods" width="272"
                    height="20">
            </figure><!-- End .footer-payments -->
        </div><!-- End .container -->
    </div><!-- End .footer-bottom -->
</footer><!-- End .footer -->
