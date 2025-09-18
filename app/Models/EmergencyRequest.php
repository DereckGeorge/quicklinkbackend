<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_name',
        'patient_phone',
        'patient_address',
        'patient_latitude',
        'patient_longitude',
        'emergency_type',
        'description',
        'severity',
        'status',
        'estimated_response_time',
        'assigned_hospital',
    ];

    protected function casts(): array
    {
        return [
            'patient_latitude' => 'decimal:8',
            'patient_longitude' => 'decimal:8',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
