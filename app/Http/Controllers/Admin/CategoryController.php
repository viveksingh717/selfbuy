<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Services\FileUploadService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct() {
        $this->categoryService = new CategoryService;
        $this->fileUploadService = new FileUploadService;
    }

    public function index(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            if ($request->ajax()) {

                $data = Category::latest();
                Log::info('Category Data Retrieved: ', ['count' => $data->count()]);

                if (!$data || $data->count() == 0) {
                    return redirect()
                    ->route('admin.category')
                    ->with('error', 'No data available');
                }

                return DataTables::of($data)

                    ->addColumn('category_image', function ($row) {

                        $img = $row->category_image;

                        // invalid / empty / url guard
                        if (!$img || filter_var($img, FILTER_VALIDATE_URL)) {
                            return '
                                <img src="'.asset('photo.png').'"
                                    width="40"
                                    height="40"
                                    style="object-fit:cover;border-radius:6px;">
                            ';
                        }


                        $path = asset('storage/category/thumb/' . $img);

                        return '
                            <img src="'.$path.'"
                                width="40"
                                height="40"
                                style="object-fit:cover;border-radius:6px;">
                        ';
                    })
                    ->addColumn('status', function ($row) {
                        return $row->status ? 'Active' : 'Inactive';
                    })
                    ->addColumn('is_featured', function ($row) {
                        return $row->is_featured ? 'Yes' : 'No';
                    })
                    ->editColumn('created_at', function ($row) {
                        return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <a href="'.route('admin.edit_category', $row->id).'" class="btn btn-sm btn-primary">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger delete-category"
                                data-id="'.$row->id.'">
                                Delete
                            </button>
                        ';
                    })
                    ->rawColumns(['category_image', 'action'])
                    ->make(true);
            }

            return view('admin.category.category');

        } else {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function create_category() {

        return view('admin.category.add_category');
    }

    public function process_category(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            // $user = Auth::guard('admin')->user();
            $validators = Validator::make($request->all(), [
                'category_name'=>'required|string',
                'category_slug' => 'required|unique:categories,category_slug',
                'cat_description'=>'nullable|string|max:500',
                'category_image' => 'nullable|string',
                'is_featured'=>'required|boolean',
                'status'=>'required|boolean',
                'meta_title'=>'nullable|string|max:60',
                'meta_description'=>'nullable|string|max:160'
            ]);

            if ($validators->passes()) {
                $data = [
                    'category_name' => $request->category_name,
                    'category_slug' => $request->category_slug,
                    'description' => $request->cat_description ?? '',
                    'category_image' => $request->category_image ?? null,
                    'is_featured' => $request->is_featured,
                    'status' => $request->status,
                    'meta_title' => $request->meta_title ?? '',
                    'meta_description' => $request->meta_description ?? ''
                ];
                $res = $this->categoryService->save_category($data);

                Log::info('Category Save Result: ', $res);

                if (!$res['status']) {
                    $request->session()->flash('error', $res['message']);
                    return $rs->setErrorResponse($res['message']);
                }

                $request->session()->flash('success', $res['message']);
                return $rs->setCreatedResponse($res['message'],$res['data']);
            } else{
                return $rs->setValidationResponse($validators->errors());
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    /**
     * Handle category image upload with validation, thumbnail creation, and error handling.
     **/
    public function category_image_upload(Request $request, ResponseService $rs)
    {
        try {

            $validator = Validator::make($request->all(), [
                'category_image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return $rs->setValidationResponse($validator->errors()->first());
            }

            $filename = $this->fileUploadService->uploadImage(
                $request->file('category_image'),
                'category'
            );

            Log::info('Category Image Uploaded', [
                'file_name' => $filename
            ]);

            return response()->json([
                'success' => true,
                'file_name' => $filename,
                'image_url' => $this->fileUploadService->getUrl('category', $filename),
                'thumb_url' => $this->fileUploadService->getThumbUrl('category', $filename),
                'message' => 'Image uploaded successfully'
            ]);

        } catch (Exception $e) {

            Log::error('Category Image Upload Error: ' . $e->getMessage());

            return $rs->setErrorResponse('Failed to upload image');
        }
    }

    public function edit_category(Request $request, $id, ResponseService $rs) {
        if (Auth::guard('admin')->check()) {
            $category = Category::find($id);
            Log::info('Category Found: ', ['category' => $category]);

            if (!$category) {
                return redirect()
                    ->route('admin.category')
                    ->with('error', 'Something went wrong, category not found. ID: ' . $id);
            }

            return view('admin.category.edit_category', compact('category'));
        } else {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function update_category(Request $request, $id, ResponseService $rs)
    {
        $category = Category::find($id);
        Log::info('Category Found: ', ['category' => $category]);

        if ($request->category_image && $category->category_image) {

            $this->fileUploadService->deleteImage(
                $category->category_image,
                'category'
            );
        }

        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'category_name'=>'required|string',
                'category_slug' => 'required|unique:categories,category_slug,' . $id . ',id',
                'cat_description'=>'nullable|string|max:500',
                'category_image' => 'nullable|string',
                'is_featured'=>'required|boolean',
                'status'=>'required|boolean',
                'meta_title'=>'nullable|string|max:60',
                'meta_description'=>'nullable|string|max:160'
            ]);

            if ($validators->passes()) {
                $data = [
                    'category_name' => $request->category_name,
                    'category_slug' => $request->category_slug,
                    'description' => $request->cat_description ?? '',
                    'category_image' => $request->category_image ?? $category->category_image,
                    'is_featured' => $request->is_featured ,
                    'status' => $request->status,
                    'meta_title' => $request->meta_title ?? '',
                    'meta_description' => $request->meta_description ?? ''
                ];
                $res = $this->categoryService->update_category($id, $data);

                Log::info('Category Updated Result: ', $res);

                if (!$res['status']) {
                    $request->session()->flash('error', $res['message']);
                    return $rs->setErrorResponse($res['message']);
                }

                $request->session()->flash('success', $res['message']);
                return $rs->setSuccessResponse($res['message'],$res['data']);
            } else{
                return $rs->setValidationResponse($validators->errors());
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function delete_category(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $category = Category::find($id);

            if (!$category) {
                return redirect()->route('admin.category')
                    ->with('error', 'Category not found.');
            }

            if ($category->category_image) {
                $this->fileUploadService->deleteImage(
                    $category->category_image,
                    'category'
                );
            }

            try {
                $category->delete();
                $request->session()->flash('success', 'Category deleted successfully');
                return $rs->setSuccessResponse('Category deleted successfully', ['id'=>$id]);
            } catch (Exception $e) {
                Log::error('Category Deletion Error: ' . $e->getMessage());
                $request->session()->flash('error', 'Failed to delete category');
                return $rs->setErrorResponse('Failed to delete category');
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }

    }
}
