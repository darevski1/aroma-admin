<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    /**
     * Display the media library.
     */
    public function index(Request $request)
    {
        $query = Media::query()
            ->with('model')
            ->orderByDesc('created_at');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('mime_type', 'like', "%{$search}%");
            });
        }

        // Filter by collection
        if ($collection = $request->input('collection')) {
            $query->where('collection_name', $collection);
        }

        // Filter by MIME type
        if ($mimeType = $request->input('mime_type')) {
            $query->where('mime_type', 'like', "%{$mimeType}%");
        }

        // Filter by model type
        if ($modelType = $request->input('model_type')) {
            $query->where('model_type', $modelType);
        }

        // Sort
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['created_at', 'name', 'size', 'mime_type', 'collection_name'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $media = $query->paginate($request->input('per_page', 24));

        // Get unique collections for filter
        $collections = Media::distinct()->pluck('collection_name')->filter()->values();
        $modelTypes = Media::distinct()->pluck('model_type')->filter()->values();

        return view('admin_pages.media.index', compact('media', 'collections', 'modelTypes'));
    }

    /**
     * Display a single media item detail.
     */
    public function show(Request $request, Media $media)
    {
        $media->load('model');

        // AJAX consumers (e.g. the media library detail modal) expect JSON.
        if ($request->wantsJson()) {
            return response()->json([
                'id' => $media->id,
                'name' => $media->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'disk' => $media->disk,
                'url' => $media->getUrl(),
                'collection_name' => $media->collection_name,
                'custom_properties' => $media->custom_properties,
                'created_at' => $media->created_at?->toISOString(),
                'updated_at' => $media->updated_at?->toISOString(),
                'model_type' => $media->model_type,
                'model_id' => $media->model_id,
            ]);
        }

        return view('admin_pages.media.show', compact('media'));
    }

    /**
     * Upload media files.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'collection' => 'required|string|max:255',
            'model_type' => 'nullable|string|max:255',
            'model_id' => 'nullable|integer',
        ]);

        $file = $request->file('file');
        $collection = $request->input('collection', 'default');

        // Validate MIME type
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/webp', 'image/avif', 'image/gif', 'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'application/zip',
        ];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json([
                'success' => false,
                'message' => 'The file type "' . $file->getMimeType() . '" is not allowed.',
            ], 422);
        }

        // If linked to a model
        if ($request->input('model_type') && $request->input('model_id')) {
            $modelType = $request->input('model_type');
            $modelId = $request->input('model_id');

            if (!class_exists($modelType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid model type.',
                ], 422);
            }

            $model = $modelType::findOrFail($modelId);

            if (!method_exists($model, 'addMedia')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Model does not support media.',
                ], 422);
            }

            $mediaItem = $model->addMedia($file)
                ->usingName(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
                ->toMediaCollection($collection);
        } else {
            // Global media (not attached to a model yet)
            $disk = config('media-library.disk_name', 'public');
            $fileName = $file->getClientOriginalName();

            // Create the media record first to get the ID (needed for path generation)
            $mediaItem = Media::create([
                'model_type' => null,
                'model_id' => null,
                'collection_name' => $collection,
                'name' => pathinfo($fileName, PATHINFO_FILENAME),
                'file_name' => $fileName,
                'mime_type' => $file->getMimeType(),
                'disk' => $disk,
                'conversions_disk' => config('media-library.conversions_disk_name') ?? $disk,
                'size' => $file->getSize(),
                'manipulations' => [],
                'custom_properties' => [],
                'generated_conversions' => [],
                'responsive_images' => [],
            ]);

            // Store the file at {id}/{filename} to match OrganizedPathGenerator
            $file->storeAs((string) $mediaItem->id, $fileName, $disk);

            $mediaItem->file_name = $fileName;
            $mediaItem->save();

            // Generate conversions (e.g. thumbnails) synchronously where possible
            try {
                $mediaItem->generateConversions();
            } catch (\Exception $e) {
                // Don't let conversion failures break the upload
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully.',
            'media' => [
                'id' => $mediaItem->id,
                'name' => $mediaItem->name,
                'file_name' => $mediaItem->file_name,
                'mime_type' => $mediaItem->mime_type,
                'size' => $mediaItem->size,
                'url' => $mediaItem->getUrl(),
                'collection_name' => $mediaItem->collection_name,
                'created_at' => $mediaItem->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Update media metadata.
     */
    public function update(Request $request, Media $media)
    {
        // The media library detail form posts flat fields (alt_text, title, ...).
        $flat = $request->only(['alt_text', 'title', 'caption', 'copyright']);
        if ($flat) {
            $request->merge(['custom_properties' => array_merge((array) $request->input('custom_properties', []), $flat)]);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'custom_properties.alt_text' => 'nullable|string|max:500',
            'custom_properties.title' => 'nullable|string|max:255',
            'custom_properties.caption' => 'nullable|string|max:1000',
            'custom_properties.copyright' => 'nullable|string|max:255',
        ]);

        if (isset($validated['name'])) {
            $media->name = $validated['name'];
        }

        $customProperties = $media->custom_properties;
        if (isset($validated['custom_properties'])) {
            $customProperties = array_merge($customProperties, $validated['custom_properties']);
        }
        $media->custom_properties = $customProperties;
        $media->save();

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully.',
        ]);
    }

    /**
     * Delete a single media item: physical files + conversions, then DB row.
     */
    public function destroy(Media $media)
    {
        $disk = $media->disk;
        $conversionsDisk = $media->conversions_disk ?? $disk;

        // Remove physical files from disk (gracefully skip missing ones)
        $this->removeFilesFromDisk($media, $disk, $conversionsDisk);

        // Spatie's delete() handles the DB row and also attempts its own
        // file cleanup via clearMediaCollectionItems(). We call it last so
        // the DB record is removed even if disk cleanup partially failed.
        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.',
        ]);
    }

    /**
     * Bulk delete media: per-item file cleanup + DB removal.
     * One failing item does not stop the rest.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media,id',
        ]);

        $ids = $request->input('ids');
        $deleted = 0;
        $errors  = [];

        foreach ($ids as $id) {
            try {
                $media = Media::findOrFail($id);
                $disk  = $media->disk;
                $conversionsDisk = $media->conversions_disk ?? $disk;

                $this->removeFilesFromDisk($media, $disk, $conversionsDisk);
                $media->delete();
                $deleted++;
            } catch (\Exception $e) {
                $errors[] = "ID {$id}: " . $e->getMessage();
            }
        }

        $message = "{$deleted} file(s) deleted successfully.";
        if ($errors) {
            $message .= ' Some items could not be deleted: ' . implode('; ', $errors);
        }

        return response()->json([
            'success' => $deleted > 0,
            'message' => $message,
        ]);
    }

    /**
     * Remove the original file, conversions, and responsive images from disk.
     * Missing files are silently ignored.
     */
    private function removeFilesFromDisk(Media $media, string $disk, string $conversionsDisk): void
    {
        $filesystem = Storage::disk($disk);

        // Original file  —  stored at {id}/{filename} by OrganizedPathGenerator
        $originalPath = (string) $media->id . '/' . $media->file_name;
        if ($filesystem->exists($originalPath)) {
            $filesystem->delete($originalPath);
        }

        // Conversions directory  —  stored at {id}/conversions/
        $conversionsDir = (string) $media->id . '/conversions/';
        if ($filesystem->exists($conversionsDir)) {
            $filesystem->deleteDirectory($conversionsDir);
        } elseif ($conversionsDisk !== $disk) {
            // Conversions may live on a different disk
            $altFs = Storage::disk($conversionsDisk);
            if ($altFs->exists($conversionsDir)) {
                $altFs->deleteDirectory($conversionsDir);
            }
        }

        // Responsive images directory  —  stored at {id}/responsive/
        $responsiveDir = (string) $media->id . '/responsive/';
        if ($filesystem->exists($responsiveDir)) {
            $filesystem->deleteDirectory($responsiveDir);
        } elseif ($conversionsDisk !== $disk) {
            $altFs = Storage::disk($conversionsDisk);
            if ($altFs->exists($responsiveDir)) {
                $altFs->deleteDirectory($responsiveDir);
            }
        }
    }

    /**
     * Replace a media file.
     */
    public function replace(Request $request, Media $media)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Update metadata
        $media->update([
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // Store the new file at {id}/{filename} to match the path generator
        $file->storeAs((string) $media->id, $fileName, $media->disk);

        $media->file_name = $fileName;
        $media->save();

        // Regenerate conversions
        try {
            $media->generateConversions();
        } catch (\Exception $e) {
            // Don't let conversion failures break the replace
        }

        return response()->json([
            'success' => true,
            'message' => 'File replaced successfully.',
        ]);
    }

    /**
     * API endpoint for the media picker.
     */
    public function picker(Request $request)
    {
        $query = Media::query()
            ->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($collection = $request->input('collection')) {
            $query->where('collection_name', $collection);
        }

        if ($mimeType = $request->input('mime_type')) {
            $query->where('mime_type', 'like', "%{$mimeType}%");
        }

        $media = $query->paginate($request->input('per_page', 20));

        $items = $media->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'file_name' => $item->file_name,
                'mime_type' => $item->mime_type,
                'size' => $item->size,
                'url' => $item->getUrl(),
                'thumb_url' => $item->model_type ? $item->getUrl('thumb') : $item->getUrl(),
                'collection_name' => $item->collection_name,
                'custom_properties' => $item->custom_properties,
                'created_at' => $item->created_at->toISOString(),
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
            ];
        });

        return response()->json([
            'data' => $items,
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
            'per_page' => $media->perPage(),
            'total' => $media->total(),
        ]);
    }

    /**
     * Upload endpoint for the media picker.
     */
    public function pickerUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'collection' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $collection = $request->input('collection', 'default');
        $disk = config('media-library.disk_name', 'public');
        $fileName = $file->getClientOriginalName();

        // Create the media record first to get the ID
        $mediaItem = Media::create([
            'model_type' => null,
            'model_id' => null,
            'collection_name' => $collection,
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $file->getMimeType(),
            'disk' => $disk,
            'conversions_disk' => config('media-library.conversions_disk_name') ?? $disk,
            'size' => $file->getSize(),
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);

        // Store the file at {id}/{filename} to match OrganizedPathGenerator
        $file->storeAs((string) $mediaItem->id, $fileName, $disk);

        $mediaItem->file_name = $fileName;
        $mediaItem->save();

        // Generate conversions
        try {
            $mediaItem->generateConversions();
        } catch (\Exception $e) {
            // Don't let conversion failures break the upload
        }

        return response()->json([
            'id' => $mediaItem->id,
            'name' => $mediaItem->name,
            'file_name' => $mediaItem->file_name,
            'mime_type' => $mediaItem->mime_type,
            'size' => $mediaItem->size,
            'url' => $mediaItem->getUrl(),
            'thumb_url' => $mediaItem->getUrl(),
            'collection_name' => $mediaItem->collection_name,
            'created_at' => $mediaItem->created_at->toISOString(),
        ]);
    }

    /**
     * Download a media file.
     */
    public function download(Media $media)
    {
        $fullPath = $media->getPath();

        return Storage::disk($media->disk)->download($fullPath, $media->name . '.' . $media->extension);
    }

    /**
     * Get media data for API/picker consumption.
     */
    public function showJson(Media $media)
    {
        return response()->json([
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'url' => $media->getUrl(),
            'collection_name' => $media->collection_name,
            'custom_properties' => $media->custom_properties,
            'created_at' => $media->created_at->toISOString(),
            'conversions' => collect($media->getGeneratedConversions()->keys())->mapWithKeys(fn ($name) => [$name => $media->getUrl($name)]),
            'model_type' => $media->model_type,
            'model_id' => $media->model_id,
        ]);
    }
}
