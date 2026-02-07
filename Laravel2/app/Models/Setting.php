<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }
        $row = static::query()->where('key', $key)->first();
        if (!$row) {
            return $default;
        }
        return static::decode($row->value);
    }

    public static function set(string $key, $value): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }
        $encoded = static::encode($value);
        static::query()->updateOrCreate(['key' => $key], ['value' => $encoded]);
    }

    protected static function encode($value): string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return (string)$value;
    }

    protected static function decode(string $value)
    {
        $trim = trim($value);
        if ($trim === '') {
            return '';
        }
        $jsonFirst = substr($trim, 0, 1);
        if ($jsonFirst === '{' || $jsonFirst === '[') {
            $decoded = json_decode($trim, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return $value;
    }
}
