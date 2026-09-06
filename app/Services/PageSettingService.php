<?php

namespace App\Services;

use App\Models\PageSetting;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageSettingService
{
    /** Uploaded page files live here on the "public" disk. */
    private const FILE_DIR = 'pages/files';

    public function get_all_pages()
    {
        return PageSetting::latest()->get();
    }

    public function get_page_by_id($id)
    {
        return PageSetting::find($id);
    }

    /** Update a page from the common edit form. Every field is optional. */
    public function update_page($id, array $data): array
    {
        try {
            $page = PageSetting::find($id);

            if (!$page) {
                return ['status' => false, 'message' => 'Page not found.', 'data' => []];
            }

            $page->slug              = Str::slug($data['slug'] ?: ($data['name'] ?? $page->slug));
            $page->name              = $data['name']              ?? null;
            $page->label             = $data['label']             ?? null;
            $page->title             = $data['title']             ?? null;
            $page->short_description = $data['short_description'] ?? null;
            $page->description       = $data['description']       ?? null;
            $page->content           = $data['content']           ?? null;
            $page->meta_title        = $data['meta_title']        ?? null;
            $page->meta_description  = $data['meta_description']  ?? null;
            $page->meta_keywords     = $data['meta_keywords']     ?? null;
            $page->status            = $data['status'] ?? 1;

            // ── Optional file attachment ────────────────────
            if (!empty($data['remove_file']) && $page->file_path) {
                Storage::disk('public')->delete($page->file_path);
                $page->file_path = null;
            }

            if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
                if ($page->file_path) {
                    Storage::disk('public')->delete($page->file_path);
                }

                $file     = $data['file'];
                $fileName = Str::slug($page->slug) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $page->file_path = $file->storeAs(self::FILE_DIR, $fileName, 'public');
            }

            $page->save();

            return ['status' => true, 'message' => 'Page updated successfully.', 'data' => $page];

        } catch (Exception $e) {
            Log::error('PageSetting Update Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to update page.', 'error' => $e->getMessage()];
        }
    }

    public function toggle_status($status, $id): array
    {
        $page = PageSetting::find($id);

        if (!$page) {
            return ['status' => false, 'message' => 'Page not found.'];
        }

        $page->status = $status;

        return $page->save()
            ? ['status' => true, 'message' => 'Page status updated successfully.']
            : ['status' => false, 'message' => 'Failed to update page status.'];
    }

    /** Soft delete - the row is kept, just hidden. */
    public function delete_page($id): array
    {
        try {
            $page = PageSetting::find($id);

            if (!$page) {
                return ['status' => false, 'message' => 'Page not found.'];
            }

            $page->delete();

            return ['status' => true, 'message' => 'Page deleted successfully.'];

        } catch (Exception $e) {
            Log::error('PageSetting Delete Error: ' . $e->getMessage());
            return ['status' => false, 'message' => 'Failed to delete page.', 'error' => $e->getMessage()];
        }
    }
}
