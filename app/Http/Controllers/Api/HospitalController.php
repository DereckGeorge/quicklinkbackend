<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Hospitals",
 *     description="Hospital and doctor management endpoints"
 * )
 */
class HospitalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hospitals",
     *     tags={"Hospitals"},
     *     summary="Get nearby hospitals",
     *     description="Get hospitals with optional filtering by location and specialty",
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="User's latitude",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example="-6.8000")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="User's longitude",
     *         required=false,
     *         @OA\Schema(type="number", format="float", example="39.2847")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Search radius in kilometers",
     *         required=false,
     *         @OA\Schema(type="integer", example="50")
     *     ),
     *     @OA\Parameter(
     *         name="specialty",
     *         in="query",
     *         description="Filter by specialty",
     *         required=false,
     *         @OA\Schema(type="string", example="cardiology")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Hospitals retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", example="h1"),
     *                     @OA\Property(property="name", type="string", example="Muhimbili National Hospital"),
     *                     @OA\Property(property="address", type="string", example="United Nations Rd, Dar es Salaam"),
     *                     @OA\Property(property="latitude", type="number", example=-6.8000),
     *                     @OA\Property(property="longitude", type="number", example=39.2847),
     *                     @OA\Property(property="distance", type="number", example=2.5),
     *                     @OA\Property(property="specialties", type="array", @OA\Items(type="string"), example={"General", "Cardiology", "Ophthalmology", "Emergency"}),
     *                     @OA\Property(property="rating", type="number", example=4.5),
     *                     @OA\Property(property="phoneNumber", type="string", example="+255-22-2151591"),
     *                     @OA\Property(property="hasEmergency", type="boolean", example=true),
     *                     @OA\Property(property="imageUrl", type="string", example="https://api.example.com/images/hospital_h1.jpg"),
     *                     @OA\Property(property="doctors", type="array", @OA\Items(
     *                         @OA\Property(property="id", type="string", example="d1"),
     *                         @OA\Property(property="name", type="string", example="Dr. John Mwakalinga"),
     *                         @OA\Property(property="specialty", type="string", example="General Medicine"),
     *                         @OA\Property(property="qualification", type="string", example="MBBS, MD"),
     *                         @OA\Property(property="experience", type="integer", example=10),
     *                         @OA\Property(property="rating", type="number", example=4.7),
     *                         @OA\Property(property="imageUrl", type="string", example="https://api.example.com/images/doctor_d1.jpg"),
     *                         @OA\Property(property="availableDays", type="array", @OA\Items(type="string"), example={"Monday", "Tuesday", "Wednesday", "Friday"}),
     *                         @OA\Property(property="availableTime", type="string", example="9:00 AM - 5:00 PM"),
     *                         @OA\Property(property="consultationFee", type="number", example=30000.0),
     *                         @OA\Property(property="bio", type="string", example="Experienced general practitioner with 10 years of practice."),
     *                         @OA\Property(property="languages", type="array", @OA\Items(type="string"), example={"English", "Kiswahili"})
     *                     ))
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $radius = $request->get('radius', 50); // Default 50km
        $specialty = $request->get('specialty');

        $query = Hospital::with('doctors');

        // Filter by specialty if provided
        if ($specialty) {
            $query->whereJsonContains('specialties', $specialty);
        }

        $hospitals = $query->get();

        // Calculate distance if coordinates are provided
        if ($latitude && $longitude) {
            $hospitals = $hospitals->map(function ($hospital) use ($latitude, $longitude, $radius) {
                $distance = $this->calculateDistance($latitude, $longitude, $hospital->latitude, $hospital->longitude);
                $hospital->distance = round($distance, 2);
                return $hospital;
            })->filter(function ($hospital) use ($radius) {
                return $hospital->distance <= $radius;
            })->sortBy('distance');
        }

        return response()->json([
            'success' => true,
            'data' => $hospitals->map(function ($hospital) {
                return [
                    'id' => $hospital->id,
                    'name' => $hospital->name,
                    'address' => $hospital->address,
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude,
                    'distance' => $hospital->distance ?? null,
                    'specialties' => $hospital->specialties,
                    'rating' => $hospital->rating,
                    'phoneNumber' => $hospital->phone_number,
                    'hasEmergency' => $hospital->has_emergency,
                    'imageUrl' => $hospital->image_url,
                    'doctors' => $hospital->doctors->map(function ($doctor) {
                        return [
                            'id' => $doctor->id,
                            'name' => $doctor->name,
                            'specialty' => $doctor->specialty,
                            'qualification' => $doctor->qualification,
                            'experience' => $doctor->experience,
                            'rating' => $doctor->rating,
                            'imageUrl' => $doctor->image_url,
                            'availableDays' => $doctor->available_days,
                            'availableTime' => $doctor->available_time,
                            'consultationFee' => $doctor->consultation_fee,
                            'bio' => $doctor->bio,
                            'languages' => $doctor->languages,
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/hospitals/{hospitalId}/doctors",
     *     tags={"Hospitals"},
     *     summary="Get doctors in a specific hospital",
     *     description="Get all doctors working at a specific hospital",
     *     @OA\Parameter(
     *         name="hospitalId",
     *         in="path",
     *         description="Hospital ID",
     *         required=true,
     *         @OA\Schema(type="string", example="h1")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctors retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", example="d1"),
     *                     @OA\Property(property="name", type="string", example="Dr. John Mwakalinga"),
     *                     @OA\Property(property="specialty", type="string", example="General Medicine"),
     *                     @OA\Property(property="qualification", type="string", example="MBBS, MD"),
     *                     @OA\Property(property="experience", type="integer", example=10),
     *                     @OA\Property(property="rating", type="number", example=4.7),
     *                     @OA\Property(property="imageUrl", type="string", example="https://api.example.com/images/doctor_d1.jpg"),
     *                     @OA\Property(property="availableDays", type="array", @OA\Items(type="string"), example={"Monday", "Tuesday", "Wednesday", "Friday"}),
     *                     @OA\Property(property="availableTime", type="string", example="9:00 AM - 5:00 PM"),
     *                     @OA\Property(property="consultationFee", type="number", example=30000.0),
     *                     @OA\Property(property="bio", type="string", example="Experienced general practitioner with 10 years of practice."),
     *                     @OA\Property(property="languages", type="array", @OA\Items(type="string"), example={"English", "Kiswahili"})
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Hospital not found"
     *     )
     * )
     */
    public function getDoctors($hospitalId)
    {
        $hospital = Hospital::with('doctors')->find($hospitalId);

        if (!$hospital) {
            return response()->json([
                'success' => false,
                'message' => 'Hospital not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hospital->doctors->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialty' => $doctor->specialty,
                    'qualification' => $doctor->qualification,
                    'experience' => $doctor->experience,
                    'rating' => $doctor->rating,
                    'imageUrl' => $doctor->image_url,
                    'availableDays' => $doctor->available_days,
                    'availableTime' => $doctor->available_time,
                    'consultationFee' => $doctor->consultation_fee,
                    'bio' => $doctor->bio,
                    'languages' => $doctor->languages,
                ];
            })
        ]);
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}