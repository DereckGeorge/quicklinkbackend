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
        'is_polyclinic',
        'hfrs_number',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'physical_address',
        'contact_details',
        'ownership_type',
        'affiliation',
        'date_operation_began',
        'credentialing_contact',
        'registration_legal_compliance',
        'clinical_services_infrastructure',
        'key_personnel_staffing',
    ];

    protected function casts(): array
    {
        return [
            'specialties' => 'array',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'rating' => 'decimal:2',
            'has_emergency' => 'boolean',
            'is_polyclinic' => 'boolean',
            'physical_address' => 'array',
            'contact_details' => 'array',
            'affiliation' => 'array',
            'date_operation_began' => 'array',
            'credentialing_contact' => 'array',
            'registration_legal_compliance' => 'array',
            'clinical_services_infrastructure' => 'array',
            'key_personnel_staffing' => 'array',
            'verified_at' => 'datetime',
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

    /**
     * Get the documents for the hospital.
     */
    public function documents()
    {
        return $this->hasMany(HospitalDocument::class);
    }

    /**
     * Get the user who verified the hospital.
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
