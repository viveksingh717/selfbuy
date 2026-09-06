<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminContactUsController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\PageSettingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Payments\InstamojoController;
use App\Http\Controllers\Payments\PayPalController;
use App\Http\Controllers\Payments\RazorpayController;
use App\Http\Controllers\Payments\StripeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewVoteController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ShopController::class, 'productDetails'])->name('product.details');
Route::get('/search', [ShopController::class, 'search'])->name('search');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.remove');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::post('/cart/shipping', [CartController::class, 'setShipping'])->name('cart.shipping');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/remove/{id}', [WishlistController::class, 'destroy'])->name('wishlist.remove');

// Guest-accessible like cart/wishlist above — voting Helpful/Unhelpful doesn't
// need an account, only writing a review does (see the 'auth' group below).
Route::post('/reviews/vote', [ReviewVoteController::class, 'store'])->name('reviews.vote');

Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit')->middleware('throttle:6,1');

Route::get('/payment', [HomeController::class, 'payment'])->name('payment');

Route::get('/money-back-guarantee', [HomeController::class, 'money_back_guarantee'])->name('money_back_guarantee');

Route::get('/refund-policy', [HomeController::class, 'refund_policy'])->name('refund_policy');

Route::get('/shipping', [HomeController::class, 'shipping'])->name('shipping');

Route::get('/terms_conditions', [HomeController::class, 'terms_and_conditions'])->name('terms_conditions');

Route::get('/privacy_policy', [HomeController::class, 'privacy_policy'])->name('privacy_policy');

Route::get('/track_order', [HomeController::class, 'track_my_order'])->name('track_order');

Route::get('/blog', [HomeController::class, 'blog'])->name('blog');

Route::middleware('auth')->group(function () {
    Route::get('/myaccount', [AccountController::class, 'index'])->name('myaccount');
    Route::post('/myaccount/details', [AccountController::class, 'updateDetails'])->name('account.details.update');
    Route::post('/myaccount/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/myaccount/address', [AccountController::class, 'updateAddress'])->name('account.address.update');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

// Payment routes are accessible to guests and logged-in users alike (unlike
// login/register/otp below), so they sit outside the 'guest' middleware group.
Route::get('/payment/razorpay/{payment}', [RazorpayController::class, 'show'])->name('payment.razorpay.show');
Route::post('/payment/razorpay/verify', [RazorpayController::class, 'verify'])->name('payment.razorpay.verify');
Route::post('/payment/razorpay/failed', [RazorpayController::class, 'failed'])->name('payment.razorpay.failed');

// Stripe Checkout is a hosted redirect flow, not an in-page widget like Razorpay's —
// "show" bounces straight to Stripe, and Stripe itself redirects back to callback/cancel.
Route::get('/payment/stripe/{payment}', [StripeController::class, 'show'])->name('payment.stripe.show');
Route::get('/payment/stripe/{payment}/callback', [StripeController::class, 'callback'])->name('payment.stripe.callback');
Route::get('/payment/stripe/{payment}/cancel', [StripeController::class, 'cancel'])->name('payment.stripe.cancel');

// PayPal Checkout is likewise a hosted redirect flow (PayPal's Orders API v2).
Route::get('/payment/paypal/{payment}', [PayPalController::class, 'show'])->name('payment.paypal.show');
Route::get('/payment/paypal/{payment}/callback', [PayPalController::class, 'callback'])->name('payment.paypal.callback');
Route::get('/payment/paypal/{payment}/cancel', [PayPalController::class, 'cancel'])->name('payment.paypal.cancel');

// Instamojo's Payment Requests API only takes a single redirect_url — success
// and failure both land on "callback", distinguished by a status query param
// (see InstamojoController::callback()) — so there's no separate cancel route.
Route::get('/payment/instamojo/{payment}', [InstamojoController::class, 'show'])->name('payment.instamojo.show');
Route::get('/payment/instamojo/{payment}/callback', [InstamojoController::class, 'callback'])->name('payment.instamojo.callback');

// Server-to-server only — trusted via signature, not session/CSRF (see bootstrap/app.php
// for the matching CSRF exemption; gateway servers can't supply a CSRF token).
Route::post('/webhooks/razorpay', [RazorpayController::class, 'webhook'])->name('webhooks.razorpay');
Route::post('/webhooks/stripe', [StripeController::class, 'webhook'])->name('webhooks.stripe');
Route::post('/webhooks/paypal', [PayPalController::class, 'webhook'])->name('webhooks.paypal');
Route::post('/webhooks/instamojo', [InstamojoController::class, 'webhook'])->name('webhooks.instamojo');

Route::middleware('guest')->group(function () {
    // Login/register happen via the modal on every page; these bare URLs just
    // land on the homepage with the modal pre-opened (deep links, redirects).
    Route::get('/login', fn () => redirect()->route('home')->with('open_auth_modal', 'signin'))->name('login');
    Route::get('/register', fn () => redirect()->route('home')->with('open_auth_modal', 'register'))->name('register');

    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/auth/google/redirect', [SocialAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'callback'])->name('auth.google.callback');

    Route::post('/otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [OtpController::class, 'resend'])->name('otp.resend');

    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::get('/test', function () {
    $order = \App\Models\Order::with('items')->latest()->first();

    if (!$order) {
        return 'No orders exist yet to preview this template with.';
    }

    return view('emails.templates.order_email', compact('order'));
});

Route::post('/generate_slug', [CommonController::class, 'generate_slug'])->name('generate_slug');

Route::prefix('admin')->group(function () {
    Route::get('/', function () {

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('admin.login');
    });

    // Routes for guests (adminGuest middleware applied)
    Route::middleware(['adminGuest'])->group(function () {
        Route::get('/login', [AuthController::class, 'login'])->name('admin.login');
        Route::post('/login_process', [AuthController::class, 'login_process'])->name('admin.login_process');
        Route::get('/register', [AuthController::class, 'register'])->name('admin.register');
        Route::post('/register_process', [AuthController::class, 'register_process'])->name('admin.register_process');
        Route::get('/terms_condition', [AuthController::class, 'term_condition'])->name('admin.terms_condition');
        Route::get('/forget_password', [AuthController::class, 'forget_password'])->name('admin.forget_password');
        Route::post('/forget_password', [AuthController::class, 'sendResetLink'])->name('admin.password.email');
        Route::get('/reset_password/{token}', [AuthController::class, 'showResetForm'])->name('admin.password.reset');
        Route::post('/reset_password', [AuthController::class, 'reset_password'])->name('admin.reset_password');
    });

    // Routes for authenticated admin users (adminAuth middleware applied)
    Route::middleware(['adminAuth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

        // Global admin search (header search box)
        Route::get('/search', [SearchController::class, 'index'])->name('admin.search');

        // Admin profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('admin.profile.avatar.delete');

        // Category Routes (Protected by adminAuth middleware)
        Route::get('/category', [CategoryController::class, 'index'])->name('admin.category');
        Route::get('/create_category', [CategoryController::class, 'create_category'])->name('admin.create_category');
        Route::post('/process_category', [CategoryController::class, 'process_category'])->name('admin.process_category');
        Route::post('/category_image_upload', [CategoryController::class, 'category_image_upload'])->name('admin.category_image_upload');
        Route::get('/edit_category/{id}', [CategoryController::class, 'edit_category'])->name('admin.edit_category');
        Route::post('/update_category/{id}', [CategoryController::class, 'update_category'])->name('admin.update_category');
        Route::delete('/delete_category/{id}', [CategoryController::class, 'delete_category'])->name('admin.delete_category');

        //Sub Category Routes (Protected by adminAuth middleware)
        Route::get('/sub_category', [SubCategoryController::class, 'index'])->name('admin.sub_category');
        Route::get('/create_subcategory', [SubCategoryController::class, 'create_subcategory'])->name('admin.create_subcategory');
        Route::post('/process_subcategory', [SubCategoryController::class, 'process_subcategory'])->name('admin.process_subcategory');
        Route::get('/edit_subcategory/{id}', [SubCategoryController::class, 'edit_subcategory'])->name('admin.edit_subcategory');
        Route::post('/update_subcategory/{id}', [SubCategoryController::class, 'update_subcategory'])->name('admin.update_subcategory');
        Route::delete('/delete_subcategory/{id}', [SubCategoryController::class, 'delete_subcategory'])->name('admin.delete_subcategory');

        //Brand Routes (Protected by adminAuth middleware)
        Route::get('/brand', [BrandController::class, 'index'])->name('admin.brand');
        Route::get('/create_brand', [BrandController::class, 'create_brand'])->name('admin.create_brand');
        Route::post('/process_brand', [BrandController::class, 'process_brand'])->name('admin.process_brand');
        Route::post('/brand_image_upload', [BrandController::class, 'brand_image_upload'])->name('admin.brand_image_upload');
        Route::get('/edit_brand/{id}', [BrandController::class, 'edit_brand'])->name('admin.edit_brand');
        Route::post('/update_brand/{id}', [BrandController::class, 'update_brand'])->name('admin.update_brand');
        Route::delete('/delete_brand/{id}', [BrandController::class, 'delete_brand'])->name('admin.delete_brand');

        //Colour Routes (Protected by adminAuth middleware)
        Route::get('/color', [ColorController::class, 'index'])->name('admin.color');
        Route::get('/create_color', [ColorController::class, 'create_color'])->name('admin.create_color');
        Route::post('/process_color', [ColorController::class, 'process_color'])->name('admin.process_color');
        Route::get('/edit_color/{id}', [ColorController::class, 'edit_color'])->name('admin.edit_color');
        Route::post('/update_color/{id}', [ColorController::class, 'update_color'])->name('admin.update_color');
        Route::delete('/delete_color/{id}', [ColorController::class, 'delete_color'])->name('admin.delete_color');

        //Size Routes (Protected by adminAuth middleware)
        Route::get('/size', [SizeController::class, 'index'])->name('admin.size');
        Route::get('/create_size', [SizeController::class, 'create_size'])->name('admin.create_size');
        Route::post('/process_size', [SizeController::class, 'process_size'])->name('admin.process_size');
        Route::get('/edit_size/{id}', [SizeController::class, 'edit_size'])->name('admin.edit_size');
        Route::post('/update_size/{id}', [SizeController::class, 'update_size'])->name('admin.update_size');
        Route::delete('/delete_size/{id}', [SizeController::class, 'delete_size'])->name('admin.delete_size');

        //Coupon Routes (Protected by adminAuth middleware)
        Route::get('/coupon', [CouponController::class, 'index'])->name('admin.coupon');
        Route::get('/create_coupon', [CouponController::class, 'create_coupon'])->name('admin.create_coupon');
        Route::post('/process_coupon', [CouponController::class, 'process_coupon'])->name('admin.process_coupon');
        Route::get('/edit_coupon/{id}', [CouponController::class, 'edit_coupon'])->name('admin.edit_coupon');
        Route::post('/update_coupon/{id}', [CouponController::class, 'update_coupon'])->name('admin.update_coupon');
        Route::delete('/delete_coupon/{id}', [CouponController::class, 'delete_coupon'])->name('admin.delete_coupon');
        //Toggle status change route
        Route::post('/coupon_status/{id}', [CouponController::class, 'toggle_status'])->name('admin.coupon_status');

        //Tax Routes (Protected by adminAuth middleware)
        Route::get('/tax', [TaxController::class, 'index'])->name('admin.tax');
        Route::get('/create_tax', [TaxController::class, 'create_tax'])->name('admin.create_tax');
        Route::post('/process_tax', [TaxController::class, 'process_tax'])->name('admin.process_tax');
        Route::get('/edit_tax/{id}', [TaxController::class, 'edit_tax'])->name('admin.edit_tax');
        Route::post('/update_tax/{id}', [TaxController::class, 'update_tax'])->name('admin.update_tax');
        Route::delete('/delete_tax/{id}', [TaxController::class, 'delete_tax'])->name('admin.delete_tax');
        Route::post('/tax_status/{id}',   [TaxController::class, 'toggle_status'])->name('admin.tax_status');

        // Product Routes (Protected by adminAuth middleware)
        Route::get('/product', [ProductController::class, 'index'])->name('admin.product');
        Route::get('/create_product', [ProductController::class, 'create_product'])->name('admin.create_product');
        Route::post('/process_product', [ProductController::class, 'process_product'])->name('admin.process_product');
        Route::get('/edit_product/{id}', [ProductController::class, 'edit_product'])->name('admin.edit_product');
        Route::post('/update_product/{id}', [ProductController::class, 'update_product'])->name('admin.update_product');
        Route::delete('/delete_product/{id}', [ProductController::class, 'delete_product'])->name('admin.delete_product');
        Route::get('/view_product/{id}', [ProductController::class, 'view_product'])->name('admin.view_product');
        Route::post('/product_image_upload', [ProductController::class, 'product_image_upload'])->name('admin.product_image_upload');
        Route::delete('/delete_product_image/{id}', [ProductController::class, 'delete_product_image'])->name('admin.delete_product_image');
        Route::delete('/delete_gallery_image/{id}', [ProductController::class, 'delete_gallery_image'])->name('admin.delete_gallery_image');
        Route::post('/product_status/{id}', [ProductController::class, 'toggle_status'])->name('admin.product_status');
        Route::post('/product_gallery_upload', [ProductController::class, 'product_gallery_upload'])->name('admin.product_gallery_upload');
        Route::post('/product_attribute_upload', [ProductController::class, 'product_attribute_upload'])->name('admin.product_attribute_upload');
        Route::delete('/delete_product_attribute/{id}', [ProductController::class, 'delete_product_attribute'])->name('admin.delete_product_attribute');
        Route::get('/get_subcategories/{category_id}', [ProductController::class, 'get_subcategories'])->name('admin.get_subcategories');

        // Pages Routes (Protected by adminAuth middleware)
        // One list for every static storefront page; pages are seeded, so there
        // is no "create" - admins only edit content, toggle status or soft delete.
        Route::get('/pages', [PageSettingController::class, 'index'])->name('admin.pages');
        Route::get('/edit_page/{id}', [PageSettingController::class, 'edit_page'])->name('admin.edit_page');
        Route::post('/update_page/{id}', [PageSettingController::class, 'update_page'])->name('admin.update_page');
        Route::delete('/delete_page/{id}', [PageSettingController::class, 'delete_page'])->name('admin.delete_page');
        Route::post('/page_status/{id}', [PageSettingController::class, 'toggle_status'])->name('admin.page_status');

        // Sysem Settings Routes (Protected by adminAuth middleware)
        Route::get('/settings', [SystemSettingController::class, 'settings'])->name('admin.settings');
        Route::post('/update_settings', [SystemSettingController::class, 'update_settings'])->name('admin.update_settings');

        // Contact Us Routes (Protected by adminAuth middleware)
        Route::get('/contact_us', [AdminContactUsController::class, 'index'])->name('admin.contact_us');
        Route::get('/contact_us/{id}', [AdminContactUsController::class, 'show'])->name('admin.contact_us.show');
        Route::post('/contact_us/{id}/reply', [AdminContactUsController::class, 'reply'])->name('admin.contact_us.reply');
        Route::post('/contact_us/{id}/status', [AdminContactUsController::class, 'toggle_status'])->name('admin.contact_us.status');
        Route::post('/contact_us/{id}/star', [AdminContactUsController::class, 'toggle_star'])->name('admin.contact_us.star');
        Route::delete('/contact_us/{id}', [AdminContactUsController::class, 'destroy'])->name('admin.contact_us.delete');

        // Help page for Contact Us (Protected by adminAuth middleware)
        Route::get('/help', [AdminContactUsController::class, 'help'])->name('admin.help');

        // Admin Notifications (header bell + full history)
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications');
        Route::get('/notifications/poll', [AdminNotificationController::class, 'poll'])->name('admin.notifications.poll');
        Route::get('/notifications/{id}/open', [AdminNotificationController::class, 'open'])->name('admin.notifications.open');
        Route::post('/notifications/{id}/read', [AdminNotificationController::class, 'markRead'])->name('admin.notifications.read');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])->name('admin.notifications.read_all');
    });
});

Route::get('/{slug?}/{slug2?}', [ShopController::class, 'index'])->name('shop');