<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for image upload functionality across the application
    |
    */

    // File upload limits
    'max_file_size' => env('IMAGE_MAX_FILE_SIZE', 10485760), // 10MB in bytes
    
    // Allowed file types
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ],
    
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    
    // Image dimension constraints
    'min_width' => 100,
    'min_height' => 100,
    'max_width' => 8000,
    'max_height' => 8000,
    
    // Storage paths
    'storage_disk' => 'public',
    
    'paths' => [
        'laboratory_forms' => 'laboratory-forms',
        'equipment_images' => 'equipment-images',
        'user_avatars' => 'user-avatars',
    ],
    
    // Cleanup settings
    'cleanup_enabled' => env('IMAGE_CLEANUP_ENABLED', true),
    'cleanup_days' => env('IMAGE_CLEANUP_DAYS', 180), // Keep files for 6 months
    
    // Security settings
    'content_scan_enabled' => env('IMAGE_CONTENT_SCAN_ENABLED', true),
    
    // Local backup settings (optional)
    'local_backup_enabled' => env('IMAGE_LOCAL_BACKUP_ENABLED', false),
    'backup_path' => env('IMAGE_BACKUP_PATH', null),
];
