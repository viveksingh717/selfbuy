<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeModel;
use App\Services\ResponseService;
use App\Services\SizeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SizeController extends Controller
{
    public function __construct()
    {
        $this->sizeService = new SizeService();
    }

    public function index()
    {
        if (Auth::guard('admin')->check()) {
            if (request()->ajax()) {
                $sizes = SizeModel::latest();
                Log::info('Size Data Retrieved: ', ['count' => $sizes->count()]);

                if (!$sizes || $sizes->count() == 0) {
                    return redirect()
                    ->route('admin.size')
                    ->with('error', 'No data available');
                }

                return DataTables::of($sizes)

                     ->addColumn('status', function ($row) {
                            return $row->status ? 'Active' : 'Inactive';
                        })

                        ->editColumn('created_at', function ($row) {
                            return Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
                        })
                        ->addColumn('action', function ($row) {
                            return '
                                <a href="'.route('admin.edit_size', $row->id).'" class="btn btn-sm btn-primary">Edit</a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger delete-size"
                                    data-id="'.$row->id.'">
                                    Delete
                                </button>
                            ';
                        })
                    ->rawColumns(['size_code', 'action'])
                    ->make(true);
            }

            return view('admin.size.size');
        } else {
            return redirect()
                ->route('admin.login')
                ->with('alert', 'Unauthorized access');
        }
    }

    public function create_size()
    {
        return view('admin.size.create_size');
    }

    public function process_size(Request $request, ResponseService $rs)
    {
       if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'size_name'=>'required|string',
                'size_code' => 'required|string|unique:size_models,size_code',
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'size_name' => $request->size_name,
                    'size_code' => $request->size_code,
                    'status' => $request->status,
                ];
                $res = $this->sizeService->save_size($data);

                Log::info('Size Save Result: ', $res);

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

    public function edit_size($id)
    {
        $sizes = SizeModel::findOrFail($id);
        return view('admin.size.edit_size', compact('sizes'));
    }

    public function update_size(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'size_name'=>'required|string',
                'size_code' => 'required|string|unique:size_models,size_code,'.$id,
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'size_name' => $request->size_name,
                    'size_code' => $request->size_code,
                    'status' => $request->status,
                ];
                $res = $this->sizeService->update_size($id, $data);

                Log::info('Size Update Result: ', $res);

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

    public function delete_size(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $size = SizeModel::find($id);

            if (!$size) {
                $request->session()->flash('error', 'Size not found');
                return $rs->setErrorResponse('Size not found');
            }

            // if (!$size) {
            //      return redirect()->route('admin.size')
            //         ->with('error', 'Size not found.');
            // }

            try {
                $size->delete();
                $request->session()->flash('success', 'Size deleted successfully');
                return $rs->setSuccessResponse('Size deleted successfully', ['id' => $id]);
            } catch (Exception $e) {
                Log::error('Size Deletion Error: ' . $e->getMessage());
                $request->session()->flash('error', 'Failed to delete size');
                return $rs->setErrorResponse('Failed to delete size');
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

}
