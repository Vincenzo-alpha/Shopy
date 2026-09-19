<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $table = 'sk_platform_settings';
    protected $primaryKey = 'setting_id_pk';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'description',
    ];

    public static function getVal(string $key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function setVal(string $key, $value, ?string $description = null): static
    {
        return static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value, 'description' => $description]
        );
    }
}
