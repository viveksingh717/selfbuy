<?php
namespace App\Services;

use App\Models\ColorModel;
use Exception;
use Illuminate\Support\Facades\Log;

class ColorService {
    public function save_color($data)
    {
        try {

            $color = new ColorModel();
            $color->color_name = $data['color_name'];
            $color->color_code = $data['color_code'];
            $color->status = $data['status'];

            $color->save();

            return [
                'status' => true,
                'message' => 'Color saved successfully',
                'data' => $color
            ];

        } catch (Exception $e) {

            Log::error('Color Save Error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to save color',
                'error' => $e->getMessage()
            ];
        }
    }

    public function update_color($id, $data)
    {
        try {

            $color = ColorModel::find($id);

            if (!$color) {
                return [
                    'status' => false,
                    'message' => 'Color data not found.',
                    'data' => []
                ];
            }

            $color->color_name     = $data['color_name'];
            $color->color_code     = $data['color_code'];
            $color->status         = $data['status'];

            $color->save();

            return [
                'status' => true,
                'message' => 'Color updated successfully',
                'data' => $color
            ];

        } catch (Exception $e) {

            Log::error('Color update error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to update color',
                'error' => $e->getMessage()
            ];
        }
    }

}