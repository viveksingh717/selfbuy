<?php
namespace App\Services;

use App\Models\Category;
use App\Models\SubCategory;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryService {
    public function save_category($data)
    {
        try {

            $category = new Category();

            $category->category_name     = $data['category_name'];
            $category->category_slug     = $data['category_slug'];
            $category->description       = $data['description'] ?? null;
            $category->category_image    = $data['category_image'] ?? null;
            $category->is_featured       = $data['is_featured'];
            $category->status            = $data['status'];
            $category->meta_title        = $data['meta_title'] ?? null;
            $category->meta_description  = $data['meta_description'] ?? null;

            $category->save();

            return [
                'status' => true,
                'message' => 'Category saved successfully',
                'data' => $category
            ];

        } catch (Exception $e) {

            Log::error('Category Save Error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to save category',
                'error' => $e->getMessage()
            ];
        }
    }

    public function update_category($id, $data)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return [
                    'status' => false,
                    'message' => 'Category data not found.',
                    'data' => []
                ];
            }

            $category->category_name     = $data['category_name'];
            $category->category_slug     = $data['category_slug'];
            $category->description       = $data['description'];
            $category->category_image    = $data['category_image'] ?? $category->category_image;
            $category->is_featured       = $data['is_featured'];
            $category->status            = $data['status'];
            $category->meta_title        = $data['meta_title'];
            $category->meta_description  = $data['meta_description'];

            $category->save();

            return [
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ];

        } catch (Exception $e) {

            Log::error('Category update error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ];
        }
    }

    // SubCategory related services can be added here
    public function getActiveCategories()
    {
        return Category::query()
            ->where('status', 1)
            ->orderBy('category_name')
            ->get();
    }

    public function save_subcategory($data)
    {
        try {

            $subcategory = new SubCategory();

            $subcategory->subcategory_name     = $data['subcategory_name'];
            $subcategory->subcategory_slug     = $data['subcategory_slug'];
            $subcategory->category_id          = $data['category_id'];
            $subcategory->status               = $data['status'];

            $subcategory->save();

            return [
                'status' => true,
                'message' => 'SubCategory saved successfully',
                'data' => $subcategory
            ];

        } catch (Exception $e) {

            Log::error('SubCategory Save Error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to save subcategory',
                'error' => $e->getMessage()
            ];
        }
    }

    public function update_subcategory($id, $data)
    {
        try {

            $subcategory = SubCategory::find($id);

            if (!$subcategory) {
                return [
                    'status' => false,
                    'message' => 'Subcategory data not found.',
                    'data' => []
                ];
            }

            $subcategory->subcategory_name = $data['subcategory_name'];
            $subcategory->subcategory_slug = $data['subcategory_slug'];
            $subcategory->category_id = $data['category_id'];
            $subcategory->status = $data['status'];
            $subcategory->save();

            return [
                'status' => true,
                'message' => 'Subcategory updated successfully',
                'data' => $subcategory
            ];

        } catch (Exception $e) {

            Log::error('SubCategory update error: ' . $e->getMessage());

            return [
                'status' => false,
                'message' => 'Failed to update subcategory',
                'error' => $e->getMessage()
            ];
        }
    }

}
