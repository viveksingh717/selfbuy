<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
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

class SubCategoryController extends Controller
{
    function __construct()
    {
        $this->categoryService = new CategoryService;
        $this->fileUploadService = new FileUploadService;
    }

    public function index(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            if ($request->ajax()) {

                $data = SubCategory::with('category')->latest();
                Log::info('SubCategory Data Retrieved: ', ['count' => $data->count()]);

                if (!$data || $data->count() == 0) {
                    return redirect()
                    ->route('admin.sub_category')
                    ->with('error', 'No subcategories found.');
                }

                return DataTables::of($data)

                    ->addColumn('category', function ($row) {
                        return $row->category->category_name ?? 'N/A';
                    })

                    ->addColumn('status', function ($row) {
                        return $row->status ? 'Active' : 'Inactive';
                    })

                    ->editColumn('created_at', function ($row) {
                        return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <a href="' . route('admin.edit_subcategory', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger delete-subcategory"
                                data-id="'.$row->id.'">
                                Delete
                            </button>
                        ';
                    })
                    ->rawColumns(['category_image', 'action'])
                    ->make(true);
            }

            return view('admin.category.sub_category.subcategory');

        } else {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function create_subcategory()
    {
        $categories = $this->categoryService->getActiveCategories();
        return view('admin.category.sub_category.add_subcategory', compact('categories'));
    }

    public function process_subcategory(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'subcategory_name'=>'required|string',
                'subcategory_slug' => 'required|unique:sub_categories,subcategory_slug',
                'category_id' => 'required|exists:categories,id',
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'subcategory_name' => $request->subcategory_name,
                    'subcategory_slug' => $request->subcategory_slug,
                    'category_id' => $request->category_id,
                    'status' => $request->status,
                ];
                $res = $this->categoryService->save_subcategory($data);

                Log::info('SubCategory Save Result: ', $res);

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

    public function edit_subcategory($id)
    {
        $subcategory = SubCategory::find($id);
        $categories = $this->categoryService->getActiveCategories();
        return view('admin.category.sub_category.edit_subcategory', compact('subcategory', 'categories'));
    }

    public function update_subcategory(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'subcategory_name'=>'required|string',
                'subcategory_slug' => 'required|unique:sub_categories,subcategory_slug,'.$id,
                'category_id' => 'required|exists:categories,id',
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'subcategory_name' => $request->subcategory_name,
                    'subcategory_slug' => $request->subcategory_slug,
                    'category_id' => $request->category_id,
                    'status' => $request->status,
                ];
                $res = $this->categoryService->update_subcategory($id, $data);

                Log::info('SubCategory Update Result: ', $res);

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

    public function delete_subcategory(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $subcategory = SubCategory::find($id);

            if (!$subcategory) {
                return redirect()->route('admin.sub_category')
                    ->with('error', 'Subcategory not found.');
            }
            try {
                $subcategory->delete();
                $request->session()->flash('success', 'Subcategory deleted successfully');
                return $rs->setSuccessResponse('Subcategory deleted successfully', ['id' => $id]);
            } catch (Exception $e) {
                Log::error('SubCategory Deletion Error: ' . $e->getMessage());
                $request->session()->flash('error', 'Failed to delete subcategory');
                return $rs->setErrorResponse('Failed to delete subcategory');
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }


}
