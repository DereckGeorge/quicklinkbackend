1. Authentication & User Management
POST /api/auth/register
Purpose: User registration
// Request Body
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+255700000000",
  "password": "securePassword123",
  "dateOfBirth": "1990-05-15",
  "gender": "male",
  "address": "Mikocheni, Dar es Salaam",
  "emergencyContact": "Jane Doe",
  "emergencyContactPhone": "+255700000001",
  "medicalHistory": ["diabetes", "hypertension"],
  "allergies": ["penicillin"],
  "bloodGroup": "O+"
}

// Response
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "id": "user_123",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+255700000000",
    "createdAt": "2024-01-15T10:30:00Z"
  },
  "token": "jwt_token_here"
}

POST /api/auth/login
Purpose: User authentication
// Request Body
{
  "email": "john@example.com",
  "password": "securePassword123"
}

// Response
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": "user_123",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+255700000000"
  },
  "token": "jwt_token_here"
}
GET /api/user/profile
Purpose: Get user profile
// Response
{
  "success": true,
  "data": {
    "id": "user_123",
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+255700000000",
    "dateOfBirth": "1990-05-15",
    "gender": "male",
    "address": "Mikocheni, Dar es Salaam",
    "emergencyContact": "Jane Doe",
    "emergencyContactPhone": "+255700000001",
    "medicalHistory": ["diabetes", "hypertension"],
    "allergies": ["penicillin"],
    "bloodGroup": "O+",
    "profileImageUrl": "https://api.example.com/images/user_123.jpg",
    "createdAt": "2024-01-15T10:30:00Z"
  }
}
2. Hospital & Doctor Management
GET /api/hospitals
Purpose: Get nearby hospitals
// Query Parameters
?latitude=-6.8000&longitude=39.2847&radius=50&specialty=cardiology

// Response
{
  "success": true,
  "data": [
    {
      "id": "h1",
      "name": "Muhimbili National Hospital",
      "address": "United Nations Rd, Dar es Salaam",
      "latitude": -6.8000,
      "longitude": 39.2847,
      "distance": 2.5,
      "specialties": ["General", "Cardiology", "Ophthalmology", "Emergency"],
      "rating": 4.5,
      "phoneNumber": "+255-22-2151591",
      "hasEmergency": true,
      "imageUrl": "https://api.example.com/images/hospital_h1.jpg",
      "doctors": [
        {
          "id": "d1",
          "name": "Dr. John Mwakalinga",
          "specialty": "General Medicine",
          "qualification": "MBBS, MD",
          "experience": 10,
          "rating": 4.7,
          "imageUrl": "https://api.example.com/images/doctor_d1.jpg",
          "availableDays": ["Monday", "Tuesday", "Wednesday", "Friday"],
          "availableTime": "9:00 AM - 5:00 PM",
          "consultationFee": 30000.0,
          "bio": "Experienced general practitioner with 10 years of practice.",
          "languages": ["English", "Kiswahili"]
        }
      ]
    }
  ]
}
GET /api/hospitals/{hospitalId}/doctors
Purpose: Get doctors in a specific hospital
// Response
{
  "success": true,
  "data": [
    {
      "id": "d1",
      "name": "Dr. John Mwakalinga",
      "specialty": "General Medicine",
      "qualification": "MBBS, MD",
      "experience": 10,
      "rating": 4.7,
      "imageUrl": "https://api.example.com/images/doctor_d1.jpg",
      "availableDays": ["Monday", "Tuesday", "Wednesday", "Friday"],
      "availableTime": "9:00 AM - 5:00 PM",
      "consultationFee": 30000.0,
      "bio": "Experienced general practitioner with 10 years of practice.",
      "languages": ["English", "Kiswahili"]
    }
  ]
}
3. Appointment Management
GET /api/doctors/{doctorId}/availability
Purpose: Check doctor availability
// Query Parameters
?date=2024-01-20

// Response
{
  "success": true,
  "data": {
    "isAvailable": true,
    "availableTimeSlots": [
      "9:00 AM",
      "10:00 AM",
      "11:00 AM",
      "2:00 PM",
      "3:00 PM",
      "4:00 PM"
    ],
    "alternativeDates": [
      "2024-01-21",
      "2024-01-23",
      "2024-01-27"
    ]
  }
}
POST /api/appointments
Purpose: Book hospital appointment
// Request Body
{
  "hospitalId": "h1",
  "doctorId": "d1",
  "appointmentDate": "2024-01-20",
  "timeSlot": "10:00 AM",
  "patientName": "John Doe",
  "patientPhone": "+255700000000",
  "problem": "Regular checkup",
  "paymentMethod": "mpesa"
}

// Response
{
  "success": true,
  "message": "Appointment booked successfully",
  "data": {
    "id": "apt_123",
    "hospitalId": "h1",
    "hospitalName": "Muhimbili National Hospital",
    "doctorId": "d1",
    "doctorName": "Dr. John Mwakalinga",
    "doctorSpecialty": "General Medicine",
    "appointmentDate": "2024-01-20T10:00:00Z",
    "timeSlot": "10:00 AM",
    "patientName": "John Doe",
    "patientPhone": "+255700000000",
    "problem": "Regular checkup",
    "status": "confirmed",
    "amount": 30500.0,
    "paymentMethod": "mpesa",
    "paymentStatus": "pending",
    "createdAt": "2024-01-15T10:30:00Z"
  }
}
GET /api/appointments
Purpose: Get user's appointments
// Query Parameters
?status=pending&limit=10&offset=0

// Response
{
  "success": true,
  "data": [
    {
      "id": "apt_123",
      "hospitalId": "h1",
      "hospitalName": "Muhimbili National Hospital",
      "doctorId": "d1",
      "doctorName": "Dr. John Mwakalinga",
      "doctorSpecialty": "General Medicine",
      "appointmentDate": "2024-01-20T10:00:00Z",
      "timeSlot": "10:00 AM",
      "patientName": "John Doe",
      "patientPhone": "+255700000000",
      "problem": "Regular checkup",
      "status": "confirmed",
      "amount": 30500.0,
      "paymentMethod": "mpesa",
      "paymentStatus": "pending",
      "createdAt": "2024-01-15T10:30:00Z"
    }
  ],
  "pagination": {
    "total": 5,
    "limit": 10,
    "offset": 0,
    "hasMore": false
  }
}
4. Home Visit Services
GET /api/home-visits
Purpose: Get available home visit providers
// Query Parameters
?providerType=doctor&specialty=General Medicine&maxPrice=5000&latitude=-6.8000&longitude=39.2847&maxDistance=20

// Response
{
  "success": true,
  "data": [
    {
      "id": "hv1",
      "providerId": "doc1",
      "providerName": "Dr. Sarah Mwangi",
      "providerType": "doctor",
      "specialty": "General Medicine",
      "providerImageUrl": "https://api.example.com/images/provider_doc1.jpg",
      "rating": 4.8,
      "reviewCount": 127,
      "price": 2500.0,
      "currency": "TZS",
      "location": "Mikocheni, Dar es Salaam",
      "latitude": -6.8235,
      "longitude": 39.2695,
      "estimatedTravelTime": 25,
      "availableDays": ["monday", "tuesday", "wednesday", "thursday", "friday"],
      "availableTimeSlots": ["09:00", "10:00", "11:00", "14:00", "15:00", "16:00"],
      "isAvailable": true,
      "description": "Experienced general practitioner available for home visits.",
      "services": ["diagnosis", "prescription", "basic care", "elderly care"],
      "acceptsInsurance": true,
      "createdAt": "2024-01-15T10:30:00Z"
    }
  ]
}
POST /api/home-visits/book
Purpose: Book home visit
// Request Body
{
  "homeVisitId": "hv1",
  "patientId": "user_123",
  "patientName": "John Doe",
  "patientPhone": "+255700000000",
  "patientAddress": "Mikocheni, Dar es Salaam",
  "patientLatitude": -6.8235,
  "patientLongitude": 39.2695,
  "scheduledDate": "2024-01-20",
  "timeSlot": "10:00",
  "visitReason": "Regular checkup",
  "symptoms": "None"
}

// Response
{
  "success": true,
  "message": "Home visit booked successfully",
  "data": {
    "id": "hvb_123",
    "homeVisitId": "hv1",
    "providerId": "doc1",
    "providerName": "Dr. Sarah Mwangi",
    "providerType": "doctor",
    "patientId": "user_123",
    "patientName": "John Doe",
    "patientPhone": "+255700000000",
    "patientAddress": "Mikocheni, Dar es Salaam",
    "patientLatitude": -6.8235,
    "patientLongitude": 39.2695,
    "scheduledDate": "2024-01-20T10:00:00Z",
    "timeSlot": "10:00",
    "visitReason": "Regular checkup",
    "symptoms": "None",
    "amount": 2500.0,
    "currency": "TZS",
    "status": "pending",
    "paymentStatus": "pending",
    "createdAt": "2024-01-15T10:30:00Z"
  }
}
GET /api/home-visits/bookings
Purpose: Get user's home visit bookings
// Response
{
  "success": true,
  "data": [
    {
      "id": "hvb_123",
      "homeVisitId": "hv1",
      "providerId": "doc1",
      "providerName": "Dr. Sarah Mwangi",
      "providerType": "doctor",
      "patientId": "user_123",
      "patientName": "John Doe",
      "patientPhone": "+255700000000",
      "patientAddress": "Mikocheni, Dar es Salaam",
      "patientLatitude": -6.8235,
      "patientLongitude": 39.2695,
      "scheduledDate": "2024-01-20T10:00:00Z",
      "timeSlot": "10:00",
      "visitReason": "Regular checkup",
      "symptoms": "None",
      "amount": 2500.0,
      "currency": "TZS",
      "status": "confirmed",
      "paymentStatus": "paid",
      "notes": null,
      "actualVisitTime": null,
      "completedTime": null,
      "createdAt": "2024-01-15T10:30:00Z"
    }
  ]
}
6. Emergency Services
POST /api/emergency/request
Purpose: Request emergency services
// Request Body
{
  "patientId": "user_123",
  "patientName": "John Doe",
  "patientPhone": "+255700000000",
  "patientAddress": "Mikocheni, Dar es Salaam",
  "patientLatitude": -6.8235,
  "patientLongitude": 39.2695,
  "emergencyType": "medical",
  "description": "Severe chest pain",
  "severity": "high"
}

// Response
{
  "success": true,
  "message": "Emergency request submitted",
  "data": {
    "emergencyId": "emr_123",
    "status": "pending",
    "estimatedResponseTime": "15 minutes",
    "assignedHospital": "Muhimbili National Hospital",
    "createdAt": "2024-01-15T10:30:00Z"
  }
}

POST https://api.sandbox.pawapay.io/v2/deposits

    {
        "depositId": "afb57b93-7849-49aa-babb-4c3ccbfe3d79",
        "amount": "100",
        "currency": "RWF",
        "payer": {
            "type": "MMO",
            "accountDetails": {
                "phoneNumber": "250783456789",
                "provider": "MTN_MOMO_RWA"
            }
        }
    }
// Response

{
        "depositId": "afb57b93-7849-49aa-babb-4c3ccbfe3d79",
        "status": "ACCEPTED",
        "nextStep": "FINAL_STATUS",
        "created": "2025-05-15T07:38:56Z"
    }
// callback response

{
        "depositId": "afb57b93-7849-49aa-babb-4c3ccbfe3d79",
        "status": "COMPLETED",
        "amount": "100.00",
        "currency": "RWF",
        "country": "RWA",
        "payer": {
            "type": "MMO",
            "accountDetails": {
                "phoneNumber": "250783456789",
                "provider": "MTN_MOMO_RWA"
            }
        },
        "customerMessage": "DEMO",
        "created": "2025-05-15T07:38:56Z",
        "providerTransactionId": "df0e9405-fb17-42c2-a264-440c239f67ed"
    }

// Providers 
GET https://api.sandbox.pawapay.io/v2/active-conf?country=RWA&operationType=DEPOSIT

    {
        "companyName": "Demo",
        "countries": [
            {
                "country": "RWA",
                "prefix": "250",
                "flag": "https://static-content.pawapay.io/country_flags/rwa.svg",
                "displayName": {
                    "en": "Rwanda",
                    "fr": "Rwanda"
                },
                "providers": [
                    {
                        "provider": "AIRTEL_RWA",
                        "displayName": "Airtel",
                        "logo": "https://static-content.pawapay.io/company_logos/airtel.png",
                        "currencies": [
                            {
                                "currency": "RWF",
                                "displayName": "R₣",
                                "operationTypes": ...
                            }
                        ]
                    },
                    {
                        "provider": "MTN_MOMO_RWA",
                        "displayName": "MTN",
                        "logo": "https://static-content.pawapay.io/company_logos/mtn.png",
                        "currencies": [
                            {
                                "currency": "RWF",
                                "displayName": "R₣",
                                "operationTypes": ...
                            }
                        ]
                    }
                ]
            }
        ]
        ...
    }