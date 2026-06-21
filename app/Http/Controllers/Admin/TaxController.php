<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxModel;
use App\Services\ResponseService;
use App\Services\TaxService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->taxService = new TaxService();
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('admin.tax.tax');
        }

        $taxes = TaxModel::select([
            'id', 'tax_name', 'tax_alias', 'tax_type',
            'tax_rate', 'applicable_to', 'status', 'created_at',
        ]);

        return DataTables::of($taxes)

            ->addColumn('tax_type', function ($row) {
                $badges = [
                    'percentage' => '<span class="badge badge-info">Percentage</span>',
                    'fixed'      => '<span class="badge badge-primary">Fixed</span>',
                    'compound'   => '<span class="badge badge-warning">Compound</span>',
                    'inclusive'  => '<span class="badge badge-success">Inclusive</span>',
                ];
                return $badges[$row->tax_type]
                    ?? '<span class="badge badge-secondary">N/A</span>';
            })

            ->addColumn('tax_rate', function ($row) {
                return $row->tax_type === 'fixed'
                    ? '₹' . number_format($row->tax_rate, 2)
                    : $row->tax_rate . '%';
            })

            ->addColumn('status', function ($row) {

                $checked = $row->status ? 'checked' : '';

                return '
                    <label class="custom-switch">
                        <input
                            type="checkbox"
                            class="custom-switch-input toggle-status"
                            data-id="'.$row->id.'"
                            data-url="/admin/tax_status"
                            data-table="#taxTable"
                            '.$checked.'
                        >
                        <span class="custom-switch-indicator"></span>
                    </label>
                ';
            })

            // ->addColumn('status', function ($row) {
            //     $checked = $row->status == 1 ? 'checked' : '';
            //     return '<label class="custom-switch">
            //                 <input type="checkbox" class="custom-switch-input toggle-status"
            //                     data-id="' . $row->id . '" ' . $checked . '>
            //                 <span class="custom-switch-indicator"></span>
            //             </label>';
            // })

            ->addColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d M Y');
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('admin.edit_tax', $row->id) . '"
                        class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger delete-tax"
                        data-id="' . $row->id . '">
                        <i class="fa fa-trash"></i>
                    </button>';
            })

            ->rawColumns(['tax_type', 'status', 'action'])
            ->make(true);
    }

    public function create_tax()
    {
        return view('admin.tax.create_tax');
    }

    public function process_tax(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'tax_name'      => 'required|string|max:100',
                'tax_alias'     => 'nullable|string|max:20',
                'tax_type'      => 'required|in:percentage,fixed,compound,inclusive',
                'tax_rate'      => 'required|numeric|min:0',
                'applicable_to' => 'nullable|in:all,category,product',
                'tax_region'    => 'nullable|in:all,domestic,international',
                'priority'      => 'nullable|integer|min:1',
                'description'   => 'nullable|string|max:500',
                'status'        => 'required|in:0,1',
            ]);

            if ($validators->fails()) {
                $request->session()->flash('error', 'Please fix the errors below.');
                return $rs->setValidationResponse($validators->errors());
            }

            $result = $this->taxService->save_tax($request->all());

            if ($result['status']) {
                $request->session()->flash('success', $result['message']);
                return $rs->setSuccessResponse($result['message'], $result['data']);
            } else {
                $request->session()->flash('error', $result['message']);
                return $rs->setErrorResponse($result['message']);
            }
        } else {
            return $rs->setErrorResponse('Please login to continue.');
        }
    }

    public function edit_tax($id)
    {
        $tax = $this->taxService->get_tax_by_id($id);

        if (!$tax) {
            return redirect()->route('admin.tax')
                ->with('error', 'Tax not found.');
        }

        return view('admin.tax.edit_tax', compact('tax'));
    }

    public function update_tax(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'tax_name'      => 'required|string|max:100',
                'tax_alias'     => 'nullable|string|max:20',
                'tax_type'      => 'required|in:percentage,fixed,compound,inclusive',
                'tax_rate'      => 'required|numeric|min:0',
                'applicable_to' => 'nullable|in:all,category,product',
                'tax_region'    => 'nullable|in:all,domestic,international',
                'priority'      => 'nullable|integer|min:1',
                'description'   => 'nullable|string|max:500',
                'status'        => 'required|in:0,1',
            ]);

            if ($validators->fails()) {
                return $rs->setValidationResponse($validators->errors());
            }

            $result = $this->taxService->update_tax($id, $request->all());

            if ($result['status']) {
                $request->session()->flash('success', $result['message']);
                return $rs->setSuccessResponse($result['message'], $result['data']);
            } else {
                $request->session()->flash('error', $result['message']);
                return $rs->setErrorResponse($result['message']);
            }
        } else {
            return $rs->setErrorResponse('Please login to continue.');
        }
    }

    public function delete_tax(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $result = $this->taxService->delete_tax($id);

        if ($result['status']) {
            $request->session()->flash('success', $result['message']);
            return $rs->setSuccessResponse($result['message'], ['id' => $id]);
        } else {
            $request->session()->flash('error', $result['message']);
            return $rs->setErrorResponse($result['message']);
        }
    }

    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        if (!Auth::guard('admin')->check()) {
            return $rs->setErrorResponse('Please login to continue.');
        }

        $result = $this->taxService->toggle_status($request->status,$id);

        if ($result['status']) {
            return $rs->setSuccessResponse($result['message'], ['status' => $request->status]);
        }

        return $rs->setErrorResponse($result['message']);
    }


}
