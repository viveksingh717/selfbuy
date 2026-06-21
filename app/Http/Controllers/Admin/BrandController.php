<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\BrandService;
use App\Services\FileUploadService;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    function __construct(ResponseService $responseService)
    {
        $this->brandService = new BrandService();
        $this->fileUploadService = new FileUploadService();
    }

    public function index(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            if ($request->ajax()) {

                $data = Brand::latest();
                Log::info('Brand Data Retrieved: ', ['count' => $data->count()]);

                if (!$data || $data->count() == 0) {
                    return redirect()
                    ->route('admin.brand')
                    ->with('error', 'No data available');
                }

                return DataTables::of($data)

                    ->addColumn('brand_image', function ($row) {

                        $img = $row->brand_logo;

                        // invalid / empty / url guard
                        if (!$img || filter_var($img, FILTER_VALIDATE_URL)) {
                            return '
                                <img src="'.asset('photo.png').'"
                                    width="40"
                                    height="40"
                                    style="object-fit:cover;border-radius:6px;">
                            ';
                        }


                        $path = asset('storage/brand/thumb/' . $img);

                        return '
                            <img src="'.$path.'"'.'
                                width="40"
                                height="40"
                                style="object-fit:cover;border-radius:6px;">
                        ';
                    })
                    ->addColumn('status', function ($row) {
                        return $row->status ? 'Active' : 'Inactive';
                    })

                    ->editColumn('created_at', function ($row) {
                        return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <a href="'.route('admin.edit_brand', $row->id).'" class="btn btn-sm btn-primary">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm btn-danger delete-brand"
                                data-id="'.$row->id.'">
                                Delete
                            </button>
                        ';
                    })
                    ->rawColumns(['brand_image', 'action'])
                    ->make(true);
            }

            return view('admin.brands.brand');

        } else {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function create_brand()
    {
        return view('admin.brands.add_brand');
    }

    public function process_brand(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'brand_name'=>'required|string',
                'brand_slug' => 'required|unique:brands,brand_slug',
                'brand_image' => 'nullable|string',
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'brand_name' => $request->brand_name,
                    'brand_slug' => $request->brand_slug,
                    'brand_image' => $request->brand_image ?? null,
                    'status' => $request->status,
                ];
                $res = $this->brandService->save_brand($data);

                Log::info('Brand Save Result: ', $res);

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

    public function brand_image_upload(Request $request, ResponseService $rs)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'brand_image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048'
            ]);

            if ($validator->fails()) {
                return $rs->setValidationResponse($validator->errors()->first());
            }

            $filename = $this->fileUploadService->uploadImage(
                $request->file('brand_image'),
                'brand'
            );

            Log::info('Brand Image Uploaded', [
                'file_name' => $filename
            ]);

            return response()->json([
                'success' => true,
                'file_name' => $filename,
                'image_url' => $this->fileUploadService->getUrl('brand', $filename),
                'thumb_url' => $this->fileUploadService->getThumbUrl('brand', $filename),
                'message' => 'Image uploaded successfully'
            ]);

        } catch (Exception $e) {

            Log::error('Brand Image Upload Error: ' . $e->getMessage());

            return $rs->setErrorResponse('Failed to upload image');
        }
    }

    public function edit_brand(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $brand = Brand::find($id);
            Log::info('Brand Found: ', ['brand' => $brand]);

            if (!$brand) {
                return redirect()
                    ->route('admin.brand')
                    ->with('error', 'Something went wrong, category not found. ID: ' . $id);
            }

            return view('admin.brands.edit_brand', compact('brand'));
        } else {
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function update_brand(Request $request, $id, ResponseService $rs)
    {
        $brand = Brand::find($id);
        Log::info('Brand Found: ', ['brand' => $brand]);

        if ($request->brand_image && $brand->brand_logo) {

            $this->fileUploadService->deleteImage(
                $brand->brand_logo,
                'brand'
            );
        }

        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'brand_name'=>'required|string',
                'brand_slug' => 'required|unique:brands,brand_slug,' . $id . ',id',
                'brand_image' => 'nullable|string',
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'brand_name' => $request->brand_name,
                    'brand_slug' => $request->brand_slug,
                    'brand_image' => $request->brand_image ?? $brand->brand_logo,
                    'status' => $request->status,
                ];
                $res = $this->brandService->update_brand($id, $data);

                Log::info('Brand Updated Result: ', $res);

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

    public function delete_brand(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $brand = Brand::find($id);

            if (!$brand) {
                return redirect()->route('admin.brand')
                    ->with('error', 'Brand not found.');
            }

            if ($brand->brand_logo) {
                $this->fileUploadService->deleteImage(
                    $brand->brand_logo,
                    'brand'
                );
            }

            try {
                $brand->delete();
                $request->session()->flash('success', 'Brand deleted successfully');
                return $rs->setSuccessResponse('Brand deleted successfully', ['id' => $id]);
            } catch (Exception $e) {
                Log::error('Brand Deletion Error: ' . $e->getMessage());
                $request->session()->flash('error', 'Failed to delete brand');
                return $rs->setErrorResponse('Failed to delete brand');
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }
}
