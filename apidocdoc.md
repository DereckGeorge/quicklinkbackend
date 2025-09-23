## Quick Clinic Documentation

### Doctor Model Structure

This documents the `Doctor` data model used in the app.

#### Fields

```text
id                : String        // Unique identifier
name              : String        // Doctor's full name
specialty         : String        // Primary specialty (e.g., Cardiology)
qualification     : String        // Qualifications/degree(s)
experience        : int           // Years of experience
rating            : double        // Average rating (e.g., 4.7)
imageUrl          : String        // URL to profile image
availableDays     : List<String>  // Days available (e.g., ["Mon","Tue"])
availableTime     : String        // Time window (e.g., "09:00 - 17:00")
consultationFee   : double        // Fee amount
bio               : String        // Short biography/summary
languages         : List<String>  // Languages spoken (e.g., ["English","Swahili"]) 
```

#### Dart Class (reference)

```dart
class Doctor {
  final String id;
  final String name;
  final String specialty;
  final String qualification;
  final int experience;
  final double rating;
  final String imageUrl;
  final List<String> availableDays;
  final String availableTime;
  final double consultationFee;
  final String bio;
  final List<String> languages;
}
```

#### Example JSON

```json
{
  "id": "doc_123",
  "name": "Dr. Jane Doe",
  "specialty": "Cardiology",
  "qualification": "MBChB, MMed (Cardiology)",
  "experience": 12,
  "rating": 4.8,
  "imageUrl": "https://example.com/images/jane.jpg",
  "availableDays": ["Mon", "Wed", "Fri"],
  "availableTime": "09:00 - 16:00",
  "consultationFee": 3500.0,
  "bio": "Cardiologist with a focus on preventive care and patient education.",
  "languages": ["English", "Swahili"]
}
```

Notes:
- experience is an integer (years).
- rating and consultationFee are doubles.
- availableDays and languages are arrays of strings.

---

### Appointment Model Structure

This documents the `Appointment` data model used in the app.

#### Fields

```text
id                : String         // Unique identifier
hospitalId        : String         // Hospital ID
hospitalName      : String         // Hospital display name
doctorId          : String         // Doctor ID
doctorName        : String         // Doctor display name
doctorSpecialty   : String         // Doctor specialty at booking time
appointmentDate   : DateTime       // ISO 8601 string in JSON
timeSlot          : String         // Selected time slot (e.g., "10:00 - 10:30")
patientName       : String         // Patient full name
patientPhone      : String         // Patient phone number
problem           : String         // Short description of the issue
status            : AppointmentStatus  // Booking status
amount            : double         // Total amount (fee + any charges)
paymentMethod     : PaymentMethod  // Chosen payment method
paymentStatus     : PaymentStatus  // Payment state
createdAt         : DateTime       // ISO 8601 string in JSON
```

#### Enums

```text
AppointmentStatus: pending | confirmed | completed | cancelled | rescheduled
PaymentMethod    : mpesa | card | cash | insurance
PaymentStatus    : pending | paid | failed | refunded
```

#### Dart Class (reference)

```dart
class Appointment {
  final String id;
  final String hospitalId;
  final String hospitalName;
  final String doctorId;
  final String doctorName;
  final String doctorSpecialty;
  final DateTime appointmentDate;
  final String timeSlot;
  final String patientName;
  final String patientPhone;
  final String problem;
  final AppointmentStatus status;
  final double amount;
  final PaymentMethod paymentMethod;
  final PaymentStatus paymentStatus;
  final DateTime createdAt;
}
```

#### Example JSON

```json
{
  "id": "apt_20250922_001",
  "hospitalId": "hosp_001",
  "hospitalName": "Quick Clinic Nairobi",
  "doctorId": "doc_123",
  "doctorName": "Dr. Jane Doe",
  "doctorSpecialty": "Cardiology",
  "appointmentDate": "2025-09-23T09:00:00.000Z",
  "timeSlot": "09:00 - 09:30",
  "patientName": "John Smith",
  "patientPhone": "+254712345678",
  "problem": "Chest discomfort and shortness of breath",
  "status": "confirmed",
  "amount": 4000.0,
  "paymentMethod": "mpesa",
  "paymentStatus": "pending",
  "createdAt": "2025-09-22T12:34:56.000Z"
}
```

Notes:
- DateTime values are serialized as ISO 8601 strings.
- Enum values use their lowercase string names in JSON.

---

### Home Visit Features - Quick Clinic App

## Overview
This document describes the home visit booking features that have been implemented in the Quick Clinic app, addressing the need for elderly care and home-based medical services.

## Features Implemented

### 1. Home Visit Provider Discovery
- Provider Types: Doctors and Nurses available for home visits
- Specialties: General Medicine, Pediatrics, Elderly Care, Home Care Nursing, Chronic Disease Management
- Location-based Search: Find providers near your location
- Availability Filtering: Filter by day of week and time slots
- Price Range Filtering: Set maximum budget for visits

### 2. Provider Information Display
- Profile Details: Name, specialty, rating, review count
- Service Offerings: List of available services (diagnosis, prescription, wound care, etc.)
- Location & Travel Time: Provider location and estimated travel time
- Pricing: Clear pricing in Kenyan Shillings (KES)
- Availability: Days and time slots when providers are available

### 3. Advanced Search & Filtering
- Text Search: Search by provider name, specialty, or location
- Provider Type Filter: Filter by doctor or nurse
- Specialty Filter: Filter by medical specialty
- Day Filter: Filter by available days (Monday-Sunday)
- Time Filter: Filter by available time slots (8:00 AM - 5:00 PM)
- Price Filter: Filter by maximum price range

### 4. Home Visit Booking System
- Date Selection: Choose from next 7 days
- Time Slot Selection: Select from provider's available times
- Visit Details: Specify reason for visit and symptoms
- Contact Information: Provide home address and phone number
- Booking Summary: Review all details before confirmation
- Real-time Validation: Form validation ensures complete information

### 5. User Experience Features
- Modern UI: Clean, intuitive interface with Material Design
- Responsive Design: Works on both mobile and desktop
- Location Services: Automatic location detection for nearby providers
- Provider Reviews: See ratings and reviews from other patients
- Service Descriptions: Detailed information about what each provider offers

## Technical Implementation

### Data Models
- HomeVisit: Provider information, services, pricing, availability
- HomeVisitBooking: Booking details, patient information, scheduling

### Services
- HomeVisitService: Manages provider data, search, filtering, and bookings
- LocationService: Handles user location and distance calculations

### Screens
- HomeVisitScreen: Main screen showing available providers with filters
- HomeVisitBookingScreen: Detailed booking form with date/time selection

## Use Cases Addressed

### 1. Elderly Care
- Home-bound Patients: Elderly patients who cannot travel to hospitals
- Chronic Disease Management: Regular checkups and medication management
- Basic Health Monitoring: Vital signs, wound care, health assessments

### 2. General Home Care
- Post-surgery Recovery: Follow-up care after hospital procedures
- Child Care: Pediatric home visits for sick children
- Convenience: Busy professionals who prefer home-based care

### 3. Emergency Situations
- Non-critical Care: Basic diagnosis and treatment at home
- Medication Administration: Help with prescribed medications
- Health Education: Guidance on health and wellness

## Pricing Structure
- Nurses: Starting from TZS 1,200 per visit
- Doctors: Starting from TZS 2,500 per visit
- Specialized Care: Up to TZS 3,000+ for specialized services
- Insurance: Some providers accept insurance coverage

## Future Enhancements

### Phase 2 Features
- Real-time Provider Tracking: See provider location during visit
- Video Consultations: Pre-visit video calls for assessment
- Prescription Delivery: Medication delivery to home
- Follow-up Scheduling: Automatic follow-up appointment booking

### Phase 3 Features
- Provider Verification: Enhanced background checks and verification
- Payment Integration: Direct payment processing
- Insurance Claims: Automated insurance claim processing
- Multi-language Support: Swahili and other local languages

## Integration Points

### Existing App Features
- User Authentication: Leverages existing user management
- Payment System: Integrates with current payment infrastructure
- Location Services: Uses existing location permission system
- Navigation: Seamlessly integrated into main app navigation

### External Services
- Maps Integration: Provider location and travel time calculations for Dar es Salaam area
- Push Notifications: Booking confirmations and reminders
- Analytics: Usage tracking and provider performance metrics

## Benefits

### For Patients
- Convenience: Medical care without leaving home
- Accessibility: Care for mobility-limited patients
- Time Savings: No travel time to hospitals
- Comfort: Care in familiar environment

### For Healthcare Providers
- Flexibility: Choose working hours and locations
- Patient Reach: Serve patients who can't visit hospitals
- Efficiency: Focused one-on-one care
- Income: Additional revenue stream

### For Healthcare System
- Reduced Hospital Load: Non-critical cases handled at home
- Better Patient Outcomes: Personalized care in comfortable setting
- Cost Efficiency: Reduced transportation and facility costs
- Community Health: Improved access to healthcare services

## Conclusion
The home visit features provide a comprehensive solution for home-based healthcare, addressing the specific needs mentioned in the requirements:
- "A place where you can book a doctor to come over or nurse where it shows a list of available doc or nurses in that area and their prices and when they will be available"
- "Unajuwa kama wale nurses ambao wanaweza kuja kuku angalia and sometimes kuna wale elderly ambao hawawezi kwenda hospital they need doctors to come over for a simple diagnosis"

The implementation focuses on user experience, comprehensive filtering, and seamless integration with the existing app infrastructure.

