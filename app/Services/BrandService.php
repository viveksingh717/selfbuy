<?php
namespace App\Services;

use App\Models\Brand;
use Exception;
use Illuminate\Support\Facades\Log;

class BrandService {
    public function save_brand($data)
    {
        try {

            $brand = new Brand();

            $brand->brand_name     = $data['brand_name'];
            $brand->brand_slug     = $data['brand_slug'];
            $brand->brand_logo     = $data['brand_image'] ?? null;
            $brand->status         = $data['status'];

            $brand->save();

            return [
                'status' => true,
                'message' => 'Brand saved successfully',
                'data' => $brand
            ];

        } catch (Exception $e) {

            Log::error('Brand Save Error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to save brand',
                'error' => $e->getMessage()
            ];
        }
    }

    public function update_brand($id, $data)
    {
        try {

            $brand = Brand::find($id);

            if (!$brand) {
                return [
                    'status' => false,
                    'message' => 'Brand data not found.',
                    'data' => []
                ];
            }

            $brand->brand_name     = $data['brand_name'];
            $brand->brand_slug     = $data['brand_slug'];
            $brand->brand_logo     = $data['brand_image'] ?? null;
            $brand->status         = $data['status'];

            $brand->save();

            return [
                'status' => true,
                'message' => 'Brand updated successfully',
                'data' => $brand
            ];

        } catch (Exception $e) {

            Log::error('Brand update error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to update brand',
                'error' => $e->getMessage()
            ];
        }
    }

    // SubCategory related services can be added here
    public function getActiveBrands()
    {
        return Brand::query()
            ->where('status', 1)
            ->orderBy('brand_name')
            ->get();
    }

}
