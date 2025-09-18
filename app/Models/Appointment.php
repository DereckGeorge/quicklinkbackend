<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'doctor_id',
        'user_id',
        'appointment_date',
        'time_slot',
        'patient_name',
        'patient_phone',
        'problem',
        'status',
        'amount',
        'payment_method',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
