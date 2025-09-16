<?php

// config/local-storage.php - On-premises storage configuration

return [

    /*
    |--------------------------------------------------------------------------
    | Local Storage Configuration for Production
    |--------------------------------------------------------------------------
    |
    | Optimized settings for self-hosted AssetLab without cloud dependencies
    |
    */

    'strategy' => 'local_redundant', // Local with backup redundancy

    /*
    |--------------------------------------------------------------------------
    | Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'primary' => storage_path('app/public/laboratory-forms'),
        'backup' => storage_path('app/backup/laboratory-forms'),
        'archive' => storage_path('app/archive/laboratory-forms'),
        'temp' => storage_path('app/temp'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Quotas and Limits
    |--------------------------------------------------------------------------
    */

    'quotas' => [
        'max_per_laboratory' => 5 * 1024 * 1024 * 1024, // 5GB per lab
        'max_file_size' => 10 * 1024 * 1024,            // 10MB per file
        'min_free_space' => 100 * 1024 * 1024,          // Keep 100MB free
        'warning_threshold' => 0.9,                      // Warn at 90% usage
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    */

    'optimization' => [
        'max_dimension' => 1920,        // Reasonable for local storage
        'quality' => 85,                // Good balance of quality/size
        'auto_compress' => true,
        'generate_webp' => false,       // Skip WebP for simplicity
        'progressive_jpeg' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Configuration
    |--------------------------------------------------------------------------
    */

    'thumbnails' => [
        'enabled' => true,
        'sizes' => [
            'thumb' => 150,     // For listings and previews
            'medium' => 400,    // For modal displays
        ],
        'quality' => 80,
        'format' => 'jpg',      // Consistent format
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup and Redundancy
    |--------------------------------------------------------------------------
    */

    'backup' => [
        'enabled' => true,
        'method' => 'local_copy',           // Simple file copying
        'verify_integrity' => true,
        'cleanup_failed_backups' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Management
    |--------------------------------------------------------------------------
    */

    'management' => [
        'archive_after_days' => 365,       // Archive files after 1 year
        'delete_after_days' => 1095,       // Delete after 3 years (from archive)
        'metadata_tracking' => true,
        'access_logging' => false,          // Disable for performance
        'auto_cleanup' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Optimization
    |--------------------------------------------------------------------------
    */

    'performance' => [
        'lazy_loading' => true,
        'cache_thumbnails' => true,
        'optimize_delivery' => [
            'gzip_compression' => true,
            'browser_caching' => '30 days',
            'etags' => true,
        ],
        'batch_processing' => false,        // Avoid server overload
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'file_validation' => [
            'check_mime_type' => true,
            'check_file_extension' => true,
            'check_file_headers' => true,
            'scan_for_malware' => false,    // Optional: ClamAV integration
        ],
        'access_control' => [
            'restrict_direct_access' => false,  // Files served via web server
            'rate_limiting' => [
                'enabled' => true,
                'max_uploads_per_hour' => 20,
                'max_size_per_hour' => '100MB',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Logging
    |--------------------------------------------------------------------------
    */

    'monitoring' => [
        'track_storage_usage' => true,
        'log_file_operations' => true,
        'performance_metrics' => false,     // Keep lightweight
        'error_notifications' => true,
        'storage_alerts' => [
            'disk_space_warning' => '85%',
            'quota_warning' => '90%',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance and Cleanup
    |--------------------------------------------------------------------------
    */

    'maintenance' => [
        'auto_cleanup' => [
            'enabled' => true,
            'schedule' => 'daily',          // Via Laravel scheduler
            'time' => '02:00',              // 2 AM server time
        ],
        'integrity_checks' => [
            'enabled' => true,
            'schedule' => 'weekly',
            'repair_corrupted' => true,
        ],
        'backup_verification' => [
            'enabled' => true,
            'schedule' => 'weekly',
        ],
    ],
];