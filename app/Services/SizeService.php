<?php
namespace App\Services;

use App\Models\SizeModel;
use Exception;
use Illuminate\Support\Facades\Log;

class SizeService {
    public function save_size($data)
    {
        try {

            $size = new SizeModel();
            $size->size_name = $data['size_name'];
            $size->size_code = $data['size_code'];
            $size->status = $data['status'];

            $size->save();

            return [
                'status' => true,
                'message' => 'Size saved successfully',
                'data' => $size
            ];

        } catch (Exception $e) {

            Log::error('Size Save Error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to save size',
                'error' => $e->getMessage()
            ];
        }
    }

    public function update_size($id, $data)
    {
        try {

            $size = SizeModel::find($id);

            if (!$size) {
                return [
                    'status' => false,
                    'message' => 'Size data not found.',
                    'data' => []
                ];
            }

            $size->size_name     = $data['size_name'];
            $size->size_code     = $data['size_code'];
            $size->status         = $data['status'];

            $size->save();

            return [
                'status' => true,
                'message' => 'Size updated successfully',
                'data' => $size
            ];

        } catch (Exception $e) {

            Log::error('Size update error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to update size',
                'error' => $e->getMessage()
            ];
        }
    }

}
