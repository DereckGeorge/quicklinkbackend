<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Emergency",
 *     description="Emergency services endpoints"
 * )
 */
class EmergencyController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/emergency/request",
     *     tags={"Emergency"},
     *     summary="Request emergency services",
     *     description="Create an emergency request",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patientName","patientPhone","patientAddress","patientLatitude","patientLongitude","emergencyType","description","severity"},
     *             @OA\Property(property="patientId", type="string", nullable=true, example="user_123"),
     *             @OA\Property(property="patientName", type="string", example="John Doe"),
     *             @OA\Property(property="patientPhone", type="string", example="+255700000000"),
     *             @OA\Property(property="patientAddress", type="string", example="Mikocheni, Dar es Salaam"),
     *             @OA\Property(property="patientLatitude", type="number", format="float", example=-6.8235),
     *             @OA\Property(property="patientLongitude", type="number", format="float", example=39.2695),
     *             @OA\Property(property="emergencyType", type="string", example="medical"),
     *             @OA\Property(property="description", type="string", example="Severe chest pain"),
     *             @OA\Property(property="severity", type="string", enum={"low","medium","high"}, example="high")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Emergency requested")
     * )
     */
    public function requestEmergency(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patientId' => 'nullable|string',
            'patientName' => 'required|string|max:255',
            'patientPhone' => 'required|string|max:20',
            'patientAddress' => 'required|string',
            'patientLatitude' => 'required|numeric',
            'patientLongitude' => 'required|numeric',
            'emergencyType' => 'required|string',
            'description' => 'required|string',
            'severity' => 'required|string|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignedHospital = 'Muhimbili National Hospital';
        $estimatedResponseTime = '15 minutes';

        $emergency = EmergencyRequest::create([
            'user_id' => auth()->id(),
            'patient_name' => $request->patientName,
            'patient_phone' => $request->patientPhone,
            'patient_address' => $request->patientAddress,
            'patient_latitude' => $request->patientLatitude,
            'patient_longitude' => $request->patientLongitude,
            'emergency_type' => $request->emergencyType,
            'description' => $request->description,
            'severity' => $request->severity,
            'status' => 'pending',
            'estimated_response_time' => $estimatedResponseTime,
            'assigned_hospital' => $assignedHospital,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Emergency request submitted',
            'data' => [
                'emergencyId' => $emergency->id,
                'status' => $emergency->status,
                'estimatedResponseTime' => $emergency->estimated_response_time,
                'assignedHospital' => $emergency->assigned_hospital,
                'createdAt' => $emergency->created_at->toISOString(),
            ]
        ], 201);
    }
}


