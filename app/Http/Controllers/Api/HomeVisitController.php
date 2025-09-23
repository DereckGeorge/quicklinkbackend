<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeVisit;
use App\Models\HomeVisitBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="HomeVisits",
 *     description="Home visit discovery and booking endpoints"
 * )
 */
class HomeVisitController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/home-visits",
     *     tags={"HomeVisits"},
     *     summary="Get available home visit providers",
     *     description="List home visit providers with optional filters",
     *     @OA\Parameter(name="providerType", in="query", @OA\Schema(type="string", example="doctor")),
     *     @OA\Parameter(name="specialty", in="query", @OA\Schema(type="string", example="General Medicine")),
     *     @OA\Parameter(name="maxPrice", in="query", @OA\Schema(type="number", example=5000)),
     *     @OA\Parameter(name="latitude", in="query", @OA\Schema(type="number", format="float", example=-6.8000)),
     *     @OA\Parameter(name="longitude", in="query", @OA\Schema(type="number", format="float", example=39.2847)),
     *     @OA\Parameter(name="maxDistance", in="query", @OA\Schema(type="number", example=20)),
     *     @OA\Response(response=200, description="Providers retrieved")
     * )
     */
    public function index(Request $request)
    {
        $providerType = $request->get('providerType');
        $specialty = $request->get('specialty');
        $maxPrice = $request->get('maxPrice');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $maxDistance = $request->get('maxDistance', 50);

        $query = HomeVisit::query();

        if ($providerType) {
            $query->where('provider_type', $providerType);
        }

        if ($specialty) {
            $query->where('specialty', 'like', "%{$specialty}%");
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        $providers = $query->get();

        if ($latitude && $longitude) {
            $providers = $providers->map(function ($p) use ($latitude, $longitude) {
                $p->distance = $this->calculateDistance($latitude, $longitude, $p->latitude, $p->longitude);
                return $p;
            })->filter(function ($p) use ($maxDistance) {
                return $p->distance <= $maxDistance;
            })->values();
        }

        return response()->json([
            'success' => true,
            'data' => $providers->map(function ($p) {
                return [
                    'id' => $p->id,
                    'providerId' => $p->provider_id,
                    'providerName' => $p->provider_name,
                    'providerType' => $p->provider_type,
                    'specialty' => $p->specialty,
                    'providerImageUrl' => $p->provider_image_url,
                    'rating' => $p->rating,
                    'reviewCount' => $p->review_count,
                    'price' => $p->price,
                    'currency' => $p->currency,
                    'location' => $p->location,
                    'latitude' => $p->latitude,
                    'longitude' => $p->longitude,
                    'estimatedTravelTime' => $p->estimated_travel_time,
                    'availableDays' => $p->available_days,
                    'availableTimeSlots' => $p->available_time_slots,
                    'isAvailable' => $p->is_available,
                    'description' => $p->description,
                    'services' => $p->services,
                    'acceptsInsurance' => $p->accepts_insurance,
                ];
            })
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/home-visits/book",
     *     tags={"HomeVisits"},
     *     summary="Book home visit",
     *     description="Create a booking for a home visit provider",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"homeVisitId","patientName","patientPhone","patientAddress","patientLatitude","patientLongitude","scheduledDate","timeSlot","visitReason"},
     *             @OA\Property(property="homeVisitId", type="string", example="hv1"),
     *             @OA\Property(property="patientId", type="string", nullable=true, example="user_123"),
     *             @OA\Property(property="patientName", type="string", example="John Doe"),
     *             @OA\Property(property="patientPhone", type="string", example="+255700000000"),
     *             @OA\Property(property="patientAddress", type="string", example="Mikocheni, Dar es Salaam"),
     *             @OA\Property(property="patientLatitude", type="number", format="float", example=-6.8235),
     *             @OA\Property(property="patientLongitude", type="number", format="float", example=39.2695),
     *             @OA\Property(property="scheduledDate", type="string", format="date", example="2025-09-25"),
     *             @OA\Property(property="timeSlot", type="string", example="10:00"),
     *             @OA\Property(property="visitReason", type="string", example="Regular checkup"),
     *             @OA\Property(property="symptoms", type="string", nullable=true, example="None")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Booked")
     * )
     */
    public function book(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'homeVisitId' => 'required|exists:home_visits,id',
            'patientId' => 'nullable|string',
            'patientName' => 'required|string|max:255',
            'patientPhone' => 'required|string|max:20',
            'patientAddress' => 'required|string',
            'patientLatitude' => 'required|numeric',
            'patientLongitude' => 'required|numeric',
            'scheduledDate' => 'required|date|after:today',
            'timeSlot' => 'required|string',
            'visitReason' => 'required|string',
            'symptoms' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $homeVisit = HomeVisit::find($request->homeVisitId);

        $booking = HomeVisitBooking::create([
            'home_visit_id' => $homeVisit->id,
            'provider_id' => $homeVisit->provider_id,
            'provider_name' => $homeVisit->provider_name,
            'provider_type' => $homeVisit->provider_type,
            'user_id' => auth()->id(),
            'patient_name' => $request->patientName,
            'patient_phone' => $request->patientPhone,
            'patient_address' => $request->patientAddress,
            'patient_latitude' => $request->patientLatitude,
            'patient_longitude' => $request->patientLongitude,
            'scheduled_date' => $request->scheduledDate . ' ' . $this->mapTimeSlot($request->timeSlot),
            'time_slot' => $request->timeSlot,
            'visit_reason' => $request->visitReason,
            'symptoms' => $request->symptoms,
            'amount' => $homeVisit->price,
            'currency' => $homeVisit->currency,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Home visit booked successfully',
            'data' => [
                'id' => $booking->id,
                'homeVisitId' => $homeVisit->id,
                'providerId' => $booking->provider_id,
                'providerName' => $booking->provider_name,
                'providerType' => $booking->provider_type,
                'patientId' => $booking->user_id,
                'patientName' => $booking->patient_name,
                'patientPhone' => $booking->patient_phone,
                'patientAddress' => $booking->patient_address,
                'patientLatitude' => $booking->patient_latitude,
                'patientLongitude' => $booking->patient_longitude,
                'scheduledDate' => $booking->scheduled_date->toISOString(),
                'timeSlot' => $booking->time_slot,
                'visitReason' => $booking->visit_reason,
                'symptoms' => $booking->symptoms,
                'amount' => $booking->amount,
                'currency' => $booking->currency,
                'status' => $booking->status,
                'paymentStatus' => $booking->payment_status,
                'notes' => $booking->notes,
                'actualVisitTime' => $booking->actual_visit_time?->toISOString(),
                'completedTime' => $booking->completed_time?->toISOString(),
                'createdAt' => $booking->created_at->toISOString(),
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/home-visits/bookings",
     *     tags={"HomeVisits"},
     *     summary="Get user's home visit bookings",
     *     description="List bookings for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Bookings retrieved")
     * )
     */
    public function myBookings(Request $request)
    {
        $bookings = HomeVisitBooking::with('homeVisit')
            ->where('user_id', auth()->id())
            ->orderByDesc('scheduled_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings->map(function ($b) {
                return [
                    'id' => $b->id,
                    'homeVisitId' => $b->home_visit_id,
                    'providerId' => $b->provider_id,
                    'providerName' => $b->provider_name,
                    'providerType' => $b->provider_type,
                    'patientId' => $b->user_id,
                    'patientName' => $b->patient_name,
                    'patientPhone' => $b->patient_phone,
                    'patientAddress' => $b->patient_address,
                    'patientLatitude' => $b->patient_latitude,
                    'patientLongitude' => $b->patient_longitude,
                    'scheduledDate' => $b->scheduled_date->toISOString(),
                    'timeSlot' => $b->time_slot,
                    'visitReason' => $b->visit_reason,
                    'symptoms' => $b->symptoms,
                    'amount' => $b->amount,
                    'currency' => $b->currency,
                    'status' => $b->status,
                    'paymentStatus' => $b->payment_status,
                    'notes' => $b->notes,
                    'actualVisitTime' => $b->actual_visit_time?->toISOString(),
                    'completedTime' => $b->completed_time?->toISOString(),
                    'createdAt' => $b->created_at->toISOString(),
                ];
            })
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c, 2);
    }

    private function mapTimeSlot($timeSlot)
    {
        $map = [
            '09:00' => '09:00:00',
            '10:00' => '10:00:00',
            '11:00' => '11:00:00',
            '14:00' => '14:00:00',
            '15:00' => '15:00:00',
            '16:00' => '16:00:00',
        ];
        return $map[$timeSlot] ?? '10:00:00';
    }
}


