<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\FileUploadService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    private FileUploadService $files;

    public function __construct()
    {
        $this->files = new FileUploadService();
    }

    public function index(Request $request)
    {
        $q     = trim((string) $request->query('q', ''));
        $album = $request->query('album', '');

        $galleries = Gallery::query()
            ->when($album !== '', fn ($w) => $w->where('album', $album))
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x
                ->where('title', 'like', "%{$q}%")
                ->orWhere('caption', 'like', "%{$q}%")
                ->orWhere('album', 'like', "%{$q}%")))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $albums = Gallery::whereNotNull('album')->distinct()->orderBy('album')->pluck('album');

        return view('admin.gallery.gallery', compact('galleries', 'albums', 'q', 'album'));
    }

    public function create_gallery()
    {
        return view('admin.gallery.create_gallery');
    }

    public function process_gallery(Request $request, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $validator = Validator::make($request->all(), $this->rules(true));

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $data = $this->payload($request);
        $data['image'] = $this->files->uploadImage($request->file('image'), 'gallery');

        $item = Gallery::create($data);

        $request->session()->flash('success', 'Image added to the gallery.');
        return $rs->setCreatedResponse('Image added to the gallery.', $item);
    }

    public function edit_gallery($id)
    {
        $item = Gallery::find($id);

        if (!$item) {
            return redirect()->route('admin.gallery')->with('error', 'Image not found.');
        }

        return view('admin.gallery.edit_gallery', compact('item'));
    }

    public function update_gallery(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $item = Gallery::find($id);

        if (!$item) {
            return $rs->setNotFoundResponse('Image not found.');
        }

        $validator = Validator::make($request->all(), $this->rules(false));

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $data = $this->payload($request);

        if ($request->hasFile('image')) {
            // only delete a previously *uploaded* file, not a seeded asset path
            if ($item->image && !Str::contains($item->image, '/')) {
                $this->files->deleteImage($item->image, 'gallery');
            }
            $data['image'] = $this->files->uploadImage($request->file('image'), 'gallery');
        }

        $item->update($data);

        $request->session()->flash('success', 'Gallery image updated.');
        return $rs->setSuccessResponse('Gallery image updated.', $item);
    }

    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $item = Gallery::find($id);

        if (!$item) {
            return $rs->setNotFoundResponse('Image not found.');
        }

        $item->update(['status' => (int) $request->status]);

        return $rs->setSuccessResponse('Status updated.', ['status' => $item->status]);
    }

    public function delete_gallery(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $item = Gallery::find($id);

        if (!$item) {
            return $rs->setNotFoundResponse('Image not found.');
        }

        $item->delete(); // soft delete - the file is kept

        return $rs->setSuccessResponse('Gallery image deleted.', ['id' => $id]);
    }

    private function rules(bool $creating): array
    {
        return [
            'title'      => 'nullable|string|max:190',
            'album'      => 'nullable|string|max:100',
            'caption'    => 'nullable|string|max:1000',
            'link_url'   => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'status'     => 'required|in:0,1',
            'image'      => ($creating ? 'required' : 'nullable') . '|image|mimes:jpeg,jpg,png,webp,gif|max:4096',
        ];
    }

    private function payload(Request $request): array
    {
        return [
            'title'      => $request->title,
            'album'      => $request->album,
            'caption'    => $request->caption,
            'link_url'   => $request->link_url,
            'sort_order' => (int) ($request->sort_order ?? 0),
            'status'     => (int) $request->status,
        ];
    }
}
