<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'slug',
        'content',
        'excerpt',
        'image',
        'is_active',
        'sort_order',
        'published_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    /**
     * Scope contents that are currently active.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope contents by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope contents that are published and not expired.
     */
    public function scopePublished(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->orderBy('published_at', 'desc');
    }
}
