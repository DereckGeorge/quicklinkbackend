<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'john@example.com')->exists()) {
            User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '+255700000000',
                'password' => Hash::make('securePassword123'),
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'address' => 'Mikocheni, Dar es Salaam',
                'emergency_contact' => 'Jane Doe',
                'emergency_contact_phone' => '+255700000001',
                'medical_history' => ['diabetes'],
                'allergies' => ['penicillin'],
                'blood_group' => 'O+',
                'role' => 'patient',
            ]);
        }

        // Create a doctor user linked to an existing doctor
        $doctor = \App\Models\Doctor::first();
        if ($doctor && !User::where('email', 'doctor@example.com')->exists()) {
            User::create([
                'name' => 'Jane Doe',
                'email' => 'doctor@example.com',
                'phone' => '+255700000000',
                'password' => Hash::make('securePassword123'),
                'date_of_birth' => '1988-03-10',
                'gender' => 'female',
                'address' => 'Masaki, Dar es Salaam',
                'emergency_contact' => 'John Doe',
                'emergency_contact_phone' => '+255700000010',
                'medical_history' => [],
                'allergies' => [],
                'blood_group' => 'A+',
                'role' => 'doctor',
                'doctor_id' => $doctor->id,
            ]);
        }
    }
}


