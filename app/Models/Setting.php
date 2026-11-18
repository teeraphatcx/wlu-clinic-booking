<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get typed setting value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('settings.all', 3600, fn (): array => self::query()
            ->get(['key', 'value', 'type'])
            ->mapWithKeys(fn (self $setting): array => [
                $setting->key => self::castValue($setting->value, $setting->type),
            ])
            ->all());

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    /**
     * Set setting value.
     */
    public static function set(
        string $key,
        mixed $value,
        ?string $type = null,
        ?string $group = null,
        ?string $description = null,
        ?bool $isPublic = null
    ): self {
        $setting = self::firstOrNew(['key' => $key]);

        if ($type !== null) {
            $setting->type = $type;
        }

        if ($group !== null) {
            $setting->group = $group;
        }

        if ($description !== null) {
            $setting->description = $description;
        }

        if ($isPublic !== null) {
            $setting->is_public = $isPublic;
        }

        // Format value based on type
        if (is_array($value) || is_object($value)) {
            $setting->value = json_encode($value, JSON_UNESCAPED_UNICODE);
            $setting->type = 'json';
        } elseif (is_bool($value)) {
            $setting->value = $value ? '1' : '0';
            $setting->type = 'boolean';
        } else {
            $setting->value = (string) $value;
        }

        $setting->save();
        Cache::forget('settings.all');
        Cache::forget("setting.{$key}");

        return $setting;
    }

    /**
     * Cast raw string value to specified type.
     */
    protected static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'integer' => intval($value),
            'boolean' => in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
