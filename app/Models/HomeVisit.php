<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'provider_name',
        'provider_type',
        'specialty',
        'provider_image_url',
        'rating',
        'review_count',
        'price',
        'currency',
        'location',
        'latitude',
        'longitude',
        'estimated_travel_time',
        'available_days',
        'available_time_slots',
        'is_available',
        'description',
        'services',
        'accepts_insurance',
    ];

    protected function casts(): array
    {
        return [
            'available_days' => 'array',
            'available_time_slots' => 'array',
            'services' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'rating' => 'decimal:2',
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'accepts_insurance' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(HomeVisitBooking::class);
    }
}
