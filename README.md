# QuickLink Backend API

A comprehensive healthcare platform API built with Laravel 11, providing hospital management, appointment booking, home visit services, and emergency services.

## 🚀 Features

### Authentication & User Management
- User registration with medical history
- JWT-based authentication
- User profile management
- Medical information storage (allergies, medical history, blood group)

### Hospital & Doctor Services
- Hospital directory with location-based search
- Doctor availability checking
- Specialty-based filtering
- Distance calculation from user location

### Appointment Management
- Hospital appointment booking
- Appointment status tracking
- Payment integration support
- Appointment history

### Additional Services (Planned)
- Home visit services
- Emergency request handling
- Real-time notifications

## 🛠️ Tech Stack

- **Framework:** Laravel 11
- **Database:** MySQL
- **Authentication:** JWT (tymon/jwt-auth)
- **API Documentation:** Swagger/OpenAPI (l5-swagger)
- **PHP Version:** 8.3+

## 📋 Prerequisites

- PHP 8.3 or higher
- Composer
- MySQL
- Node.js (for frontend assets)

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/DereckGeorge/quicklinkbackend.git
   cd quicklinkbackend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   - Update your `.env` file with database credentials
   - Create the database in MySQL

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Generate API documentation**
   ```bash
   php artisan l5-swagger:generate
   ```

7. **Start the development server**
   ```bash
   php artisan serve --port=8001
   ```

## 📚 API Documentation

Once the server is running, access the interactive API documentation at:
- **Swagger UI:** `http://127.0.0.1:8001/api/documentation`
- **JSON API Docs:** `http://127.0.0.1:8001/docs/api-docs.json`

## 🔐 Authentication

The API uses JWT (JSON Web Tokens) for authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/user/profile` - Get user profile
- `POST /api/auth/logout` - User logout

### Hospitals & Doctors
- `GET /api/hospitals` - Get nearby hospitals
- `GET /api/hospitals/{id}/doctors` - Get doctors in a hospital
- `GET /api/doctors/{id}/availability` - Check doctor availability

### Appointments
- `POST /api/appointments` - Book an appointment
- `GET /api/appointments` - Get user appointments

## 🗄️ Database Schema

The application includes the following main entities:
- **Users** - Patient information and medical history
- **Hospitals** - Hospital details and specialties
- **Doctors** - Doctor information and availability
- **Appointments** - Appointment bookings and status
- **Home Visits** - Home visit service providers
- **Home Visit Bookings** - Home visit appointments
- **Emergency Requests** - Emergency service requests

## 🧪 Testing

Test the API endpoints using the provided Swagger documentation or with tools like Postman/Insomnia.

### Sample Test Data
The application comes with sample data including:
- 3 hospitals in Dar es Salaam
- 5 doctors across different specialties
- Sample user for testing

## 📝 API Response Format

All API responses follow a consistent format:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

## 🔒 Security

- JWT token authentication
- Input validation and sanitization
- CORS configuration
- Rate limiting (configurable)

## 📄 License

This project is licensed under the MIT License.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📞 Support

For support, email support@quicklink.com or create an issue in the repository.

## 🚀 Deployment

For production deployment:
1. Update environment variables
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Set up proper web server configuration (Apache/Nginx)
6. Configure SSL certificates
7. Set up database backups