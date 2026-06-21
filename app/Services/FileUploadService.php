<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    protected $imageManager;

    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Upload file (image) with optional thumbnail
     *
     * @param file $file
     * @param string $folder (e.g. category, product, users)
     * @param bool $thumbnail
     * @param int $thumbWidth
     * @param int $thumbHeight
     */
    public function uploadImage($file, $folder = 'uploads', $thumbnail = true, $thumbWidth = 300, $thumbHeight = 300)
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        //store original
        $file->storeAs($folder, $filename, 'public');

        //create thumbnail if needed
        if ($thumbnail) {

            $image = $this->imageManager->read($file);
            $image->cover($thumbWidth, $thumbHeight);

            Storage::disk('public')->put(
                $folder . '/thumb/' . $filename,
                (string) $image->encode()
            );
        }

        return $filename;
    }

    /**
     * Delete file + thumbnail safely
     */
    public function deleteImage($filename, $folder = 'uploads')
    {
        if (!$filename) return;

        Storage::disk('public')->delete($folder . '/' . $filename);
        Storage::disk('public')->delete($folder . '/thumb/' . $filename);
    }

    /**
     * Get full URL of file
     */
    public function getUrl($folder, $filename)
    {
        if (!$filename) return null;

        return asset('storage/' . $folder . '/' . $filename);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbUrl($folder, $filename)
    {
        if (!$filename) return null;

        return asset('storage/' . $folder . '/thumb/' . $filename);
    }
}