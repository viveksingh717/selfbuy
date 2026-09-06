<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    public function __construct()
    {
        $this->settingService = new SystemSettingService();
    }

    public function settings()
    {
        return view('admin.pages.system_setting', [
            'schema'   => SystemSettingService::schema(),
            'settings' => $this->settingService->all(),
        ]);
    }

    public function update_settings(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to continue.');
        }

        $fields = SystemSettingService::fields();

        // Build validation rules straight from the schema.
        $rules = [];
        foreach ($fields as $key => $meta) {
            $rules[$key] = match ($meta['type']) {
                'file'    => 'nullable|file|max:4096|mimes:jpg,jpeg,png,gif,webp,svg,ico',
                'boolean' => 'nullable|boolean',
                'number'  => 'nullable|numeric',
                default   => 'nullable|string|max:5000',
            };
        }

        $validated = $request->validate($rules);

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

        $this->settingService->save($values, $files, $removeFiles);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }
}
