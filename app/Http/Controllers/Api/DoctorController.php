<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Doctors",
 *     description="Doctor availability and management endpoints"
 * )
 */
class DoctorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/doctors/{doctorId}/availability",
     *     tags={"Doctors"},
     *     summary="Check doctor availability",
     *     description="Check if a doctor is available on a specific date and get available time slots",
     *     @OA\Parameter(
     *         name="doctorId",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *         @OA\Schema(type="string", example="d1")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date to check availability (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-20")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Availability retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="isAvailable", type="boolean", example=true),
     *                 @OA\Property(property="availableTimeSlots", type="array", @OA\Items(type="string"), example={"9:00 AM", "10:00 AM", "11:00 AM", "2:00 PM", "3:00 PM", "4:00 PM"}),
     *                 @OA\Property(property="alternativeDates", type="array", @OA\Items(type="string", format="date"), example={"2024-01-21", "2024-01-23", "2024-01-27"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found"
     *     )
     * )
     */
    public function getAvailability($doctorId, Request $request)
    {
        $doctor = Doctor::find($doctorId);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        $requestedDate = $request->get('date');
        $dayOfWeek = $requestedDate ? date('l', strtotime($requestedDate)) : null;

        // Check if doctor is available on the requested day
        $isAvailable = false;
        $availableTimeSlots = [];

        if ($dayOfWeek && in_array($dayOfWeek, $doctor->available_days)) {
            $isAvailable = true;
            // Generate time slots based on doctor's available time
            $availableTimeSlots = $this->generateTimeSlots($doctor->available_time);
        }

        // Generate alternative dates for the next 2 weeks
        $alternativeDates = $this->generateAlternativeDates($doctor->available_days, $requestedDate);

        return response()->json([
            'success' => true,
            'data' => [
                'isAvailable' => $isAvailable,
                'availableTimeSlots' => $availableTimeSlots,
                'alternativeDates' => $alternativeDates
            ]
        ]);
    }

    /**
     * Generate time slots based on doctor's available time
     */
    private function generateTimeSlots($availableTime)
    {
        // Parse the available time string (e.g., "9:00 AM - 5:00 PM")
        $timeSlots = [];
        
        // Default time slots for demonstration
        $defaultSlots = [
            "9:00 AM", "10:00 AM", "11:00 AM", 
            "2:00 PM", "3:00 PM", "4:00 PM"
        ];

        // In a real application, you would parse the available_time string
        // and generate slots based on the actual time range
        return $defaultSlots;
    }

    /**
     * Generate alternative dates when doctor is available
     */
    private function generateAlternativeDates($availableDays, $currentDate = null)
    {
        $alternativeDates = [];
        $startDate = $currentDate ? date('Y-m-d', strtotime($currentDate . ' +1 day')) : date('Y-m-d', strtotime('+1 day'));
        
        $dayMap = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 0
        ];

        $availableDayNumbers = array_map(function($day) use ($dayMap) {
            return $dayMap[$day];
        }, $availableDays);

        $current = strtotime($startDate);
        $end = strtotime('+14 days', $current);

        while ($current <= $end && count($alternativeDates) < 5) {
            $dayOfWeek = (int)date('w', $current);
            
            if (in_array($dayOfWeek, $availableDayNumbers)) {
                $alternativeDates[] = date('Y-m-d', $current);
            }
            
            $current = strtotime('+1 day', $current);
        }

        return $alternativeDates;
    }
}