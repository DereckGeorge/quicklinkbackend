<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Appointments",
 *     description="Appointment management endpoints"
 * )
 */
class AppointmentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/appointments",
     *     tags={"Appointments"},
     *     summary="Book hospital appointment",
     *     description="Book an appointment with a doctor at a hospital",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"hospitalId","doctorId","appointmentDate","timeSlot","patientName","patientPhone","problem","paymentMethod"},
     *             @OA\Property(property="hospitalId", type="string", example="h1"),
     *             @OA\Property(property="doctorId", type="string", example="d1"),
     *             @OA\Property(property="appointmentDate", type="string", format="date", example="2024-01-20"),
     *             @OA\Property(property="timeSlot", type="string", example="10:00 AM"),
     *             @OA\Property(property="patientName", type="string", example="John Doe"),
     *             @OA\Property(property="patientPhone", type="string", example="+255700000000"),
     *             @OA\Property(property="problem", type="string", example="Regular checkup"),
     *             @OA\Property(property="paymentMethod", type="string", example="mpesa")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment booked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Appointment booked successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="apt_123"),
     *                 @OA\Property(property="hospitalId", type="string", example="h1"),
     *                 @OA\Property(property="hospitalName", type="string", example="Muhimbili National Hospital"),
     *                 @OA\Property(property="doctorId", type="string", example="d1"),
     *                 @OA\Property(property="doctorName", type="string", example="Dr. John Mwakalinga"),
     *                 @OA\Property(property="doctorSpecialty", type="string", example="General Medicine"),
     *                 @OA\Property(property="appointmentDate", type="string", format="date-time", example="2024-01-20T10:00:00Z"),
     *                 @OA\Property(property="timeSlot", type="string", example="10:00 AM"),
     *                 @OA\Property(property="patientName", type="string", example="John Doe"),
     *                 @OA\Property(property="patientPhone", type="string", example="+255700000000"),
     *                 @OA\Property(property="problem", type="string", example="Regular checkup"),
     *                 @OA\Property(property="status", type="string", example="confirmed"),
     *                 @OA\Property(property="amount", type="number", example=30500.0),
     *                 @OA\Property(property="paymentMethod", type="string", example="mpesa"),
     *                 @OA\Property(property="paymentStatus", type="string", example="pending"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospitalId' => 'required|exists:hospitals,id',
            'doctorId' => 'required|exists:doctors,id',
            'appointmentDate' => 'required|date|after:today',
            'timeSlot' => 'required|string',
            'patientName' => 'required|string|max:255',
            'patientPhone' => 'required|string|max:20',
            'problem' => 'required|string',
            'paymentMethod' => 'required|string|in:mpesa,card,cash',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $doctor = Doctor::with('hospital')->find($request->doctorId);
        $hospital = Hospital::find($request->hospitalId);

        // Calculate amount (consultation fee + 500 TZS service fee)
        $amount = $doctor->consultation_fee + 500;

        $appointment = Appointment::create([
            'hospital_id' => $request->hospitalId,
            'doctor_id' => $request->doctorId,
            'user_id' => auth()->id(),
            'appointment_date' => $request->appointmentDate . ' ' . $this->convertTimeSlot($request->timeSlot),
            'time_slot' => $request->timeSlot,
            'patient_name' => $request->patientName,
            'patient_phone' => $request->patientPhone,
            'problem' => $request->problem,
            'status' => 'confirmed',
            'amount' => $amount,
            'payment_method' => $request->paymentMethod,
            'payment_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully',
            'data' => [
                'id' => $appointment->id,
                'hospitalId' => $appointment->hospital_id,
                'hospitalName' => $hospital->name,
                'doctorId' => $appointment->doctor_id,
                'doctorName' => $doctor->name,
                'doctorSpecialty' => $doctor->specialty,
                'appointmentDate' => $appointment->appointment_date->toISOString(),
                'timeSlot' => $appointment->time_slot,
                'patientName' => $appointment->patient_name,
                'patientPhone' => $appointment->patient_phone,
                'problem' => $appointment->problem,
                'status' => $appointment->status,
                'amount' => $appointment->amount,
                'paymentMethod' => $appointment->payment_method,
                'paymentStatus' => $appointment->payment_status,
                'createdAt' => $appointment->created_at->toISOString(),
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/appointments",
     *     tags={"Appointments"},
     *     summary="Get user's appointments",
     *     description="Get all appointments for the authenticated user with optional filtering",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by appointment status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending","confirmed","cancelled","completed"})
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of appointments to return",
     *         required=false,
     *         @OA\Schema(type="integer", example="10")
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of appointments to skip",
     *         required=false,
     *         @OA\Schema(type="integer", example="0")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", example="apt_123"),
     *                     @OA\Property(property="hospitalId", type="string", example="h1"),
     *                     @OA\Property(property="hospitalName", type="string", example="Muhimbili National Hospital"),
     *                     @OA\Property(property="doctorId", type="string", example="d1"),
     *                     @OA\Property(property="doctorName", type="string", example="Dr. John Mwakalinga"),
     *                     @OA\Property(property="doctorSpecialty", type="string", example="General Medicine"),
     *                     @OA\Property(property="appointmentDate", type="string", format="date-time", example="2024-01-20T10:00:00Z"),
     *                     @OA\Property(property="timeSlot", type="string", example="10:00 AM"),
     *                     @OA\Property(property="patientName", type="string", example="John Doe"),
     *                     @OA\Property(property="patientPhone", type="string", example="+255700000000"),
     *                     @OA\Property(property="problem", type="string", example="Regular checkup"),
     *                     @OA\Property(property="status", type="string", example="confirmed"),
     *                     @OA\Property(property="amount", type="number", example=30500.0),
     *                     @OA\Property(property="paymentMethod", type="string", example="mpesa"),
     *                     @OA\Property(property="paymentStatus", type="string", example="pending"),
     *                     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *                 )
     *             ),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="total", type="integer", example=5),
     *                 @OA\Property(property="limit", type="integer", example=10),
     *                 @OA\Property(property="offset", type="integer", example=0),
     *                 @OA\Property(property="hasMore", type="boolean", example=false)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);

        $query = Appointment::with(['hospital', 'doctor'])
            ->where('user_id', auth()->id());

        if ($status) {
            $query->where('status', $status);
        }

        $total = $query->count();
        $appointments = $query->orderBy('appointment_date', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'hospitalId' => $appointment->hospital_id,
                    'hospitalName' => $appointment->hospital->name,
                    'doctorId' => $appointment->doctor_id,
                    'doctorName' => $appointment->doctor->name,
                    'doctorSpecialty' => $appointment->doctor->specialty,
                    'appointmentDate' => $appointment->appointment_date->toISOString(),
                    'timeSlot' => $appointment->time_slot,
                    'patientName' => $appointment->patient_name,
                    'patientPhone' => $appointment->patient_phone,
                    'problem' => $appointment->problem,
                    'status' => $appointment->status,
                    'amount' => $appointment->amount,
                    'paymentMethod' => $appointment->payment_method,
                    'paymentStatus' => $appointment->payment_status,
                    'createdAt' => $appointment->created_at->toISOString(),
                ];
            }),
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'hasMore' => ($offset + $limit) < $total
            ]
        ]);
    }

    /**
     * Convert time slot to 24-hour format
     */
    private function convertTimeSlot($timeSlot)
    {
        $timeMap = [
            '9:00 AM' => '09:00:00',
            '10:00 AM' => '10:00:00',
            '11:00 AM' => '11:00:00',
            '2:00 PM' => '14:00:00',
            '3:00 PM' => '15:00:00',
            '4:00 PM' => '16:00:00',
        ];

        return $timeMap[$timeSlot] ?? '10:00:00';
    }
}