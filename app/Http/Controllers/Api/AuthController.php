<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Authentication"},
     *     summary="User registration",
     *     description="Register a new user account. Supports patient (default) and doctor role. If role=doctor, doctor fields must be provided.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","email","phone","password","dateOfBirth","gender","address","emergencyContact","emergencyContactPhone","bloodGroup"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+255700000000"),
     *                 @OA\Property(property="password", type="string", format="password", example="securePassword123"),
     *                 @OA\Property(property="dateOfBirth", type="string", format="date", example="1990-05-15"),
     *                 @OA\Property(property="gender", type="string", enum={"male","female","other"}, example="male"),
     *                 @OA\Property(property="address", type="string", example="Mikocheni, Dar es Salaam"),
     *                 @OA\Property(property="emergencyContact", type="string", example="Jane Doe"),
     *                 @OA\Property(property="emergencyContactPhone", type="string", example="+255700000001"),
     *                 @OA\Property(property="medicalHistory", type="array", @OA\Items(type="string"), example={"diabetes","hypertension"}),
     *                 @OA\Property(property="allergies", type="array", @OA\Items(type="string"), example={"penicillin"}),
     *                 @OA\Property(property="bloodGroup", type="string", example="O+"),
     *                 @OA\Property(property="profileImage", type="string", format="binary", description="Profile image file (optional)"),
     *                 @OA\Property(property="role", type="string", enum={"patient","doctor"}, example="patient", description="Defaults to patient if omitted"),
     *                 @OA\Property(property="hospitalId", type="string", nullable=true, example="1", description="Required if role=doctor (or first hospital will be used)"),
     *                 @OA\Property(property="licenseNumber", type="string", nullable=true, example="TMC-123456", description="Required if role=doctor"),
     *                 @OA\Property(property="specialty", type="string", nullable=true, example="Cardiology", description="Required if role=doctor"),
     *                 @OA\Property(property="yearsOfExperience", type="integer", nullable=true, example=8, description="Required if role=doctor"),
     *                 @OA\Property(property="clinicName", type="string", nullable=true, example="Sunrise Clinic"),
     *                 @OA\Property(property="clinicAddress", type="string", nullable=true, example="123 Main St, Dar es Salaam"),
     *                 @OA\Property(property="bio", type="string", nullable=true, example="Cardiologist with a focus on preventive care")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="user_123"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+255700000000"),
     *                 @OA\Property(property="role", type="string", example="patient"),
     *                 @OA\Property(property="profileImageUrl", type="string", example="http://127.0.0.1:8000/storage/profile_images/user_123.jpg"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *             ),
     *             @OA\Property(property="token", type="string", example="jwt_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'emergencyContact' => 'required|string|max:255',
            'emergencyContactPhone' => 'required|string|max:20',
            'medicalHistory' => 'nullable|array',
            'allergies' => 'nullable|array',
            'bloodGroup' => 'required|string|max:10',
            'profileImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'nullable|in:patient,doctor',
            // doctor-specific fields (conditionally required)
            'hospitalId' => 'required_if:role,doctor|nullable|exists:hospitals,id',
            'licenseNumber' => 'required_if:role,doctor|nullable|string',
            'specialty' => 'required_if:role,doctor|nullable|string',
            'yearsOfExperience' => 'required_if:role,doctor|nullable|integer|min:0',
            'clinicName' => 'nullable|string',
            'clinicAddress' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle profile image upload
        $profileImageUrl = null;
        if ($request->hasFile('profileImage')) {
            $file = $request->file('profileImage');
            $directory = public_path('profile_images');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $baseName = str_replace(' ', '_', strtolower($request->name ?? 'user'));
            $filename = $baseName . '_profile_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $filename);
            $profileImageUrl = 'profile_images/' . $filename;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'date_of_birth' => $request->dateOfBirth,
            'gender' => $request->gender,
            'address' => $request->address,
            'emergency_contact' => $request->emergencyContact,
            'emergency_contact_phone' => $request->emergencyContactPhone,
            'medical_history' => $request->medicalHistory,
            'allergies' => $request->allergies,
            'blood_group' => $request->bloodGroup,
            'profile_image_url' => $profileImageUrl,
            'role' => $request->role ?? 'patient',
        ]);

        // If registering as doctor, create doctor profile and link
        if (($request->role ?? 'patient') === 'doctor') {
            $hospital = null;
            if ($request->filled('hospitalId')) {
                $hospital = Hospital::find($request->hospitalId);
            }
            if (!$hospital) {
                $hospital = Hospital::first();
            }

            $doctor = Doctor::create([
                'hospital_id' => optional($hospital)->id,
                'name' => 'Dr. ' . $user->name,
                'specialty' => $request->specialty,
                'qualification' => null,
                'license_number' => $request->licenseNumber,
                'experience' => $request->yearsOfExperience ?? 0,
                'rating' => 0,
                'image_url' => null,
                'available_days' => [],
                'available_time' => null,
                'consultation_fee' => 0,
                'bio' => $request->bio,
                'languages' => [],
                'clinic_name' => $request->clinicName,
                'clinic_address' => $request->clinicAddress,
            ]);

            $user->doctor_id = $doctor->id;
            $user->save();
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'profileImageUrl' => $user->profile_image_url,
                'createdAt' => $user->created_at->toISOString(),
            ],
            'token' => $token
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Authenticate user and return JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="securePassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="jwt_token_here"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="role", type="string", example="doctor"),
     *                 @OA\Property(property="id", type="string", example="doc_123"),
     *                 @OA\Property(property="name", type="string", example="Dr. Jane Doe"),
     *                 @OA\Property(property="email", type="string", example="doctor@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+255700000000"),
     *                 @OA\Property(property="licenseNumber", type="string", example="TMC-123456"),
     *                 @OA\Property(property="specialty", type="string", example="Cardiology"),
     *                 @OA\Property(property="yearsOfExperience", type="integer", example=8),
     *                 @OA\Property(property="clinicName", type="string", example="Sunrise Clinic"),
     *                 @OA\Property(property="clinicAddress", type="string", example="123 Main St, Dar es Salaam"),
     *                 @OA\Property(property="bio", type="string", example="Cardiologist with a focus on preventive care")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = auth()->user()->load('doctor');

        $data = [
            'role' => $user->role ?? 'patient',
            'id' => (string)$user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        if (($user->role ?? 'patient') === 'doctor' && $user->doctor) {
            $data = [
                'role' => 'doctor',
                'id' => 'doc_' . $user->doctor->id,
                'name' => 'Dr. ' . $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'licenseNumber' => $user->doctor->license_number,
                'specialty' => $user->doctor->specialty,
                'yearsOfExperience' => (int)$user->doctor->experience,
                'clinicName' => $user->doctor->clinic_name,
                'clinicAddress' => $user->doctor->clinic_address,
                'bio' => $user->doctor->bio,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $data,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/profile",
     *     tags={"Authentication"},
     *     summary="Get user profile",
     *     description="Get authenticated user's profile information",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="user_123"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+255700000000"),
     *                 @OA\Property(property="dateOfBirth", type="string", format="date", example="1990-05-15"),
     *                 @OA\Property(property="gender", type="string", example="male"),
     *                 @OA\Property(property="address", type="string", example="Mikocheni, Dar es Salaam"),
     *                 @OA\Property(property="emergencyContact", type="string", example="Jane Doe"),
     *                 @OA\Property(property="emergencyContactPhone", type="string", example="+255700000001"),
     *                 @OA\Property(property="medicalHistory", type="array", @OA\Items(type="string"), example={"diabetes","hypertension"}),
     *                 @OA\Property(property="allergies", type="array", @OA\Items(type="string"), example={"penicillin"}),
     *                 @OA\Property(property="bloodGroup", type="string", example="O+"),
     *                 @OA\Property(property="profileImageUrl", type="string", example="https://api.example.com/images/user_123.jpg"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function profile()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'dateOfBirth' => $user->date_of_birth->format('Y-m-d'),
                'gender' => $user->gender,
                'address' => $user->address,
                'emergencyContact' => $user->emergency_contact,
                'emergencyContactPhone' => $user->emergency_contact_phone,
                'medicalHistory' => $user->medical_history,
                'allergies' => $user->allergies,
                'bloodGroup' => $user->blood_group,
                'profileImageUrl' => $user->profile_image_url,
                'createdAt' => $user->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Authentication"},
     *     summary="User logout",
     *     description="Logout user and invalidate JWT token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}