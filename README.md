# Lumen Attendance App

[![Lumen Version](https://img.shields.io/badge/Lumen-10.0.4-ff69b4.svg)](https://lumen.laravel.com/)
[![PHP Version](https://img.shields.io/badge/PHP-8.1+-777BB4.svg)](https://php.net/)
[![Composer Version](https://img.shields.io/badge/Composer-2.5+-885630.svg)](https://getcomposer.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

A robust Attendance Management System built with Lumen 10.0.4 (Laravel Components ^10.0). This application provides features for employee attendance tracking, user management, and reporting.

## 🚀 Features

- User authentication (JWT)
- Role-based access control (Employee, HR, Admin)
- Attendance tracking (Check-in/Check-out)
- User profile management
- Leave management
- Reports generation
- RESTful API endpoints

## 🛠️ Requirements

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

