<?php

namespace App\Services;

use App\Models\HomePartner;
use App\Models\HomeSetting;
use App\Models\HomeSlide;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomeSettingService
{
    private const CACHE_KEY = 'home_settings';
    private const SLIDES_CACHE_KEY = 'home_slides';
    private const PARTNERS_CACHE_KEY = 'home_partners';
    private const FILE_DIR  = 'home';

    /** In-request copy so repeated home_setting() calls don't hit the cache store again. */
    private static ?array $loaded = null;

    /**
     * Drives the admin form only (tabs, labels, input types, defaults).
     * Every key here must match a column on the home_settings table.
     * Carousel slides (home_slides) and partner logos (home_partners) are separate rows - see saveSlides()/savePartners().
     * 'placeholder' is the bundled image shown until the admin uploads one.
     *
     * type: text | textarea | boolean | file
     */
    public static function schema(): array
    {
        return [
            'sections' => [
                'label'  => 'Section Headings',
                'fields' => [
                    'trendy_products_title'  => ['type' => 'text', 'label' => 'Trendy Products Heading',  'default' => 'Trendy Products'],
                    'shop_by_category_title' => ['type' => 'text', 'label' => 'Shop by Category Heading', 'default' => 'Shop by Categories'],
                    'new_arrivals_title'     => ['type' => 'text', 'label' => 'New Arrivals Heading',     'default' => 'Recent Arrivals'],
                ],
            ],

            'signup_offer' => [
                'label'  => 'Sign Up Offer',
                'fields' => [
                    'signup_offer_enabled'     => ['type' => 'boolean',  'label' => 'Show Sign Up Offer', 'default' => '1'],
                    'signup_offer_title'       => ['type' => 'text',     'label' => 'Title',              'default' => 'Sign Up & Get 10% Off'],
                    'signup_offer_button_text' => ['type' => 'text',     'label' => 'Button Name',        'default' => 'SIGN UP'],
                    'signup_offer_button_link' => ['type' => 'text',     'label' => 'Button Link',        'default' => '/register'],
                    'signup_offer_background'  => ['type' => 'file',     'label' => 'Background Image',   'accept' => 'image/*',
                        'placeholder' => 'assets/images/backgrounds/cta/bg-6.jpg'],
                    'signup_offer_text'        => ['type' => 'textarea', 'label' => 'Description',        'default' => 'SelfBuy is your trusted online shopping destination, offering a wide range of quality products at competitive prices.'],
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

    /** The single row, seeded with the schema defaults so a field the admin clears stays blank. */
    private function row(): HomeSetting
    {
        return HomeSetting::query()->firstOrCreate(['id' => 1], self::defaults());
    }

    /** The single settings row as [column => value], cached until a save busts it. */
    public function all(): array
    {
        return self::$loaded ??= Cache::rememberForever(self::CACHE_KEY, function () {
            return $this->row()->toArray();
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
        $row = $this->row();

        foreach (self::fields() as $key => $meta) {
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

                default: // text / textarea
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
        Cache::forget(self::SLIDES_CACHE_KEY);
        Cache::forget(self::PARTNERS_CACHE_KEY);
        self::$loaded = null;
    }

    /** URL for an image setting - the uploaded file, else the bundled placeholder. */
    public function assetUrl(string $key): ?string
    {
        $path = $this->get($key);
        if ($path) {
            return asset('storage/' . $path);
        }

        $placeholder = self::fields()[$key]['placeholder'] ?? null;
        return $placeholder ? asset($placeholder) : null;
    }

    // ── Carousel slides ──────────────────────────────────────

    /** Active slides for the storefront, in display order (cached until a save busts it). */
    public function slides(): Collection
    {
        return Cache::rememberForever(self::SLIDES_CACHE_KEY, function () {
            return HomeSlide::active()->ordered()->get();
        });
    }

    /** Every slide (including hidden ones) for the admin form. */
    public function allSlides(): Collection
    {
        return HomeSlide::ordered()->get();
    }

    /**
     * Sync the Carousel tab: update/delete existing slides and create new ones.
     *
     * @param  array  $slides     existing slides: id => [field => value, 'delete' => 1?]
     * @param  array  $newSlides  new slides: index => [field => value]
     * @param  array  $files      uploaded images, same shape: ['slides' => [id => [image, image_mobile]], 'new_slides' => [...]]
     */
    public function saveSlides(array $slides, array $newSlides, array $files): void
    {
        foreach ($slides as $id => $data) {
            $slide = HomeSlide::find($id);
            if (!$slide) {
                continue;
            }

            if (!empty($data['delete'])) {
                $this->deleteImage($slide->image);
                $this->deleteImage($slide->image_mobile);
                $slide->delete();
                continue;
            }

            if (!empty($data['remove_image_mobile'])) {
                $this->deleteImage($slide->image_mobile);
                $slide->image_mobile = null;
            }

            $slide->fill($this->slideValues($data));
            $this->storeImages($slide, $files['slides'][$id] ?? [], ['image', 'image_mobile'], 'slides');
            $slide->save();
        }

        foreach ($newSlides as $index => $data) {
            $slide = new HomeSlide($this->slideValues($data));
            $this->storeImages($slide, $files['new_slides'][$index] ?? [], ['image', 'image_mobile'], 'slides');
            $slide->save();
        }

        $this->flush();
    }

    private function slideValues(array $data): array
    {
        $values = [];
        foreach (['subtitle', 'title', 'description', 'button_text', 'button_link'] as $field) {
            $values[$field] = isset($data[$field]) ? trim((string) $data[$field]) : null;
        }
        $values['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $values['status']     = !empty($data['status']);
        return $values;
    }

    // ── Partner logos ────────────────────────────────────────

    /** Active partner logos for the storefront, in display order (cached until a save busts it). */
    public function partners(): Collection
    {
        return Cache::rememberForever(self::PARTNERS_CACHE_KEY, function () {
            return HomePartner::active()->ordered()->get();
        });
    }

    /** Every partner (including hidden ones) for the admin form. */
    public function allPartners(): Collection
    {
        return HomePartner::ordered()->get();
    }

    /**
     * Sync the Partners tab: update/delete existing partners and create new ones.
     * Same shapes as saveSlides(), keyed 'partners' / 'new_partners'.
     */
    public function savePartners(array $partners, array $newPartners, array $files): void
    {
        foreach ($partners as $id => $data) {
            $partner = HomePartner::find($id);
            if (!$partner) {
                continue;
            }

            if (!empty($data['delete'])) {
                $this->deleteImage($partner->image);
                $partner->delete();
                continue;
            }

            $partner->fill($this->partnerValues($data));
            $this->storeImages($partner, $files['partners'][$id] ?? [], ['image'], 'partners');
            $partner->save();
        }

        foreach ($newPartners as $index => $data) {
            $partner = new HomePartner($this->partnerValues($data));
            $this->storeImages($partner, $files['new_partners'][$index] ?? [], ['image'], 'partners');
            $partner->save();
        }

        $this->flush();
    }

    private function partnerValues(array $data): array
    {
        return [
            'name'       => isset($data['name']) ? trim((string) $data['name']) : null,
            'link'       => isset($data['link']) ? trim((string) $data['link']) : null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'status'     => !empty($data['status']),
        ];
    }

    // ── Shared image handling (slides, partners) ─────────────

    private function storeImages(Model $row, array $files, array $fields, string $folder): void
    {
        foreach ($fields as $field) {
            if (isset($files[$field]) && $files[$field] instanceof UploadedFile) {
                $this->deleteImage($row->{$field});
                $file = $files[$field];
                $row->{$field} = $file->storeAs(
                    self::FILE_DIR . '/' . $folder,
                    $field . '-' . uniqid() . '.' . $file->getClientOriginalExtension(),
                    'public'
                );
            }
        }
    }

    /** Only uploads are deleted - bundled theme images ("assets/...") are left alone. */
    private function deleteImage(?string $path): void
    {
        if ($path && !str_starts_with($path, 'assets/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
