<?php

namespace App\Services;

use App\Models\TaxModel;
use Exception;
use Illuminate\Support\Facades\Log;

class TaxService
{
    public function get_all_taxes()
    {
        return TaxModel::latest()->get();
    }

    public function get_tax_by_id($id)
    {
        return TaxModel::find($id);
    }

    public function save_tax(array $data): array
    {
        try {
            $tax = TaxModel::create([
                'tax_name'      => $data['tax_name'],
                'tax_alias'     => isset($data['tax_alias'])
                                    ? strtoupper($data['tax_alias'])
                                    : null,
                'tax_type'      => $data['tax_type']      ?? 'percentage',
                'tax_rate'      => $data['tax_rate']      ?? 0,
                'applicable_to' => $data['applicable_to'] ?? 'all',
                'tax_region'    => $data['tax_region']    ?? 'all',
                'priority'      => $data['priority']      ?? 1,
                'description'   => $data['description']   ?? null,
                'status'        => $data['status'],
            ]);

            return [
                'status'  => true,
                'message' => 'Tax created successfully.',
                'data'    => $tax,
            ];

        } catch (Exception $e) {
            Log::error('Tax Save Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to save tax.',
                'error'   => $e->getMessage(),
            ];
        }
    }


    public function update_tax($id, array $data): array
    {
        try {
            $tax = TaxModel::find($id);

            if (!$tax) {
                return ['status' => false, 'message' => 'Tax not found.', 'data' => []];
            }

            $tax->tax_name      = $data['tax_name'];
            $tax->tax_alias     = isset($data['tax_alias'])
                                    ? strtoupper($data['tax_alias'])
                                    : null;
            $tax->tax_type      = $data['tax_type']      ?? 'percentage';
            $tax->tax_rate      = $data['tax_rate']      ?? 0;
            $tax->applicable_to = $data['applicable_to'] ?? 'all';
            $tax->tax_region    = $data['tax_region']    ?? 'all';
            $tax->priority      = $data['priority']      ?? 1;
            $tax->description   = $data['description']   ?? null;
            $tax->status        = $data['status'];
            $tax->save();

            return [
                'status'  => true,
                'message' => 'Tax updated successfully.',
                'data'    => $tax,
            ];

        } catch (Exception $e) {
            Log::error('Tax Update Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to update tax.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function delete_tax($id): array
    {
        try {
            $tax = TaxModel::find($id);

            if (!$tax) {
                return ['status' => false, 'message' => 'Tax not found.'];
            }

            $tax->delete();

            return ['status' => true, 'message' => 'Tax deleted successfully.'];

        } catch (Exception $e) {
            Log::error('Tax Delete Error: ' . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to delete tax.',
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function toggle_status($status, $id)
    {
        $tax = TaxModel::find($id);

        if (!$tax) {
            return ['status' => false, 'message' => 'Tax not found.'];
        }

        $tax->status = $status;

        if ($tax->save()) {
            return ['status' => true, 'message' => 'Tax updated successfully.'];
        } else {
            return ['status' => false, 'message' => 'Failed to update tax.'];
        }
    }
}
