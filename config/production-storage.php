<?php

// config/production-storage.php - Production storage configuration example

return [
    
    /*
    |--------------------------------------------------------------------------
    | Production Storage Strategy
    |--------------------------------------------------------------------------
    |
    | Multi-tier storage configuration for production AssetLab deployment
    |
    */

    'strategy' => [
        'primary' => 's3',           // AWS S3 for primary storage
        'backup' => 's3_backup',     // S3 in different region for backup
        'cdn' => 'cloudfront',       // CloudFront CDN for fast delivery
        'cache' => 'redis',          // Redis for metadata caching
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization Settings
    |--------------------------------------------------------------------------
    */

    'optimization' => [
        'max_dimension' => 2048,     // Maximum width/height in pixels
        'quality' => 85,             // JPEG quality (1-100)
        'auto_webp' => true,         // Convert to WebP for modern browsers
        'progressive_jpeg' => true,   // Progressive JPEG loading
    ],

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Configuration
    |--------------------------------------------------------------------------
    */

    'thumbnails' => [
        'small' => [
            'width' => 150,
            'height' => 150,
            'quality' => 80
        ],
        'medium' => [
            'width' => 300,
            'height' => 300,
            'quality' => 85
        ],
        'large' => [
            'width' => 600,
            'height' => 600,
            'quality' => 90
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'virus_scanning' => true,
        'malware_detection' => true,
        'header_validation' => true,
        'content_validation' => true,
        'rate_limiting' => [
            'max_uploads_per_hour' => 50,
            'max_total_size_per_hour' => '500MB'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */

    'performance' => [
        'lazy_loading' => true,
        'image_compression' => true,
        'cdn_caching' => '365 days',
        'browser_caching' => '30 days',
        'preload_thumbnails' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup and Disaster Recovery
    |--------------------------------------------------------------------------
    */

    'backup' => [
        'enabled' => true,
        'frequency' => 'daily',
        'retention_days' => 90,
        'cross_region' => true,
        'integrity_checks' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Analytics
    |--------------------------------------------------------------------------
    */

    'monitoring' => [
        'track_usage' => true,
        'performance_metrics' => true,
        'error_reporting' => true,
        'storage_analytics' => true
    ]
];