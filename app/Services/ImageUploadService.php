<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Production-ready image upload service with security, validation, and storage optimization
 */
class ImageUploadService
{
    protected $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    protected $maxFileSize = 10485760; // 10MB in bytes

    /**
     * Upload and process laboratory form image
     */
    public function uploadLaboratoryFormImage(UploadedFile $file, int $userId, int $laboratoryId): array
    {
        try {
            // Validate the file
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }

            // Generate secure filename and path
            $filename = $this->generateSecureFilename($file);
            $directory = "laboratory-forms/{$laboratoryId}/" . date('Y/m');
            $fullPath = "{$directory}/{$filename}";

            // Store the image locally
            $stored = Storage::disk('public')->putFileAs($directory, $file, $filename);
            
            if (!$stored) {
                return [
                    'success' => false,
                    'message' => 'Failed to store image. Please try again.'
                ];
            }

            // Log successful upload
            Log::info('Laboratory form image uploaded', [
                'user_id' => $userId,
                'laboratory_id' => $laboratoryId,
                'filename' => $filename,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'path' => $fullPath
            ]);

            return [
                'success' => true,
                'path' => $fullPath,
                'url' => asset('storage/' . $fullPath),
                'filename' => $filename,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ];

        } catch (\Exception $e) {
            Log::error('Image upload failed', [
                'user_id' => $userId,
                'laboratory_id' => $laboratoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while uploading the image. Please try again.'
            ];
        }
    }

    /**
     * Validate uploaded file with comprehensive security checks
     */
    protected function validateFile(UploadedFile $file): array
    {
        // Check if file upload was successful
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'message' => 'File upload failed. Please try again.'
            ];
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            return [
                'valid' => false,
                'message' => 'File size must be less than ' . ($this->maxFileSize / 1024 / 1024) . 'MB.'
            ];
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            return [
                'valid' => false,
                'message' => 'Only JPEG, PNG, GIF, and WebP images are allowed.'
            ];
        }

        // Check file extension matches MIME type
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $allowedExtensions)) {
            return [
                'valid' => false,
                'message' => 'Invalid file extension. Only JPG, PNG, GIF, and WebP files are allowed.'
            ];
        }

        // Verify it's actually an image file
        try {
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo === false) {
                return [
                    'valid' => false,
                    'message' => 'Invalid image file.'
                ];
            }

            // Check image dimensions
            $width = $imageInfo[0];
            $height = $imageInfo[1];
            
            if ($width < 100 || $height < 100) {
                return [
                    'valid' => false,
                    'message' => 'Image must be at least 100x100 pixels.'
                ];
            }

            if ($width > 8000 || $height > 8000) {
                return [
                    'valid' => false,
                    'message' => 'Image must not exceed 8000x8000 pixels.'
                ];
            }

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Unable to process image file.'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Generate secure filename to prevent directory traversal and conflicts
     */
    protected function generateSecureFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Create a hash from file content and metadata for uniqueness
        $content = file_get_contents($file->getPathname());
        $hash = hash('sha256', $content . time() . Str::random(16) . $file->getClientOriginalName());
        
        // Take first 32 characters of hash for filename
        return substr($hash, 0, 32) . '.' . $extension;
    }

    /**
     * Delete image and its variants
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            // Delete main image
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
                Log::info('Image deleted successfully', ['path' => $imagePath]);
                return true;
            }
            
            Log::warning('Image not found for deletion', ['path' => $imagePath]);
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to delete image', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get image URL
     */
    public function getImageUrl(string $imagePath): ?string
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return asset('storage/' . $imagePath);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get image URL', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Clean up old images (for scheduled cleanup tasks)
     */
    public function cleanupOldImages(int $daysOld = 90): int
    {
        $deletedCount = 0;
        $cutoffDate = now()->subDays($daysOld);
        
        try {
            $directories = Storage::disk('public')->directories('laboratory-forms');
            
            foreach ($directories as $directory) {
                $files = Storage::disk('public')->allFiles($directory);
                
                foreach ($files as $file) {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    if ($lastModified && $lastModified < $cutoffDate->timestamp) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
            }
            
            Log::info("Cleaned up {$deletedCount} old images");
        } catch (\Exception $e) {
            Log::error('Failed to cleanup old images', ['error' => $e->getMessage()]);
        }

        return $deletedCount;
    }

    /**
     * Get image file information
     */
    public function getImageInfo(string $imagePath): ?array
    {
        try {
            if (!Storage::disk('public')->exists($imagePath)) {
                return null;
            }

            $fullPath = Storage::disk('public')->path($imagePath);
            $size = Storage::disk('public')->size($imagePath);
            $lastModified = Storage::disk('public')->lastModified($imagePath);
            
            $imageInfo = getimagesize($fullPath);
            
            return [
                'path' => $imagePath,
                'url' => asset('storage/' . $imagePath),
                'size' => $size,
                'size_formatted' => $this->formatBytes($size),
                'last_modified' => $lastModified,
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
                'mime_type' => $imageInfo['mime'] ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to get image info', [
                'path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
