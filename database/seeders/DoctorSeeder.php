<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\Hospital;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = Hospital::all();

        $doctors = [
            [
                'hospital_id' => $hospitals[0]->id,
                'name' => 'Dr. John Mwakalinga',
                'specialty' => 'General Medicine',
                'qualification' => 'MBBS, MD',
                'experience' => 10,
                'rating' => 4.7,
                'image_url' => 'https://api.example.com/images/doctor_d1.jpg',
                'available_days' => ['Monday', 'Tuesday', 'Wednesday', 'Friday'],
                'available_time' => '9:00 AM - 5:00 PM',
                'consultation_fee' => 30000.0,
                'bio' => 'Experienced general practitioner with 10 years of practice.',
                'languages' => ['English', 'Kiswahili'],
            ],
            [
                'hospital_id' => $hospitals[0]->id,
                'name' => 'Dr. Sarah Kimani',
                'specialty' => 'Cardiology',
                'qualification' => 'MBBS, MD, Cardiology',
                'experience' => 15,
                'rating' => 4.8,
                'image_url' => 'https://api.example.com/images/doctor_d2.jpg',
                'available_days' => ['Monday', 'Wednesday', 'Thursday'],
                'available_time' => '8:00 AM - 4:00 PM',
                'consultation_fee' => 50000.0,
                'bio' => 'Cardiologist with extensive experience in heart disease treatment.',
                'languages' => ['English', 'Kiswahili', 'French'],
            ],
            [
                'hospital_id' => $hospitals[1]->id,
                'name' => 'Dr. Amina Hassan',
                'specialty' => 'Pediatrics',
                'qualification' => 'MBBS, MD, Pediatrics',
                'experience' => 8,
                'rating' => 4.6,
                'image_url' => 'https://api.example.com/images/doctor_d3.jpg',
                'available_days' => ['Tuesday', 'Thursday', 'Saturday'],
                'available_time' => '9:00 AM - 3:00 PM',
                'consultation_fee' => 25000.0,
                'bio' => 'Pediatrician specializing in child healthcare and development.',
                'languages' => ['English', 'Kiswahili', 'Arabic'],
            ],
            [
                'hospital_id' => $hospitals[1]->id,
                'name' => 'Dr. Michael Johnson',
                'specialty' => 'Orthopedics',
                'qualification' => 'MBBS, MS, Orthopedics',
                'experience' => 12,
                'rating' => 4.9,
                'image_url' => 'https://api.example.com/images/doctor_d4.jpg',
                'available_days' => ['Monday', 'Wednesday', 'Friday'],
                'available_time' => '10:00 AM - 6:00 PM',
                'consultation_fee' => 45000.0,
                'bio' => 'Orthopedic surgeon with expertise in bone and joint surgery.',
                'languages' => ['English'],
            ],
            [
                'hospital_id' => $hospitals[2]->id,
                'name' => 'Dr. Fatima Ali',
                'specialty' => 'Dermatology',
                'qualification' => 'MBBS, MD, Dermatology',
                'experience' => 6,
                'rating' => 4.4,
                'image_url' => 'https://api.example.com/images/doctor_d5.jpg',
                'available_days' => ['Monday', 'Tuesday', 'Thursday'],
                'available_time' => '11:00 AM - 5:00 PM',
                'consultation_fee' => 35000.0,
                'bio' => 'Dermatologist specializing in skin conditions and cosmetic treatments.',
                'languages' => ['English', 'Kiswahili', 'Arabic'],
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }
    }
}