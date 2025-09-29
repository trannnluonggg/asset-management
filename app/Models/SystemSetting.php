<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_editable'
    ];

    protected $casts = [
        'is_editable' => 'boolean',
    ];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function setValue($key, $value, $type = 'string')
    {
        // Ensure value is never null to avoid database constraint issues
        if ($value === null) {
            $value = '';
        }
        
        return self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => (string) $value,
                'setting_type' => $type
            ]
        );
    }
}
