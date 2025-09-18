<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeVisitBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_visit_id',
        'provider_id',
        'provider_name',
        'provider_type',
        'user_id',
        'patient_name',
        'patient_phone',
        'patient_address',
        'patient_latitude',
        'patient_longitude',
        'scheduled_date',
        'time_slot',
        'visit_reason',
        'symptoms',
        'amount',
        'currency',
        'status',
        'payment_status',
        'notes',
        'actual_visit_time',
        'completed_time',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'datetime',
            'patient_latitude' => 'decimal:8',
            'patient_longitude' => 'decimal:8',
            'amount' => 'decimal:2',
            'actual_visit_time' => 'datetime',
            'completed_time' => 'datetime',
        ];
    }

    public function homeVisit()
    {
        return $this->belongsTo(HomeVisit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
