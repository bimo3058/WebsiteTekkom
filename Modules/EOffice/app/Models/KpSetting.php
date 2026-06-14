<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class KpSetting extends Model
{
    protected $table = 'eo_kp_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Helper to get a setting value.
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set a setting value.
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
