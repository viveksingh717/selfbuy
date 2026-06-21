<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\CommonController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

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
    });

    // Routes for authenticated admin users (adminAuth middleware applied)
    Route::middleware(['adminAuth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/logout', [DashboardController::class, 'logout'])->name('admin.logout');

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


    });
});