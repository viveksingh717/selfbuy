<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ColorModel;
use App\Services\ColorService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ColorController extends Controller
{
    function __construct(ResponseService $responseService)
    {
        $this->colorService = new ColorService();
    }

    public function index()
    {
        if (Auth::guard('admin')->check()) {
            if (request()->ajax()) {
                $colors = ColorModel::latest();
                Log::info('Color Data Retrieved: ', ['count' => $colors->count()]);

                if (!$colors || $colors->count() == 0) {
                    return redirect()
                    ->route('admin.color')
                    ->with('error', 'No data available');
                }

                return DataTables::of($colors)

                    ->addColumn('color_code', function ($row) {
                        return '
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="
                                    width:20px;
                                    height:20px;
                                    background-color: '.$row->color_code.';
                                    border-radius:4px;
                                    border:1px solid #ddd;
                                "></div>
                                <span>'.$row->color_code.'</span>
                            </div>
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
                                <a href="'.route('admin.edit_color', $row->id).'" class="btn btn-sm btn-primary">Edit</a>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger delete-color"
                                    data-id="'.$row->id.'">
                                    Delete
                                </button>
                            ';
                        })
                    ->rawColumns(['color_code', 'status', 'action'])
                    ->make(true);
            }

            return view('admin.colour.color');
        } else {
            return redirect()
                ->route('admin.login')
                ->with('alert', 'Unauthorized access');
        }
    }

    public function create_color()
    {
        return view('admin.colour.create_color');
    }

    public function process_color(Request $request, ResponseService $rs)
    {
       if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'color_name'=>'required|string',
                'color_code' => 'required|string|unique:color_models,color_code',
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'color_name' => $request->color_name,
                    'color_code' => $request->color_code,
                    'status' => $request->status,
                ];
                $res = $this->colorService->save_color($data);

                Log::info('Color Save Result: ', $res);

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

    public function edit_color($id)
    {
        $colors = ColorModel::findOrFail($id);
        return view('admin.colour.edit_color', compact('colors'));
    }

    public function update_color(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'color_name'=>'required|string',
                'color_code' => 'required|string|unique:color_models,color_code,'.$id,
                'status'=>'required|boolean',
            ]);

            if ($validators->passes()) {
                $data = [
                    'color_name' => $request->color_name,
                    'color_code' => $request->color_code,
                    'status' => $request->status,
                ];
                $res = $this->colorService->update_color($id, $data);

                Log::info('Color Update Result: ', $res);

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

    public function delete_color(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $color = ColorModel::find($id);

            if (!$color) {
                $request->session()->flash('error', 'Color not found');
                return $rs->setErrorResponse('Color not found');
            }

            // if (!$color) {
            //      return redirect()->route('admin.color')
            //         ->with('error', 'Color not found.');
            // }

            try {
                $color->delete();
                $request->session()->flash('success', 'Color deleted successfully');
                return $rs->setSuccessResponse('Color deleted successfully', ['id' => $id]);
            } catch (Exception $e) {
                Log::error('Color Deletion Error: ' . $e->getMessage());
                $request->session()->flash('error', 'Failed to delete color');
                return $rs->setErrorResponse('Failed to delete color');
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }


}
