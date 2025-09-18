<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
     *     description="Register a new user account",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","phone","password","dateOfBirth","gender","address","emergencyContact","emergencyContactPhone","bloodGroup"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="+255700000000"),
     *             @OA\Property(property="password", type="string", format="password", example="securePassword123"),
     *             @OA\Property(property="dateOfBirth", type="string", format="date", example="1990-05-15"),
     *             @OA\Property(property="gender", type="string", enum={"male","female","other"}, example="male"),
     *             @OA\Property(property="address", type="string", example="Mikocheni, Dar es Salaam"),
     *             @OA\Property(property="emergencyContact", type="string", example="Jane Doe"),
     *             @OA\Property(property="emergencyContactPhone", type="string", example="+255700000001"),
     *             @OA\Property(property="medicalHistory", type="array", @OA\Items(type="string"), example={"diabetes","hypertension"}),
     *             @OA\Property(property="allergies", type="array", @OA\Items(type="string"), example={"penicillin"}),
     *             @OA\Property(property="bloodGroup", type="string", example="O+")
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
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
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
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
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="user_123"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+255700000000")
     *             ),
     *             @OA\Property(property="token", type="string", example="jwt_token_here")
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

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'token' => $token
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