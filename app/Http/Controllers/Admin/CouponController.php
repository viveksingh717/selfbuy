<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponModel;
use App\Services\CouponService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->couponService = new CouponService();
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('admin.coupon.coupon');
        }

        // handle DataTable AJAX request
        $coupons = CouponModel::select([
            'id',
            'coupon_name',
            'coupon_code',
            'discount_type',
            'discount_value',
            'expiry_date',
            'status',
            'created_at',
        ]);

        return DataTables::of($coupons)

    ->editColumn('discount_type', function ($row) {
        $badges = [
            'percentage'    => '<span class="badge badge-info">Percentage</span>',
            'fixed_cart'    => '<span class="badge badge-primary">Fixed Amount</span>',
            'free_shipping' => '<span class="badge badge-success">Free Shipping</span>',
            'buy_x_get_y'   => '<span class="badge badge-warning">Buy X Get Y</span>',
        ];

        return $badges[$row->discount_type] ?? '<span class="badge badge-secondary">N/A</span>';
    })

    // ->editColumn('status', function ($row) {
    //     $checked = $row->status == 1 ? 'checked' : '';

    //     return '<label class="custom-switch">
    //                 <input type="checkbox"
    //                     class="custom-switch-input toggle-status"
    //                     data-id="'.$row->id.'"
    //                     '.$checked.'>
    //                 <span class="custom-switch-indicator"></span>
    //             </label>';
    // })

    ->addColumn('status', function ($row) {

        $checked = $row->status ? 'checked' : '';

        return '
            <label class="custom-switch">
                <input
                    type="checkbox"
                    class="custom-switch-input toggle-status"
                    data-id="'.$row->id.'"
                    data-url="/admin/coupon_status"
                    data-table="#couponTable"
                    '.$checked.'
                >
                <span class="custom-switch-indicator"></span>
            </label>
        ';
    })

    ->editColumn('expiry_date', function ($row) {
        if (!$row->expiry_date) return '<span class="text-muted">No expiry</span>';

        $expired = Carbon::parse($row->expiry_date)->isPast();
        $label = Carbon::parse($row->expiry_date)->format('d M Y');

        return $expired
            ? '<span class="badge badge-danger">'.$label.' (Expired)</span>'
            : '<span class="text-dark">'.$label.'</span>';
    })

    ->editColumn('created_at', function ($row) {
        return Carbon::parse($row->created_at)->format('d M Y');
    })

    ->addColumn('action', function ($row) {
        return '
            <a href="'.route('admin.edit_coupon', $row->id).'"
                class="btn btn-sm btn-primary">
                <i class="fa fa-edit"></i>
            </a>

            <button type="button"
                class="btn btn-sm btn-danger delete-coupon"
                data-id="'.$row->id.'">
                <i class="fa fa-trash"></i>
            </button>
        ';
    })

    ->rawColumns(['discount_type', 'status', 'expiry_date', 'action'])
    ->make(true);
    }

    public function create_coupon()
    {
        return view('admin.coupon.create_coupon');
    }

    public function process_coupon(Request $request, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'coupon_name'         => 'required|string|max:100',
                'coupon_code'         => 'required|string|max:50|unique:coupon_models,coupon_code',
                'discount_type'       => 'required|in:percentage,fixed_cart,free_shipping,buy_x_get_y',
                'discount_value'      => 'required_unless:discount_type,free_shipping|numeric|min:0',
                'min_order_amount'    => 'nullable|numeric|min:0',
                'max_discount_amount' => 'nullable|numeric|min:0',
                'start_date'          => 'nullable|date',
                'expiry_date'         => 'nullable|date|after_or_equal:start_date',
                'usage_limit'         => 'nullable|integer|min:1',
                'per_user_limit'      => 'nullable|integer|min:1',
                'applicable_to'       => 'nullable|in:all,category,product',
                'description'         => 'nullable|string|max:500',
                'status'              => 'required|in:0,1',
            ]);

            if ($validators->passes()) {
                $result = $this->couponService->save_coupon($request->all());

                if ($result['status']) {
                    $request->session()->flash('success', $result['message']);
                    return $rs->setCreatedResponse($result['message'], $result['data']);
                } else {
                    $request->session()->flash('error', $result['message']);
                    return $rs->setErrorResponse($result['message']);
                }
            } else {
                return $rs->setValidationResponse($validators->errors());
            }
        } else {
            $request->session()->flash('error', 'Please login to continue.');
            return redirect()
                ->route('admin.login');
        }
    }

    public function edit_coupon($id)
    {
        $coupon = $this->couponService->get_coupon_by_id($id);

        if (!$coupon) {
            return redirect()
                ->route('admin.coupon')
                ->with('error', 'Coupon not found.');
        }

        return view('admin.coupon.edit_coupon', compact('coupon'));
    }

    public function update_coupon(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $validators = Validator::make($request->all(), [
                'coupon_name'         => 'required|string|max:100',
                'coupon_code'         => 'required|string|max:50|unique:coupon_models,coupon_code,'.$id,
                'discount_type'       => 'required|in:percentage,fixed_cart,free_shipping,buy_x_get_y',
                'discount_value'      => 'required_unless:discount_type,free_shipping|numeric|min:0',
                'min_order_amount'    => 'nullable|numeric|min:0',
                'max_discount_amount' => 'nullable|numeric|min:0',
                'start_date'          => 'nullable|date',
                'expiry_date'         => 'nullable|date|after_or_equal:start_date',
                'usage_limit'         => 'nullable|integer|min:1',
                'per_user_limit'      => 'nullable|integer|min:1',
                'applicable_to'       => 'nullable|in:all,category,product',
                'description'         => 'nullable|string|max:500',
                'status'              => 'required|in:0,1',
            ]);

            if ($validators->passes()) {
                $result = $this->couponService->update_coupon($id, $request->all());

                if ($result['status']) {
                    $request->session()->flash('success', $result['message']);
                    return $rs->setSuccessResponse($result['message'], $result['data']);
                } else {
                    $request->session()->flash('error', $result['message']);
                    return $rs->setErrorResponse($result['message']);
                }
            } else {
                return $rs->setValidationResponse($validators->errors());
            }
        } else {
            $request->session()->flash('error', 'Please login to continue.');
            return redirect()
                ->route('admin.login');
        }
    }

    public function delete_coupon(Request $request, $id, ResponseService $rs)
    {
        if (Auth::guard('admin')->check()) {
            $result = $this->couponService->delete_coupon($id);

            if ($result['status']) {
                return $rs->setSuccessResponse($result['message'], ['id' => $id]);
            } else {
                return $rs->setErrorResponse($result['message']);
            }
        } else {
            $request->session()->flash('alert', 'Unauthorized access');
            return $rs->setUnauthorizedResponse('Unauthorized access');
        }
    }

    public function toggle_status(Request $request, $id, ResponseService $rs)
    {
        $status = $request->input('status');
        $result = $this->couponService->toggle_status($status, $id);

        if ($result['status']) {
            return $rs->setSuccessResponse($result['message'], ['status' => $status]);
        } else {
            return $rs->setErrorResponse($result['message']);
        }
    }

}
