<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'name',
        'specialty',
        'qualification',
        'experience',
        'rating',
        'image_url',
        'available_days',
        'available_time',
        'consultation_fee',
        'bio',
        'languages',
    ];

    protected function casts(): array
    {
        return [
            'available_days' => 'array',
            'languages' => 'array',
            'rating' => 'decimal:2',
            'consultation_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the hospital that owns the doctor.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
