<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'display_name',
        'description',
        'type',
        'is_public',
        'created_by',
        'updated_by'
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key The setting key
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed The setting value
     */
    public static function get($key, $default = null)
    {
        // Try to get from cache first
        $cacheKey = "setting_{$key}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Get from database
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        // Cache the result for future use
        Cache::put($cacheKey, $setting->value, now()->addDay());
        
        return $setting->value;
    }

    /**
     * Set a setting value
     *
     * @param string $key The setting key
     * @param mixed $value The setting value
     * @param string|null $displayName Display name for the setting
     * @param string|null $description Description of the setting
     * @param string $type The type of setting (text, number, boolean, etc.)
     * @param bool $isPublic Whether the setting is publicly accessible
     * @return Setting The setting model instance
     */
    public static function set($key, $value, $displayName = null, $description = null, $type = 'text', $isPublic = false)
    {
        // Update or create the setting
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'display_name' => $displayName ?? $key,
                'description' => $description,
                'type' => $type,
                'is_public' => $isPublic,
                'updated_by' => Auth::check() ? Auth::id() : null
            ]
        );

        // Update the cache
        Cache::put("setting_{$key}", $value, now()->addDay());

        return $setting;
    }

    /**
     * Get the current academic year
     *
     * @return int The current academic year
     */
    public static function getCurrentAcademicYear()
    {
        return (int) self::get('current_academic_year', date('Y'));
    }

    /**
     * Set the current academic year
     *
     * @param int $year The academic year to set
     * @return Setting The setting model instance
     */
    public static function setCurrentAcademicYear($year)
    {
        return self::set(
            'current_academic_year',
            $year,
            'Current Academic Year',
            'The current academic year used throughout the application',
            'number',
            true
        );
    }

    /**
     * Clear the cache for a specific setting
     *
     * @param string $key The setting key
     * @return void
     */
    public static function clearCache($key)
    {
        Cache::forget("setting_{$key}");
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public static function clearAllCache()
    {
        // Get all settings
        $settings = self::all();
        
        // Clear cache for each setting
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }
}
