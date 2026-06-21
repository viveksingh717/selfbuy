<?php

namespace App\Services;

use App\Models\CouponModel;
use Exception;
use Illuminate\Support\Facades\Log;

class CouponService
{
    public function get_all_coupons()
    {
        return CouponModel::latest()->get();
    }

    public function get_coupon_by_id($id)
    {
        return CouponModel::find($id);
    }

    public function save_coupon($data)
    {
        try {
            $coupon = CouponModel::create([
                'coupon_name'         => $data['coupon_name'],
                'coupon_code'         => strtoupper($data['coupon_code']),
                'description'         => $data['description']         ?? null,
                'discount_type'       => $data['discount_type'],
                'discount_value'      => $data['discount_value']      ?? 0,
                'min_order_amount'    => $data['min_order_amount']    ?? 0,
                'max_discount_amount' => $data['max_discount_amount'] ?? null,
                'start_date'          => $data['start_date']          ?? null,
                'expiry_date'         => $data['expiry_date']         ?? null,
                'usage_limit'         => $data['usage_limit']         ?? null,
                'per_user_limit'      => $data['per_user_limit']      ?? null,
                'applicable_to'       => $data['applicable_to']       ?? 'all',
                'status'              => $data['status'],
            ]);

            return ['status' => true, 'message' => 'Coupon created successfully.', 'data' => $coupon];

        } catch (Exception $e) {
            Log::error('Coupon Save Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to save coupon.', 'error' => $e->getMessage()];
        }
    }

    public function update_coupon($id, array $data): array
    {
        try {
            $coupon = CouponModel::find($id);

            if (!$coupon) {
                return ['status' => false, 'message' => 'Coupon not found.', 'data' => []];
            }

            $coupon->coupon_name         = $data['coupon_name'];
            $coupon->coupon_code         = strtoupper($data['coupon_code']);
            $coupon->description         = $data['description']         ?? null;
            $coupon->discount_type       = $data['discount_type'];
            $coupon->discount_value      = $data['discount_value']      ?? 0;
            $coupon->min_order_amount    = $data['min_order_amount']    ?? 0;
            $coupon->max_discount_amount = $data['max_discount_amount'] ?? null;
            $coupon->start_date          = $data['start_date']          ?? null;
            $coupon->expiry_date         = $data['expiry_date']         ?? null;
            $coupon->usage_limit         = $data['usage_limit']         ?? null;
            $coupon->per_user_limit      = $data['per_user_limit']      ?? null;
            $coupon->applicable_to       = $data['applicable_to']       ?? 'all';
            $coupon->status              = $data['status'];
            $coupon->save();

            return ['status' => true, 'message' => 'Coupon updated successfully.', 'data' => $coupon];

        } catch (Exception $e) {
            Log::error('Coupon Update Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to update coupon.', 'error' => $e->getMessage()];
        }
    }

    public function delete_coupon($id): array
    {
        try {
            $coupon = CouponModel::find($id);

            if (!$coupon) {
                return ['status' => false, 'message' => 'Coupon not found.'];
            }

            $coupon->delete();

            return ['status' => true, 'message' => 'Coupon deleted successfully.'];

        } catch (Exception $e) {
            Log::error('Coupon Delete Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to delete coupon.', 'error' => $e->getMessage()];
        }
    }

    public function toggle_status($status, $id)
    {
        $coupon = CouponModel::find($id);

        if (!$coupon) {
            return ['status' => false, 'message' => 'Coupon not found.'];
        }

        $coupon->status = $status;

        if ($coupon->save()) {
            return ['status' => true, 'message' => 'Coupon updated successfully.'];
        } else {
            return ['status' => false, 'message' => 'Failed to update coupon.'];
        }
    }
}
