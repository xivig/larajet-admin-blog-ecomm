<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageCropperController extends Controller
{
    /**
     * Display the Elite Image Cropper Studio.
     */
    public function index(Request $request): View
    {
        $selectedMedia = null;
        if ($request->filled('media_id')) {
            $selectedMedia = Picture::find($request->media_id);
        }

        // Fallback default image
        $defaultImageUrl = $selectedMedia 
            ? $selectedMedia->url 
            : asset('xivig-admin/img/cropper-sample.jpg');

        // Recent library items for quick load
        $recentAssets = Picture::latest()->take(12)->get();

        return view('admin.image-cropper.index', compact(
            'selectedMedia',
            'defaultImageUrl',
            'recentAssets'
        ));
    }

    /**
     * Save the cropped canvas export into the Media Library.
     */
    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'image_data' => 'required|string',
            'name' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:100',
        ]);

        $imageData = $request->image_data;

        // Verify and parse Base64 Data URL (restrict to safe raster formats)
        if (!preg_match('/^data:image\/(png|jpeg|jpg|webp|gif);base64,/i', $imageData, $type)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or unsupported image data format.',
            ], 422);
        }

        $extension = strtolower($type[1]);
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $decodedData = base64_decode($imageData, true);

        if ($decodedData === false || @getimagesizefromstring($decodedData) === false) {
            return response()->json([
                'success' => false,
                'message' => 'Decoded payload is not a valid raster image.',
            ], 422);
        }

        $subDir = 'uploads/media/' . date('Y/m');
        $targetDir = public_path($subDir);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $name = $request->filled('name') ? $request->name : 'Cropped Asset ' . date('Y-m-d H:i');
        $fileName = 'crop_' . time() . '_' . Str::random(6) . '.' . $extension;
        $fullPath = $targetDir . '/' . $fileName;

        file_put_contents($fullPath, $decodedData);

        $relativeLink = $subDir . '/' . $fileName;
        $fileSize = strlen($decodedData);
        $mimeType = 'image/' . ($extension === 'jpg' ? 'jpeg' : $extension);

        $dimensions = null;
        $imgSize = @getimagesize($fullPath);
        if ($imgSize) {
            $dimensions = $imgSize[0] . 'x' . $imgSize[1];
        }

        $picture = Picture::create([
            'name' => $name,
            'alt_text' => $name . ' (Cropped)',
            'caption' => 'Cropped with Xivig Image Cropper Studio',
            'description' => 'Precision cropped and exported on ' . date('M d, Y H:i:s'),
            'link' => $relativeLink,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'dimensions' => $dimensions,
            'folder' => $request->filled('folder') ? Str::slug($request->folder) : 'cropped',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cropped asset successfully saved to Media Library!',
            'asset' => $picture,
        ]);
    }
}
