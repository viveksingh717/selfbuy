<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private FileUploadService $files)
    {
    }

    public function edit()
    {
        return view('admin.pages.profile', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    /** Name / email / phone / address + optional avatar. */
    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:150'],
            'email'         => ['required', 'email:rfc,filter', 'max:190', Rule::unique('users', 'email')->ignore($admin->id)],
            'phone_number'  => ['nullable', 'string', 'max:30'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'postal_code'   => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
            'avatar'        => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        // Track what actually changed so the owner can be notified.
        $originalEmail = $admin->email;
        $changes = [];
        foreach (['name' => 'Name', 'email' => 'Email', 'phone_number' => 'Phone'] as $field => $label) {
            $new = $data[$field] ?? null;
            if ((string) $admin->{$field} !== (string) $new) {
                $changes[] = ['label' => $label, 'old' => (string) $admin->{$field}, 'new' => (string) $new];
            }
        }

        if ($request->hasFile('avatar')) {
            $this->files->deleteImage($admin->profile_photo, 'users');
            $admin->profile_photo = $this->files->uploadImage($request->file('avatar'), 'users');
        }

        $admin->fill($data)->save();

        if ($changes) {
            $admin->sendAccountDetailsChangedNotification($originalEmail, $changes);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'Your current password is incorrect.'])
                ->with('password_tab', true);
        }

        $admin->password = $request->password; // 'hashed' cast handles the hashing
        $admin->save();

        $admin->sendPasswordChangedNotification();

        return back()->with('success', 'Password changed successfully.')->with('password_tab', true);
    }

    public function deleteAvatar()
    {
        $admin = Auth::guard('admin')->user();

        if ($admin->profile_photo) {
            $this->files->deleteImage($admin->profile_photo, 'users');
            $admin->profile_photo = null;
            $admin->save();
        }

        return back()->with('success', 'Profile photo removed.');
    }
}
