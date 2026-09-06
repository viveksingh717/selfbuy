<?php

namespace Database\Seeders;

use App\Models\PageSetting;
use Illuminate\Database\Seeder;

class PageSettingSeeder extends Seeder
{
    /**
     * Creates the fixed set of storefront pages so there is something to edit.
     * Keyed on slug with firstOrCreate, so re-running never overwrites content
     * an admin has already changed.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug'              => 'about-us',
                'name'              => 'About Us',
                'label'             => 'About Us',
                'title'             => 'About Us',
                'short_description' => '<p>SelfBuy started with a simple idea: online shopping shouldn\'t feel like a gamble.</p>',
                'description'       => '<p>We curate quality products across categories, keep pricing honest, and back every order with secure checkout and dependable delivery.</p>',
            ],
            [
                'slug'              => 'contact-us',
                'name'              => 'Contact Us',
                'label'             => 'Contact Us',
                'title'             => 'Contact Us',
                'short_description' => '<p>Have a question about an order or a product? Get in touch and we\'ll help.</p>',
                'description'       => null,
            ],
            [
                'slug'              => 'faq',
                'name'              => 'FAQ',
                'label'             => 'FAQ',
                'title'             => 'Frequently Asked Questions',
                'short_description' => '<p>Answers to the questions we get asked most often.</p>',
                'description'       => null,
            ],
            [
                'slug'              => 'payment-method',
                'name'              => 'Payment Method',
                'label'             => 'Payment Method',
                'title'             => 'Payment Methods',
                'short_description' => '<p>All the ways you can pay for your order on SelfBuy.</p>',
                'description'       => null,
            ],
            [
                'slug'              => 'shipping',
                'name'              => 'Shipping',
                'label'             => 'Shipping',
                'title'             => 'Shipping Information',
                'short_description' => '<p>How and when your SelfBuy orders are delivered.</p>',
                'description'       => null,
            ],
            [
                'slug'              => 'refund-policy',
                'name'              => 'Refund Policy',
                'label'             => 'Refund Policy',
                'title'             => 'Refund Policy',
                'short_description' => '<p>How returns, replacements and refunds work on SelfBuy.</p>',
                'description'       => null,
            ],
            [
                'slug'              => 'privacy-policy',
                'name'              => 'Privacy Policy',
                'label'             => 'Privacy Policy',
                'title'             => 'Privacy Policy',
                'short_description' => null,
                'description'       => null,
            ],
            [
                'slug'              => 'terms-and-conditions',
                'name'              => 'Terms & Conditions',
                'label'             => 'Terms & Conditions',
                'title'             => 'Terms & Conditions',
                'short_description' => null,
                'description'       => null,
            ],
        ];

        foreach ($pages as $page) {
            PageSetting::firstOrCreate(
                ['slug' => $page['slug']],
                array_merge($page, ['status' => 1])
            );
        }
    }
}
