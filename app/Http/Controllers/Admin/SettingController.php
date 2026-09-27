<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HomeSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    private HomeSettingService $homeSettingService;

    public function __construct()
    {
        $this->homeSettingService = new HomeSettingService();
    }

    public function home_settings()
    {
        return view('admin.pages.home_setting', [
            'schema'   => HomeSettingService::schema(),
            'settings' => $this->homeSettingService->all(),
            'slides'   => $this->homeSettingService->allSlides(),
            'partners' => $this->homeSettingService->allPartners(),
        ]);
    }

    public function update_home_settings(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to continue.');
        }

        $fields = HomeSettingService::fields();

        // Build validation rules straight from the schema.
        $rules = [];
        foreach ($fields as $key => $meta) {
            $rules[$key] = match ($meta['type']) {
                'file'    => 'nullable|file|max:4096|mimes:jpg,jpeg,png,gif,webp',
                'number'  => 'nullable|numeric|min:' . ($meta['min'] ?? 0) . '|max:' . ($meta['max'] ?? 999999),
                'boolean' => 'nullable|boolean',
                default   => 'nullable|string|max:5000',
            };
        }

        // Carousel slides (any number): existing ones keyed by id, new ones by form index.
        $image = 'nullable|file|max:4096|mimes:jpg,jpeg,png,gif,webp';
        $rules += [
            'slides'                    => 'nullable|array',
            'slides.*.image'            => $image,
            'slides.*.image_mobile'     => $image,
            'new_slides'                => 'nullable|array',
            'new_slides.*.image'        => 'required|file|max:4096|mimes:jpg,jpeg,png,gif,webp',
            'new_slides.*.image_mobile' => $image,
        ];
        foreach (['slides', 'new_slides'] as $group) {
            $rules += [
                "{$group}.*.subtitle"    => 'nullable|string|max:255',
                "{$group}.*.title"       => 'nullable|string|max:1000',
                "{$group}.*.description" => 'nullable|string|max:2000',
                "{$group}.*.button_text" => 'nullable|string|max:255',
                "{$group}.*.button_link" => 'nullable|string|max:255',
                "{$group}.*.sort_order"  => 'nullable|integer|min:0',
                "{$group}.*.status"      => 'nullable|boolean',
            ];
        }

        // Partner logos (any number), same shape as slides.
        $rules += [
            'partners'                => 'nullable|array',
            'partners.*.image'        => 'nullable|file|max:2048|mimes:jpg,jpeg,png,gif,webp,svg',
            'new_partners'            => 'nullable|array',
            'new_partners.*.image'    => 'required|file|max:2048|mimes:jpg,jpeg,png,gif,webp,svg',
        ];
        foreach (['partners', 'new_partners'] as $group) {
            $rules += [
                "{$group}.*.name"       => 'nullable|string|max:255',
                "{$group}.*.link"       => 'nullable|string|max:255',
                "{$group}.*.sort_order" => 'nullable|integer|min:0',
                "{$group}.*.status"     => 'nullable|boolean',
            ];
        }

        $request->validate($rules, [
            'new_slides.*.image.required'   => 'Every new slide needs a desktop image.',
            'new_partners.*.image.required' => 'Every new partner needs a logo image.',
        ]);

        $values = [];
        $files  = [];
        foreach ($fields as $key => $meta) {
            if ($meta['type'] === 'file') {
                if ($request->hasFile($key)) {
                    $files[$key] = $request->file($key);
                }
            } else {
                $values[$key] = $request->input($key);
            }
        }

        $removeFiles = (array) $request->input('remove_file', []);

        $this->homeSettingService->save($values, $files, $removeFiles);

        $this->homeSettingService->saveSlides(
            (array) $request->input('slides', []),
            (array) $request->input('new_slides', []),
            [
                'slides'     => (array) $request->file('slides', []),
                'new_slides' => (array) $request->file('new_slides', []),
            ]
        );

        $this->homeSettingService->savePartners(
            (array) $request->input('partners', []),
            (array) $request->input('new_partners', []),
            [
                'partners'     => (array) $request->file('partners', []),
                'new_partners' => (array) $request->file('new_partners', []),
            ]
        );

        return redirect()->route('admin.home_settings')->with('success', 'Home settings updated successfully.');
    }
}
