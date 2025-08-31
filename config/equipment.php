<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Equipment Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing equipment-related configuration options.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Auto-reject Conflicting Requests
    |--------------------------------------------------------------------------
    |
    | When set to true, the system will automatically reject pending equipment
    | requests that have conflicting time slots when another request for the
    | same equipment is approved.
    |
    */
    'auto_reject_conflicts' => env('EQUIPMENT_AUTO_REJECT_CONFLICTS', true),

    /*
    |--------------------------------------------------------------------------
    | Conflict Detection Settings
    |--------------------------------------------------------------------------
    |
    | Settings for how conflicts are detected and handled.
    |
    */
    'conflict_detection' => [
        // Buffer time in minutes to add before/after bookings to prevent back-to-back conflicts
        'buffer_minutes' => env('EQUIPMENT_CONFLICT_BUFFER_MINUTES', 0),
        
        // Whether to check for conflicts across different days
        'cross_day_conflicts' => env('EQUIPMENT_CROSS_DAY_CONFLICTS', true),
        
        // Whether to send notifications for auto-rejections
        'notify_auto_rejection' => env('EQUIPMENT_NOTIFY_AUTO_REJECTION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-rejection Reason Templates
    |--------------------------------------------------------------------------
    |
    | Templates for auto-rejection reasons to maintain consistency.
    |
    */
    'auto_rejection_reasons' => [
        'time_conflict' => 'Automatically rejected due to time conflict with approved request #{approved_request_id} for the same equipment during overlapping time period.',
        'equipment_unavailable' => 'Automatically rejected because the equipment became unavailable.',
        'maintenance_scheduled' => 'Automatically rejected due to scheduled maintenance during the requested time period.',
    ],
];
