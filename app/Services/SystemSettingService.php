<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemSettingService
{
    private const CACHE_KEY = 'system_settings';
    private const FILE_DIR  = 'settings';

    /**
     * Drives the admin form only (tabs, labels, input types, defaults).
     * Every key here must match a column on the system_settings table.
     *
     * type: text | textarea | boolean | number | select | file
     */
    public static function schema(): array
    {
        return [
            'general' => [
                'label'  => 'General',
                'fields' => [
                    'site_name'        => ['type' => 'text',    'label' => 'Site Name',            'default' => 'SelfBuy'],
                    'site_tagline'     => ['type' => 'text',    'label' => 'Tagline',              'default' => 'Shop Smart, Buy Better'],
                    'logo_horizontal'  => ['type' => 'file',    'label' => 'Header Logo (horizontal)', 'accept' => 'image/*'],
                    'logo_light'       => ['type' => 'file',    'label' => 'Light Logo (dark backgrounds / footer)', 'accept' => 'image/*'],
                    'logo_dark'        => ['type' => 'file',    'label' => 'Dark Logo (light backgrounds)', 'accept' => 'image/*'],
                    'favicon'          => ['type' => 'file',    'label' => 'Favicon',              'accept' => 'image/x-icon,image/png,image/svg+xml'],
                    'admin_logo'       => ['type' => 'file',    'label' => 'Admin Panel Logo',     'accept' => 'image/*'],
                    'maintenance_mode' => ['type' => 'boolean', 'label' => 'Maintenance Mode',     'default' => '0'],
                ],
            ],

            'contact' => [
                'label'  => 'Contact',
                'fields' => [
                    'contact_email'     => ['type' => 'text',     'label' => 'Contact Email',   'default' => 'support@selfbuy.com'],
                    'support_email'     => ['type' => 'text',     'label' => 'Support Email',   'default' => 'support@selfbuy.com'],
                    'contact_phone'     => ['type' => 'text',     'label' => 'Primary Phone',   'default' => '+91 90040 69694'],
                    'contact_phone_alt' => ['type' => 'text',     'label' => 'Secondary Phone', 'default' => '+91 86899 61600'],
                    'whatsapp_number'   => ['type' => 'text',     'label' => 'WhatsApp Number', 'default' => ''],
                    'address_line'      => ['type' => 'textarea', 'label' => 'Address',         'default' => 'New Jay Om Shanti CHS, P. K. Road, Mira Bhayandar Road, Mira Road (East)'],
                    'address_city'      => ['type' => 'text',     'label' => 'City',            'default' => 'Thane'],
                    'address_state'     => ['type' => 'text',     'label' => 'State',           'default' => 'Maharashtra'],
                    'address_country'   => ['type' => 'text',     'label' => 'Country',         'default' => 'India'],
                    'address_postcode'  => ['type' => 'text',     'label' => 'Postal Code',     'default' => '401107'],
                    'google_maps_url'   => ['type' => 'text',     'label' => 'Google Maps URL', 'default' => ''],
                ],
            ],

            'localization' => [
                'label'  => 'Localization',
                'fields' => [
                    'default_language'     => ['type' => 'select', 'label' => 'Default Language', 'default' => 'en',
                        'options' => ['en' => 'English', 'hi' => 'Hindi']],
                    'available_languages'  => ['type' => 'text',   'label' => 'Available Languages (comma separated codes)', 'default' => 'en,hi'],
                    'default_currency'     => ['type' => 'select', 'label' => 'Default Currency', 'default' => 'INR',
                        'options' => ['INR' => 'Indian Rupee (₹)', 'USD' => 'US Dollar ($)']],
                    'available_currencies' => ['type' => 'text',   'label' => 'Available Currencies (comma separated codes)', 'default' => 'INR,USD'],
                    'currency_symbol'      => ['type' => 'text',   'label' => 'Currency Symbol',  'default' => '₹'],
                    'default_country'      => ['type' => 'select', 'label' => 'Default Country',  'default' => 'IN',
                        'options' => ['IN' => 'India', 'US' => 'United States']],
                    'timezone'             => ['type' => 'text',   'label' => 'Timezone',        'default' => 'Asia/Kolkata'],
                ],
            ],

            'payment' => [
                'label'  => 'Payment',
                'fields' => [
                    'payment_image'    => ['type' => 'file',    'label' => 'Payment Image (methods strip)', 'accept' => 'image/*'],
                    'payment_upi_icon' => ['type' => 'file',    'label' => 'UPI Icon',              'accept' => 'image/*'],
                    'cod_enabled'      => ['type' => 'boolean', 'label' => 'Cash on Delivery Enabled', 'default' => '1'],
                    'payment_note'     => ['type' => 'textarea','label' => 'Checkout Payment Note', 'default' => ''],
                ],
            ],

            'footer' => [
                'label'  => 'Footer',
                'fields' => [
                    'footer_about_text' => ['type' => 'textarea', 'label' => 'Footer About Text', 'default' => 'SelfBuy is your one-stop online shopping destination for quality products, exclusive deals, and a seamless shopping experience.'],
                    'footer_copyright'  => ['type' => 'text',     'label' => 'Copyright Text',   'default' => 'SelfBuy Store. All Rights Reserved.'],
                    'newsletter_text'   => ['type' => 'textarea', 'label' => 'Newsletter Blurb', 'default' => 'Subscribe to get the latest deals and offers.'],
                ],
            ],

            'social' => [
                'label'  => 'Social Links',
                'fields' => [
                    'facebook_url'  => ['type' => 'text', 'label' => 'Facebook URL',   'default' => ''],
                    'twitter_url'   => ['type' => 'text', 'label' => 'Twitter / X URL', 'default' => ''],
                    'instagram_url' => ['type' => 'text', 'label' => 'Instagram URL',  'default' => ''],
                    'youtube_url'   => ['type' => 'text', 'label' => 'YouTube URL',    'default' => ''],
                    'linkedin_url'  => ['type' => 'text', 'label' => 'LinkedIn URL',   'default' => ''],
                ],
            ],

            'hours' => [
                'label'  => 'Business Hours',
                'fields' => [
                    'business_hours'         => ['type' => 'text', 'label' => 'Business Hours',        'default' => 'Mon - Sat, 10:00 AM - 7:00 PM'],
                    'support_hours'          => ['type' => 'text', 'label' => 'Support Hours',         'default' => 'Mon - Fri, 9:00 AM - 6:00 PM'],
                    'order_processing_time'  => ['type' => 'text', 'label' => 'Order Processing Time', 'default' => '1 - 2 business days'],
                    'delivery_time_estimate' => ['type' => 'text', 'label' => 'Delivery Estimate',    'default' => '3 - 7 business days'],
                    'order_cutoff_time'      => ['type' => 'text', 'label' => 'Same-day Dispatch Cut-off', 'default' => '2:00 PM'],
                ],
            ],

            'seo' => [
                'label'  => 'SEO',
                'fields' => [
                    'meta_title'       => ['type' => 'text',     'label' => 'Default Meta Title',       'default' => 'SelfBuy - Online Shopping'],
                    'meta_description' => ['type' => 'textarea', 'label' => 'Default Meta Description', 'default' => 'Shop quality products at fair prices on SelfBuy.'],
                    'meta_keywords'    => ['type' => 'text',     'label' => 'Default Meta Keywords',    'default' => 'online shopping, ecommerce, india'],
                    'og_image'         => ['type' => 'file',     'label' => 'Social Share Image (OG)',  'accept' => 'image/*'],
                ],
            ],
        ];
    }

    /** Flat [key => meta] map across every group. */
    public static function fields(): array
    {
        $flat = [];
        foreach (self::schema() as $group => $section) {
            foreach ($section['fields'] as $key => $meta) {
                $flat[$key] = $meta + ['group' => $group];
            }
        }
        return $flat;
    }

    /** Default value for every setting, from the schema. */
    public static function defaults(): array
    {
        $defaults = [];
        foreach (self::fields() as $key => $meta) {
            if (array_key_exists('default', $meta)) {
                $defaults[$key] = $meta['default'];
            }
        }
        return $defaults;
    }

    /** The single settings row as [column => value], cached until a save busts it. */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return SystemSetting::instance()->toArray();
        });
    }

    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== ''
            ? $all[$key]
            : $default;
    }

    /**
     * Persist a submitted settings form onto the single row.
     *
     * @param  array  $values       column => value
     * @param  array  $files        column => UploadedFile
     * @param  array  $removeFiles  columns whose stored file should be cleared
     */
    public function save(array $values, array $files = [], array $removeFiles = []): void
    {
        $row    = SystemSetting::instance();
        $fields = self::fields();

        foreach ($fields as $key => $meta) {
            switch ($meta['type']) {
                case 'file':
                    if (in_array($key, $removeFiles, true) && $row->{$key}) {
                        Storage::disk('public')->delete($row->{$key});
                        $row->{$key} = null;
                    }

                    if (isset($files[$key]) && $files[$key] instanceof UploadedFile) {
                        if ($row->{$key}) {
                            Storage::disk('public')->delete($row->{$key});
                        }
                        $file = $files[$key];
                        $row->{$key} = $file->storeAs(
                            self::FILE_DIR,
                            $key . '-' . time() . '.' . $file->getClientOriginalExtension(),
                            'public'
                        );
                    }
                    break;

                case 'boolean':
                    $row->{$key} = array_key_exists($key, $values) && $values[$key] ? 1 : 0;
                    break;

                default: // text / textarea / number / select
                    if (array_key_exists($key, $values)) {
                        $val = $values[$key];
                        $row->{$key} = is_string($val) ? trim($val) : $val;
                    }
            }
        }

        $row->save();
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** URL for a file/image setting (or null). */
    public function assetUrl(string $key): ?string
    {
        $path = $this->get($key);
        return $path ? asset('storage/' . $path) : null;
    }
}
