# Attendance Management System API

A RESTful API built with Lumen for managing employee attendance with JWT authentication.

## Features

- User authentication with JWT
- Role-based access control (Admin, HR, Employee)
- Attendance check-in/check-out
- User management
- CORS support
- API versioning
- Comprehensive test coverage

## Requirements

- PHP >= 8.3
- Composer >= 2.8.6
- MySQL >= Ver 8.0.43-0ubuntu0.24.04.1
- Lumen (10.0.4) (Laravel Components ^10.0)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone git@github.com:abubakarchaudhary731/Lumen_attendance_app.git
   cd Lumen_attendance_app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure Database**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=attendance_app
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run Migrations & Seeders**
   ```bash
   php artisan migrate --seed
   ```

6. **Generate JWT Secret**
   ```bash
   php artisan jwt:secret
   ```

7. **Storage Link**
   ```bash
   php artisan storage:link
   ```

## 🔧 Configuration

### Environment Variables

| Key | Description | Default |
|-----|-------------|---------|
| `APP_ENV` | Application environment | `local` |
| `APP_DEBUG` | Debug mode | `true` |
| `APP_KEY` | Application key | |
| `DB_*` | Database configuration | |
| `JWT_SECRET` | JWT authentication secret | |
| `MAIL_*` | Email configuration | |

## 📦 Dependencies

### Backend (Composer)
- Laravel Lumen Framework ^10.0
- tymon/jwt-auth (for API authentication)
- php-open-source-saver/jwt-auth (JWT Auth for Lumen)
- flipbox/lumen-generator (Code generation)


## 🏃‍♂️ Running the Application

```bash
# Start the development server
php -S localhost:8000 -t public
```

## 🌟 Default Admin Account

After running the seeders, a default admin account will be created:

- **Email**: admin@admin.com
- **Password**: password

