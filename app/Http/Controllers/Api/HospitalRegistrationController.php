<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\HospitalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

/**
 * @OA\Tag(
 *     name="Hospital Registration",
 *     description="Hospital registration and management endpoints"
 * )
 */
class HospitalRegistrationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/hospitals",
     *     tags={"Hospital Registration"},
     *     summary="Create hospital registration",
     *     description="Register a new hospital with complete information including legal compliance, infrastructure, and personnel details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"hospital_name","is_polyclinic","physical_address","contact_details","ownership_type","date_operation_began","credentialing_contact","registration_legal_compliance","clinical_services_infrastructure","key_personnel_staffing"},
     *             @OA\Property(property="hospital_name", type="string", example="Test Hospital"),
     *             @OA\Property(property="is_polyclinic", type="boolean", example=false),
     *             @OA\Property(property="physical_address", type="object",
     *                 @OA\Property(property="street", type="string", example="123 Main Street"),
     *                 @OA\Property(property="area", type="string", example="City Center"),
     *                 @OA\Property(property="ward", type="string", example="Ward 1"),
     *                 @OA\Property(property="district", type="string", example="Dar es Salaam"),
     *                 @OA\Property(property="region", type="string", example="Dar es Salaam"),
     *                 @OA\Property(property="gps_coordinates", type="object",
     *                     @OA\Property(property="latitude", type="number", format="float", example=-6.7924),
     *                     @OA\Property(property="longitude", type="number", format="float", example=39.2083)
     *                 )
     *             ),
     *             @OA\Property(property="contact_details", type="object",
     *                 @OA\Property(property="main_phone", type="string", example="+255700000000"),
     *                 @OA\Property(property="official_email", type="string", format="email", example="test@hospital.com")
     *             ),
     *             @OA\Property(property="ownership_type", type="string", enum={"government_public","fbo","ngo","private_for_profit"}, example="private_for_profit"),
     *             @OA\Property(property="registration_legal_compliance", type="object",
     *                 @OA\Property(property="hfrs_number", type="string", example="HFRS-001"),
     *                 @OA\Property(property="operating_license", type="object",
     *                     @OA\Property(property="license_number", type="string", example="LIC-001"),
     *                     @OA\Property(property="expiration_date", type="string", format="date", example="2026-12-31")
     *                 ),
     *                 @OA\Property(property="business_registration", type="object",
     *                     @OA\Property(property="tin_certificate_number", type="string", example="TIN-001")
     *                 ),
     *                 @OA\Property(property="nhif_status", type="object",
     *                     @OA\Property(property="accepts_nhif", type="boolean", example=true),
     *                     @OA\Property(property="nhif_accreditation_number", type="string", example="NHIF-001")
     *                 )
     *             ),
     *             @OA\Property(property="clinical_services_infrastructure", type="object",
     *                 @OA\Property(property="level_of_facility", type="string", enum={"national_hospital","regional_referral_hospital","district_hospital","health_centre","dispensary","other"}, example="district_hospital"),
     *                 @OA\Property(property="services_offered", type="object",
     *                     @OA\Property(property="inpatient_services", type="boolean", example=true),
     *                     @OA\Property(property="outpatient_services", type="boolean", example=true),
     *                     @OA\Property(property="emergency_casualty_services", type="boolean", example=true),
     *                     @OA\Property(property="major_specialties", type="array", @OA\Items(type="string"), example={"Surgery","General Medicine"})
     *                 ),
     *                 @OA\Property(property="capacity", type="object",
     *                     @OA\Property(property="total_bed_capacity", type="integer", example=50)
     *                 )
     *             ),
     *             @OA\Property(property="key_personnel_staffing", type="object",
     *                 @OA\Property(property="head_of_facility", type="object",
     *                     @OA\Property(property="name", type="string", example="Dr. Jane Smith"),
     *                     @OA\Property(property="mct_registration_number", type="string", example="MCT-001")
     *                 ),
     *                 @OA\Property(property="nursing_officer_in_charge", type="object",
     *                     @OA\Property(property="name", type="string", example="Nurse Mary"),
     *                     @OA\Property(property="tnmc_registration_number", type="string", example="TNMC-001")
     *                 ),
     *                 @OA\Property(property="pharmacist_in_charge", type="object",
     *                     @OA\Property(property="name", type="string", example="Pharm John"),
     *                     @OA\Property(property="pharmacy_council_registration_number", type="string", example="PHARM-001")
     *                 ),
     *                 @OA\Property(property="total_staff_numbers", type="object",
     *                     @OA\Property(property="doctors", type="integer", example=10),
     *                     @OA\Property(property="nurses", type="integer", example=20)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hospital registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="hospital_id", type="string", example="4"),
     *             @OA\Property(property="hospital_name", type="string", example="Test Hospital"),
     *             @OA\Property(property="hfrs_number", type="string", example="HFRS-001"),
     *             @OA\Property(property="status", type="string", example="pending_verification"),
     *             @OA\Property(property="verification_status", type="string", example="pending"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="message", type="string", example="Hospital registration submitted successfully. Please upload required documents.")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=409, description="Hospital with HFRS number already exists")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hospital_name' => 'required|string|max:255',
            'is_polyclinic' => 'required|boolean',
            'physical_address.street' => 'required|string',
            'physical_address.area' => 'required|string',
            'physical_address.ward' => 'required|string',
            'physical_address.district' => 'required|string',
            'physical_address.region' => 'required|string',
            'physical_address.gps_coordinates.latitude' => 'required|numeric|between:-90,90',
            'physical_address.gps_coordinates.longitude' => 'required|numeric|between:-180,180',
            'contact_details.main_phone' => 'required|string|max:20',
            'contact_details.landline' => 'nullable|string|max:20',
            'contact_details.mobile' => 'nullable|string|max:20',
            'contact_details.official_email' => 'required|email|max:255',
            'contact_details.website' => 'nullable|url|max:255',
            'ownership_type' => 'required|in:government_public,fbo,ngo,private_for_profit',
            'date_operation_began.year' => 'required|integer|min:1900|max:' . date('Y'),
            'date_operation_began.month' => 'required|integer|min:1|max:12',
            'credentialing_contact.name' => 'required|string|max:255',
            'credentialing_contact.title' => 'required|string|max:255',
            'credentialing_contact.direct_phone' => 'required|string|max:20',
            'credentialing_contact.direct_email' => 'required|email|max:255',
            'registration_legal_compliance.hfrs_number' => 'required|string|unique:hospitals,hfrs_number',
            'registration_legal_compliance.operating_license.license_number' => 'required|string',
            'registration_legal_compliance.operating_license.expiration_date' => 'required|date|after:today',
            'registration_legal_compliance.business_registration.tin_certificate_number' => 'required|string',
            'registration_legal_compliance.nhif_status.accepts_nhif' => 'required|boolean',
            'registration_legal_compliance.nhif_status.nhif_accreditation_number' => 'required_if:registration_legal_compliance.nhif_status.accepts_nhif,true|nullable|string',
            'clinical_services_infrastructure.level_of_facility' => 'required|in:national_hospital,regional_referral_hospital,district_hospital,health_centre,dispensary,other',
            'clinical_services_infrastructure.services_offered.inpatient_services' => 'required|boolean',
            'clinical_services_infrastructure.services_offered.outpatient_services' => 'required|boolean',
            'clinical_services_infrastructure.services_offered.emergency_casualty_services' => 'required|boolean',
            'clinical_services_infrastructure.capacity.total_bed_capacity' => 'required|integer|min:0',
            'key_personnel_staffing.head_of_facility.name' => 'required|string|max:255',
            'key_personnel_staffing.head_of_facility.professional_title' => 'required|string|max:255',
            'key_personnel_staffing.head_of_facility.contact_phone' => 'required|string|max:20',
            'key_personnel_staffing.head_of_facility.contact_email' => 'required|email|max:255',
            'key_personnel_staffing.head_of_facility.mct_registration_number' => 'required|string|max:255',
            'key_personnel_staffing.nursing_officer_in_charge.name' => 'required|string|max:255',
            'key_personnel_staffing.nursing_officer_in_charge.professional_title' => 'required|string|max:255',
            'key_personnel_staffing.nursing_officer_in_charge.contact_phone' => 'required|string|max:20',
            'key_personnel_staffing.nursing_officer_in_charge.contact_email' => 'required|email|max:255',
            'key_personnel_staffing.nursing_officer_in_charge.tnmc_registration_number' => 'required|string|max:255',
            'key_personnel_staffing.pharmacist_in_charge.name' => 'required|string|max:255',
            'key_personnel_staffing.pharmacist_in_charge.professional_title' => 'required|string|max:255',
            'key_personnel_staffing.pharmacist_in_charge.contact_phone' => 'required|string|max:20',
            'key_personnel_staffing.pharmacist_in_charge.contact_email' => 'required|email|max:255',
            'key_personnel_staffing.pharmacist_in_charge.pharmacy_council_registration_number' => 'required|string|max:255',
            'key_personnel_staffing.total_staff_numbers.doctors' => 'required|integer|min:0',
            'key_personnel_staffing.total_staff_numbers.nurses' => 'required|integer|min:0',
            'key_personnel_staffing.total_staff_numbers.clinical_officers' => 'required|integer|min:0',
            'key_personnel_staffing.total_staff_numbers.allied_health_professionals' => 'required|integer|min:0',
            'key_personnel_staffing.total_staff_numbers.non_clinical_staff' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'validation_error',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()->all()
                ]
            ], 400);
        }

        // Check if HFRS number already exists
        if (Hospital::where('hfrs_number', $request->input('registration_legal_compliance.hfrs_number'))->exists()) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_exists',
                    'message' => 'Hospital with this HFRS number already exists'
                ]
            ], 409);
        }

        // Prepare data for storage
        $specialties = $request->input('clinical_services_infrastructure.services_offered.major_specialties', []);
        
        $hospitalData = [
            'name' => $request->hospital_name,
            'is_polyclinic' => $request->is_polyclinic,
            'address' => $request->input('physical_address.street') . ', ' . $request->input('physical_address.area'),
            'latitude' => $request->input('physical_address.gps_coordinates.latitude'),
            'longitude' => $request->input('physical_address.gps_coordinates.longitude'),
            'phone_number' => $request->input('contact_details.main_phone'),
            'specialties' => $specialties,
            'has_emergency' => $request->input('clinical_services_infrastructure.services_offered.emergency_casualty_services', false),
            'physical_address' => $request->physical_address,
            'contact_details' => $request->contact_details,
            'ownership_type' => $request->ownership_type,
            'affiliation' => $request->affiliation ?? [],
            'date_operation_began' => $request->date_operation_began,
            'credentialing_contact' => $request->credentialing_contact,
            'registration_legal_compliance' => $request->registration_legal_compliance,
            'clinical_services_infrastructure' => $request->clinical_services_infrastructure,
            'key_personnel_staffing' => $request->key_personnel_staffing,
            'hfrs_number' => $request->input('registration_legal_compliance.hfrs_number'),
            'verification_status' => 'pending',
        ];

        $hospital = Hospital::create($hospitalData);

        return response()->json([
            'hospital_id' => (string) $hospital->id,
            'hospital_name' => $hospital->name,
            'hfrs_number' => $hospital->hfrs_number,
            'status' => 'pending_verification',
            'verification_status' => $hospital->verification_status,
            'created_at' => $hospital->created_at->toISOString(),
            'message' => 'Hospital registration submitted successfully. Please upload required documents.'
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/hospitals/{hospitalId}/documents",
     *     tags={"Hospital Registration"},
     *     summary="Upload hospital documents",
     *     description="Upload required documents for hospital registration verification",
     *     @OA\Parameter(
     *         name="hospitalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="official_application_letter", type="string", format="binary"),
     *                 @OA\Property(property="operating_license", type="string", format="binary"),
     *                 @OA\Property(property="hfrs_registration_certificate", type="string", format="binary"),
     *                 @OA\Property(property="facility_photos", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Documents uploaded successfully"),
     *     @OA\Response(response=404, description="Hospital not found")
     * )
     */
    public function uploadDocuments(Request $request, $hospitalId)
    {
        $hospital = Hospital::find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'official_application_letter' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'operating_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'hfrs_registration_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'brela_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tin_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'organizational_structure' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'floor_plan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'facility_photos' => 'nullable|array|min:3|max:10',
            'facility_photos.*' => 'file|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'validation_error',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()->all()
                ]
            ], 400);
        }

        $uploadedDocuments = [];
        $directory = public_path('hospital_documents');
        
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Handle single file uploads
        $documentTypes = [
            'official_application_letter',
            'operating_license',
            'hfrs_registration_certificate',
            'brela_certificate',
            'tin_certificate',
            'organizational_structure',
            'floor_plan',
        ];

        foreach ($documentTypes as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $filename = $hospital->id . '_' . $type . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                $filePath = 'hospital_documents/' . $filename;

                HospitalDocument::updateOrCreate(
                    [
                        'hospital_id' => $hospital->id,
                        'document_type' => $type,
                        'file_name' => $filename,
                    ],
                    [
                        'file_path' => $filePath,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'status' => 'uploaded',
                    ]
                );

                $uploadedDocuments[] = [
                    'document_type' => $type,
                    'file_name' => $filename,
                    'file_size' => $file->getSize(),
                    'uploaded_at' => now()->toISOString(),
                    'status' => 'uploaded'
                ];
            }
        }

        // Handle facility photos (multiple files)
        if ($request->hasFile('facility_photos')) {
            foreach ($request->file('facility_photos') as $index => $file) {
                $filename = $hospital->id . '_facility_photo_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $file->move($directory, $filename);
                $filePath = 'hospital_documents/' . $filename;

                HospitalDocument::create([
                    'hospital_id' => $hospital->id,
                    'document_type' => 'facility_photo',
                    'file_name' => $filename,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'status' => 'uploaded',
                ]);

                $uploadedDocuments[] = [
                    'document_type' => 'facility_photo',
                    'file_name' => $filename,
                    'file_size' => $file->getSize(),
                    'uploaded_at' => now()->toISOString(),
                    'status' => 'uploaded'
                ];
            }
        }

        // Update hospital status if documents are uploaded
        if (count($uploadedDocuments) > 0 && $hospital->verification_status === 'pending') {
            $hospital->verification_status = 'documents_pending';
            $hospital->save();
        }

        // Get all required documents status
        $allDocuments = HospitalDocument::where('hospital_id', $hospital->id)->get();
        $documentsRequired = $this->getRequiredDocumentsStatus($hospital, $allDocuments);

        return response()->json([
            'hospital_id' => (string) $hospital->id,
            'documents_uploaded' => $uploadedDocuments,
            'documents_required' => $documentsRequired,
            'verification_status' => $hospital->verification_status
        ], 200);
    }

    /**
     * Get required documents status
     */
    private function getRequiredDocumentsStatus($hospital, $documents)
    {
        $required = [
            'official_application_letter',
            'operating_license',
            'hfrs_registration_certificate',
            'tin_certificate',
        ];

        if ($hospital->ownership_type !== 'government_public') {
            $required[] = 'brela_certificate';
        }

        $status = [];
        foreach ($required as $type) {
            $doc = $documents->where('document_type', $type)->first();
            $status[] = [
                'document_type' => $type,
                'status' => $doc ? 'uploaded' : 'pending',
                'required' => true
            ];
        }

        // Check facility photos (minimum 3)
        $photoCount = $documents->where('document_type', 'facility_photo')->count();
        $status[] = [
            'document_type' => 'facility_photos',
            'status' => $photoCount >= 3 ? 'uploaded' : 'pending',
            'required' => true,
            'count' => $photoCount,
            'minimum_required' => 3
        ];

        return $status;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hospitals/{hospitalId}",
     *     tags={"Hospital Registration"},
     *     summary="Get hospital details",
     *     description="Retrieve complete hospital registration details",
     *     @OA\Parameter(
     *         name="hospitalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Hospital details retrieved successfully"),
     *     @OA\Response(response=404, description="Hospital not found")
     * )
     */
    public function show($hospitalId)
    {
        $hospital = Hospital::with(['documents', 'verifier'])->find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        $documents = $hospital->documents->map(function ($doc) {
            return [
                'document_type' => $doc->document_type,
                'file_name' => $doc->file_name,
                'file_url' => url($doc->file_path),
                'uploaded_at' => $doc->created_at->toISOString(),
                'status' => $doc->status,
                'verified_at' => $doc->verified_at ? $doc->verified_at->toISOString() : null,
                'rejection_reason' => $doc->rejection_reason,
            ];
        });

        return response()->json([
            'hospital_id' => (string) $hospital->id,
            'hospital_name' => $hospital->name,
            'is_polyclinic' => $hospital->is_polyclinic,
            'physical_address' => $hospital->physical_address,
            'contact_details' => $hospital->contact_details,
            'ownership_type' => $hospital->ownership_type,
            'affiliation' => $hospital->affiliation,
            'date_operation_began' => $hospital->date_operation_began,
            'credentialing_contact' => $hospital->credentialing_contact,
            'registration_legal_compliance' => $hospital->registration_legal_compliance,
            'clinical_services_infrastructure' => $hospital->clinical_services_infrastructure,
            'key_personnel_staffing' => $hospital->key_personnel_staffing,
            'documents' => $documents,
            'verification_status' => $hospital->verification_status,
            'verified_at' => $hospital->verified_at ? $hospital->verified_at->toISOString() : null,
            'verified_by' => $hospital->verified_by ? (string) $hospital->verified_by : null,
            'rejection_reason' => $hospital->rejection_reason,
            'created_at' => $hospital->created_at->toISOString(),
            'updated_at' => $hospital->updated_at->toISOString(),
        ], 200);
    }

    /**
     * Update hospital registration
     */
    public function update(Request $request, $hospitalId)
    {
        $hospital = Hospital::find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        // Cannot update immutable fields
        $immutableFields = ['hfrs_number', 'id', 'created_at'];
        foreach ($immutableFields as $field) {
            if ($request->has($field)) {
                unset($request[$field]);
            }
        }

        // If verified, some fields require re-verification
        if ($hospital->verification_status === 'verified') {
            $sensitiveFields = ['ownership_type', 'registration_legal_compliance', 'key_personnel_staffing'];
            $hasSensitiveChanges = false;
            
            foreach ($sensitiveFields as $field) {
                if ($request->has($field)) {
                    $hasSensitiveChanges = true;
                    break;
                }
            }

            if ($hasSensitiveChanges) {
                $hospital->verification_status = 'pending';
            }
        }

        // Update allowed fields
        $updateableFields = [
            'name', 'is_polyclinic', 'physical_address', 'contact_details',
            'ownership_type', 'affiliation', 'date_operation_began',
            'credentialing_contact', 'registration_legal_compliance',
            'clinical_services_infrastructure', 'key_personnel_staffing'
        ];

        foreach ($updateableFields as $field) {
            if ($request->has($field)) {
                $hospital->$field = $request->input($field);
            }
        }

        // Update address and coordinates if physical_address changed
        if ($request->has('physical_address')) {
            $hospital->address = $request->input('physical_address.street') . ', ' . $request->input('physical_address.area');
            $hospital->latitude = $request->input('physical_address.gps_coordinates.latitude');
            $hospital->longitude = $request->input('physical_address.gps_coordinates.longitude');
        }

        // Update phone if contact_details changed
        if ($request->has('contact_details')) {
            $hospital->phone_number = $request->input('contact_details.main_phone');
        }

        $hospital->save();

        return response()->json([
            'hospital_id' => (string) $hospital->id,
            'hospital_name' => $hospital->name,
            'verification_status' => $hospital->verification_status,
            'message' => 'Hospital registration updated successfully'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/hospitals",
     *     tags={"Hospital Registration"},
     *     summary="List hospitals",
     *     description="List all hospitals with filtering, sorting, and pagination",
     *     @OA\Parameter(name="region", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="district", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="verification_status", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Hospitals retrieved successfully")
     * )
     */
    public function index(Request $request)
    {
        $query = Hospital::query();

        // Apply filters
        if ($request->has('region')) {
            $query->whereJsonContains('physical_address->region', $request->region);
        }

        if ($request->has('district')) {
            $query->whereJsonContains('physical_address->district', $request->district);
        }

        if ($request->has('ward')) {
            $query->whereJsonContains('physical_address->ward', $request->ward);
        }

        if ($request->has('ownership_type')) {
            $query->where('ownership_type', $request->ownership_type);
        }

        if ($request->has('level_of_facility')) {
            $query->whereJsonContains('clinical_services_infrastructure->level_of_facility', $request->level_of_facility);
        }

        if ($request->has('is_polyclinic')) {
            $query->where('is_polyclinic', $request->boolean('is_polyclinic'));
        }

        if ($request->has('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->has('accepts_nhif')) {
            $query->whereJsonContains('registration_legal_compliance->nhif_status->accepts_nhif', $request->boolean('accepts_nhif'));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('hfrs_number', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        $hospitals = $query->paginate($limit);

        return response()->json([
            'hospitals' => collect($hospitals->items())->map(function ($hospital) {
                return [
                    'hospital_id' => (string) $hospital->id,
                    'hospital_name' => $hospital->name,
                    'hfrs_number' => $hospital->hfrs_number,
                    'region' => $hospital->physical_address['region'] ?? null,
                    'district' => $hospital->physical_address['district'] ?? null,
                    'ownership_type' => $hospital->ownership_type,
                    'verification_status' => $hospital->verification_status,
                    'created_at' => $hospital->created_at->toISOString(),
                ];
            })->values(),
            'pagination' => [
                'page' => $hospitals->currentPage(),
                'limit' => $hospitals->perPage(),
                'total' => $hospitals->total(),
                'total_pages' => $hospitals->lastPage(),
            ]
        ], 200);
    }

    /**
     * Get specific document
     */
    public function getDocument($hospitalId, $documentType)
    {
        $hospital = Hospital::find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        // Handle facility_photos (plural) - return all facility photos
        if ($documentType === 'facility_photos') {
            $documents = HospitalDocument::where('hospital_id', $hospitalId)
                ->where('document_type', 'facility_photo')
                ->get();

            if ($documents->isEmpty()) {
                return response()->json([
                    'error' => [
                        'code' => 'document_not_found',
                        'message' => 'No facility photos found'
                    ]
                ], 404);
            }

            return response()->json([
                'document_type' => 'facility_photos',
                'photos' => $documents->map(function ($doc) {
                    return [
                        'file_name' => $doc->file_name,
                        'file_url' => url($doc->file_path),
                        'file_size' => $doc->file_size,
                        'uploaded_at' => $doc->created_at->toISOString(),
                        'status' => $doc->status,
                        'verified_at' => $doc->verified_at ? $doc->verified_at->toISOString() : null,
                    ];
                })
            ], 200);
        }

        // Handle single document types
        $document = HospitalDocument::where('hospital_id', $hospitalId)
            ->where('document_type', $documentType)
            ->first();

        if (!$document) {
            return response()->json([
                'error' => [
                    'code' => 'document_not_found',
                    'message' => 'Document not found'
                ]
            ], 404);
        }

        return response()->json([
            'document_type' => $document->document_type,
            'file_name' => $document->file_name,
            'file_url' => url($document->file_path),
            'file_size' => $document->file_size,
            'uploaded_at' => $document->created_at->toISOString(),
            'status' => $document->status,
            'verified_at' => $document->verified_at ? $document->verified_at->toISOString() : null,
        ], 200);
    }

    /**
     * List all documents for a hospital
     */
    public function listDocuments($hospitalId)
    {
        $hospital = Hospital::find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        $documents = HospitalDocument::where('hospital_id', $hospitalId)->get();
        $documentsRequired = $this->getRequiredDocumentsStatus($hospital, $documents);

        return response()->json([
            'hospital_id' => (string) $hospital->id,
            'documents' => $documents->map(function ($doc) {
                return [
                    'document_type' => $doc->document_type,
                    'file_name' => $doc->file_name,
                    'file_size' => $doc->file_size,
                    'uploaded_at' => $doc->created_at->toISOString(),
                    'status' => $doc->status,
                    'verified_at' => $doc->verified_at ? $doc->verified_at->toISOString() : null,
                ];
            }),
            'documents_required' => $documentsRequired,
        ], 200);
    }

    /**
     * Delete document
     */
    public function deleteDocument(Request $request, $hospitalId, $documentType)
    {
        $hospital = Hospital::find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        if (in_array($hospital->verification_status, ['verified', 'under_review'])) {
            return response()->json([
                'error' => [
                    'code' => 'verification_required',
                    'message' => 'Cannot delete document - hospital is verified or under review'
                ]
            ], 400);
        }

        // Handle facility_photos - delete all or specific one by filename
        if ($documentType === 'facility_photos') {
            $query = HospitalDocument::where('hospital_id', $hospitalId)
                ->where('document_type', 'facility_photo');

            // If filename provided, delete specific photo
            if ($request->has('file_name')) {
                $query->where('file_name', $request->file_name);
            }

            $documents = $query->get();

            if ($documents->isEmpty()) {
                return response()->json([
                    'error' => [
                        'code' => 'document_not_found',
                        'message' => 'Document not found'
                    ]
                ], 404);
            }

            // Delete files from filesystem
            foreach ($documents as $document) {
                $filePath = public_path($document->file_path);
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $document->delete();
            }

            return response()->json(null, 204);
        }

        // Handle single document types
        $document = HospitalDocument::where('hospital_id', $hospitalId)
            ->where('document_type', $documentType)
            ->first();

        if (!$document) {
            return response()->json([
                'error' => [
                    'code' => 'document_not_found',
                    'message' => 'Document not found'
                ]
            ], 404);
        }

        // Delete file from filesystem
        $filePath = public_path($document->file_path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $document->delete();

        return response()->json(null, 204);
    }

    /**
     * Verify hospital registration (Admin only)
     */
    public function verify(Request $request, $hospitalId)
    {
        // TODO: Add admin middleware check
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:verified,rejected',
            'notes' => 'required_if:status,rejected|nullable|string',
            'verified_by' => 'required|string',
            'document_feedback' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'validation_error',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()->all()
                ]
            ], 400);
        }

        $hospital = Hospital::find($hospitalId);
        
        if (!$hospital) {
            return response()->json([
                'error' => [
                    'code' => 'hospital_not_found',
                    'message' => 'Hospital not found'
                ]
            ], 404);
        }

        // Check required documents
        $requiredDocs = $this->getRequiredDocumentsStatus($hospital, $hospital->documents);
        $missingDocs = collect($requiredDocs)->where('status', 'pending')->count();
        
        if ($request->status === 'verified' && $missingDocs > 0) {
            return response()->json([
                'error' => [
                    'code' => 'missing_documents',
                    'message' => 'Cannot verify hospital - required documents are missing'
                ]
            ], 400);
        }

        $hospital->verification_status = $request->status === 'verified' ? 'verified' : 'rejected';
        $hospital->verified_at = now();
        $hospital->verified_by = $request->verified_by;
        
        if ($request->status === 'rejected') {
            $hospital->rejection_reason = $request->notes;
        }

        $hospital->save();

        // Update document statuses if feedback provided
        if ($request->has('document_feedback')) {
            foreach ($request->document_feedback as $feedback) {
                HospitalDocument::where('hospital_id', $hospitalId)
                    ->where('document_type', $feedback['document_type'])
                    ->update([
                        'status' => $feedback['status'],
                        'verified_at' => $feedback['status'] === 'verified' ? now() : null,
                        'verified_by' => $request->verified_by,
                        'rejection_reason' => $feedback['status'] === 'rejected' ? ($feedback['notes'] ?? null) : null,
                        'notes' => $feedback['notes'] ?? null,
                    ]);
            }
        }

        return response()->json([
            'hospital_id' => (string) $hospital->id,
            'hospital_name' => $hospital->name,
            'verification_status' => $hospital->verification_status,
            'verified_at' => $hospital->verified_at->toISOString(),
            'verified_by' => $hospital->verified_by,
            'message' => 'Hospital verification status updated successfully'
        ], 200);
    }

    /**
     * Get hospitals pending verification (Admin only)
     */
    public function pendingVerification(Request $request)
    {
        // TODO: Add admin middleware check
        
        $query = Hospital::whereIn('verification_status', ['pending', 'documents_pending', 'under_review']);

        if ($request->has('status')) {
            $query->where('verification_status', $request->status);
        }

        $limit = min($request->get('limit', 20), 100);
        $hospitals = $query->orderBy('created_at', 'desc')->paginate($limit);

        return response()->json([
            'hospitals' => $hospitals->items()->map(function ($hospital) {
                return [
                    'hospital_id' => (string) $hospital->id,
                    'hospital_name' => $hospital->name,
                    'hfrs_number' => $hospital->hfrs_number,
                    'verification_status' => $hospital->verification_status,
                    'created_at' => $hospital->created_at->toISOString(),
                ];
            }),
            'pagination' => [
                'page' => $hospitals->currentPage(),
                'limit' => $hospitals->perPage(),
                'total' => $hospitals->total(),
                'total_pages' => $hospitals->lastPage(),
            ]
        ], 200);
    }
}
