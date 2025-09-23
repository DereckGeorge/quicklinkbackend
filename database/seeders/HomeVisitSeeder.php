<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomeVisit;

class HomeVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'provider_id' => 'doc1',
                'provider_name' => 'Dr. Sarah Mwangi',
                'provider_type' => 'doctor',
                'specialty' => 'General Medicine',
                'provider_image_url' => 'https://api.example.com/images/provider_doc1.jpg',
                'rating' => 4.8,
                'review_count' => 127,
                'price' => 2500.0,
                'currency' => 'TZS',
                'location' => 'Mikocheni, Dar es Salaam',
                'latitude' => -6.8235,
                'longitude' => 39.2695,
                'estimated_travel_time' => 25,
                'available_days' => ['monday','tuesday','wednesday','thursday','friday'],
                'available_time_slots' => ['09:00','10:00','11:00','14:00','15:00','16:00'],
                'is_available' => true,
                'description' => 'Experienced general practitioner available for home visits.',
                'services' => ['diagnosis','prescription','basic care','elderly care'],
                'accepts_insurance' => true,
            ],
            [
                'provider_id' => 'nurse1',
                'provider_name' => 'Nurse John K',
                'provider_type' => 'nurse',
                'specialty' => 'Home Care Nursing',
                'provider_image_url' => 'https://api.example.com/images/provider_nurse1.jpg',
                'rating' => 4.6,
                'review_count' => 89,
                'price' => 1800.0,
                'currency' => 'TZS',
                'location' => 'Masaki, Dar es Salaam',
                'latitude' => -6.7480,
                'longitude' => 39.2780,
                'estimated_travel_time' => 30,
                'available_days' => ['monday','tuesday','wednesday','thursday','friday','saturday'],
                'available_time_slots' => ['08:00','09:00','10:00','13:00','15:00'],
                'is_available' => true,
                'description' => 'Skilled nurse for home care and wound dressing.',
                'services' => ['wound care','vital signs','medication admin'],
                'accepts_insurance' => false,
            ],
            [
                'provider_id' => 'doc2',
                'provider_name' => 'Dr. Kelvin T',
                'provider_type' => 'doctor',
                'specialty' => 'Pediatrics',
                'provider_image_url' => 'https://api.example.com/images/provider_doc2.jpg',
                'rating' => 4.7,
                'review_count' => 64,
                'price' => 3200.0,
                'currency' => 'TZS',
                'location' => 'Kinondoni, Dar es Salaam',
                'latitude' => -6.8005,
                'longitude' => 39.2500,
                'estimated_travel_time' => 22,
                'available_days' => ['tuesday','thursday','saturday'],
                'available_time_slots' => ['09:00','10:00','11:00','14:00'],
                'is_available' => true,
                'description' => 'Pediatrician for child home care visits.',
                'services' => ['diagnosis','prescription','follow up'],
                'accepts_insurance' => true,
            ],
        ];

        foreach ($providers as $p) {
            HomeVisit::create($p);
        }
    }
}


