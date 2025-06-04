<?php

namespace App\Services;

class DaysOfWeekHelper
{
    /**
     * Array of day names indexed by day number (0 = Sunday)
     */
    public const DAYS = [
        0 => 'Sunday',
        1 => 'Monday', 
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday'
    ];

    /**
     * Array of short day names indexed by day number
     */
    public const SHORT_DAYS = [
        0 => 'Sun',
        1 => 'Mon',
        2 => 'Tue', 
        3 => 'Wed',
        4 => 'Thu',
        5 => 'Fri',
        6 => 'Sat'
    ];

    /**
     * Get day name by number
     *
     * @param int $dayNumber
     * @param bool $short
     * @return string
     */
    public static function getDayName(int $dayNumber, bool $short = false): string
    {
        $days = $short ? self::SHORT_DAYS : self::DAYS;
        return $days[$dayNumber] ?? 'Unknown';
    }

    /**
     * Get all days as array
     *
     * @param bool $short
     * @return array
     */
    public static function getAllDays(bool $short = false): array
    {
        return $short ? self::SHORT_DAYS : self::DAYS;
    }

    /**
     * Get days as select options for HTML forms
     *
     * @param int|null $selected
     * @param bool $includeEmpty
     * @return array
     */
    public static function getSelectOptions(?int $selected = null, bool $includeEmpty = true): array
    {
        $options = [];
        
        if ($includeEmpty) {
            $options[] = ['value' => '', 'text' => 'Select Day', 'selected' => false];
        }
        
        foreach (self::DAYS as $value => $text) {
            $options[] = [
                'value' => $value,
                'text' => $text,
                'selected' => $selected === $value
            ];
        }
        
        return $options;
    }

    /**
     * Get weekdays only (Monday-Friday)
     *
     * @param bool $short
     * @return array
     */
    public static function getWeekdays(bool $short = false): array
    {
        $days = $short ? self::SHORT_DAYS : self::DAYS;
        return array_slice($days, 1, 5, true); // Monday (1) to Friday (5)
    }

    /**
     * Check if a day number is a weekday
     *
     * @param int $dayNumber
     * @return bool
     */
    public static function isWeekday(int $dayNumber): bool
    {
        return $dayNumber >= 1 && $dayNumber <= 5;
    }

    /**
     * Check if a day number is a weekend
     *
     * @param int $dayNumber
     * @return bool
     */
    public static function isWeekend(int $dayNumber): bool
    {
        return $dayNumber === 0 || $dayNumber === 6;
    }
}
