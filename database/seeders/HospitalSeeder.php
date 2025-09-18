<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = [
            [
                'name' => 'Muhimbili National Hospital',
                'address' => 'United Nations Rd, Dar es Salaam',
                'latitude' => -6.8000,
                'longitude' => 39.2847,
                'specialties' => ['General', 'Cardiology', 'Ophthalmology', 'Emergency'],
                'rating' => 4.5,
                'phone_number' => '+255-22-2151591',
                'has_emergency' => true,
                'image_url' => 'https://api.example.com/images/hospital_h1.jpg',
            ],
            [
                'name' => 'Aga Khan Hospital',
                'address' => 'Ocean Road, Dar es Salaam',
                'latitude' => -6.7892,
                'longitude' => 39.2723,
                'specialties' => ['General', 'Pediatrics', 'Gynecology', 'Orthopedics'],
                'rating' => 4.7,
                'phone_number' => '+255-22-2115151',
                'has_emergency' => true,
                'image_url' => 'https://api.example.com/images/hospital_h2.jpg',
            ],
            [
                'name' => 'TMJ Hospital',
                'address' => 'Kinondoni, Dar es Salaam',
                'latitude' => -6.8235,
                'longitude' => 39.2695,
                'specialties' => ['General', 'Dermatology', 'ENT'],
                'rating' => 4.2,
                'phone_number' => '+255-22-2660100',
                'has_emergency' => false,
                'image_url' => 'https://api.example.com/images/hospital_h3.jpg',
            ],
        ];

        foreach ($hospitals as $hospital) {
            Hospital::create($hospital);
        }
    }
}