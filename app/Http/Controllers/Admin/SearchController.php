<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\CouponModel;
use App\Models\PageSetting;
use App\Models\ProductModel;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /** How many hits to show per section. */
    private const PER_GROUP = 8;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $groups = [];
        $total  = 0;

        if (mb_strlen($q) >= 2) {
            $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';

            $groups = array_filter([
                $this->group('Products', 'fa-cube',
                    ProductModel::query()
                        ->where(fn ($w) => $w->where('product_name', 'like', $like)
                            ->orWhere('sku', 'like', $like)
                            ->orWhere('product_slug', 'like', $like))
                        ->limit(self::PER_GROUP)->get()
                        ->map(fn ($p) => [
                            'title'    => $p->product_name,
                            'subtitle' => 'SKU: ' . ($p->sku ?: '—'),
                            'url'      => route('admin.edit_product', $p->id),
                        ])),

                $this->group('Categories', 'fa-list',
                    Category::query()
                        ->where(fn ($w) => $w->where('category_name', 'like', $like)
                            ->orWhere('category_slug', 'like', $like))
                        ->limit(self::PER_GROUP)->get()
                        ->map(fn ($c) => [
                            'title'    => $c->category_name,
                            'subtitle' => '/' . $c->category_slug,
                            'url'      => route('admin.edit_category', $c->id),
                        ])),

                $this->group('Sub-categories', 'fa-sitemap',
                    SubCategory::query()
                        ->where(fn ($w) => $w->where('subcategory_name', 'like', $like)
                            ->orWhere('subcategory_slug', 'like', $like))
                        ->limit(self::PER_GROUP)->get()
                        ->map(fn ($s) => [
                            'title'    => $s->subcategory_name,
                            'subtitle' => '/' . $s->subcategory_slug,
                            'url'      => route('admin.edit_subcategory', $s->id),
                        ])),

                $this->group('Brands', 'fa-copyright',
                    Brand::query()
                        ->where(fn ($w) => $w->where('brand_name', 'like', $like)
                            ->orWhere('brand_slug', 'like', $like))
                        ->limit(self::PER_GROUP)->get()
                        ->map(fn ($b) => [
                            'title'    => $b->brand_name,
                            'subtitle' => '/' . $b->brand_slug,
                            'url'      => route('admin.edit_brand', $b->id),
                        ])),

                $this->group('Coupons', 'fa-ticket',
                    CouponModel::query()
                        ->where(fn ($w) => $w->where('coupon_name', 'like', $like)
                            ->orWhere('coupon_code', 'like', $like))
                        ->limit(self::PER_GROUP)->get()
                        ->map(fn ($c) => [
                            'title'    => $c->coupon_name,
                            'subtitle' => $c->coupon_code,
                            'url'      => route('admin.edit_coupon', $c->id),
                        ])),

                $this->group('Pages', 'fa-file-text-o',
                    PageSetting::query()
                        ->where(fn ($w) => $w->where('name', 'like', $like)
                            ->orWhere('slug', 'like', $like)
                            ->orWhere('title', 'like', $like))
                        ->limit(self::PER_GROUP)->get()
                        ->map(fn ($p) => [
                            'title'    => $p->name,
                            'subtitle' => '/' . $p->slug,
                            'url'      => route('admin.edit_page', $p->id),
                        ])),

                $this->group('Contact messages', 'fa-envelope',
                    ContactUs::query()
                        ->where(fn ($w) => $w->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like)
                            ->orWhere('subject', 'like', $like))
                        ->latest()->limit(self::PER_GROUP)->get()
                        ->map(fn ($m) => [
                            'title'    => $m->name,
                            'subtitle' => $m->email . ($m->subject ? ' · ' . $m->subject : ''),
                            'url'      => route('admin.contact_us'),
                        ])),
            ]);

            $total = collect($groups)->sum(fn ($g) => count($g['items']));
        }

        return view('admin.pages.search', compact('q', 'groups', 'total'));
    }

    private function group(string $label, string $icon, $items): ?array
    {
        $items = $items->all();

        return $items ? compact('label', 'icon', 'items') : null;
    }
}
