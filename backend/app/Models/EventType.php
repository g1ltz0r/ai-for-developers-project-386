<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'duration_minutes',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'event_type_id');
    }
}
