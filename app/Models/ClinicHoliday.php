<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicHoliday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'is_full_day',
        'start_time',
        'end_time',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_full_day' => 'boolean',
        ];
    }
}
