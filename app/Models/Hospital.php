<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'specialties',
        'rating',
        'phone_number',
        'has_emergency',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'specialties' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'rating' => 'decimal:2',
            'has_emergency' => 'boolean',
        ];
    }

    /**
     * Get the doctors for the hospital.
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get the appointments for the hospital.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
