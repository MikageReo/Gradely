# Laravel Project Installation Guide

## 1. Introduction
This document provides a step-by-step guide to install and deploy the Laravel-based web application on a server environment. The guide is intended for system administrators, developers, or evaluators to assess the installability of the system.

---

## 2. System Requirements
Before installation, ensure the following requirements are met:

- PHP version 8.1 or higher
- Composer (Dependency Manager for PHP)
- MySQL / MariaDB database
- Web server (Apache or Nginx)
- cPanel, XAMPP, or equivalent hosting environment
- Internet connection (for dependency installation)

---

## 3. Project Deployment Steps

### 3.1 Upload Project Files
1. Upload the Laravel project files to the server using File Manager or FTP.
2. Extract the project folder.
3. Set the document root to the `/public` directory of the project.

---

## 4. Environment Configuration

### 4.1 Create Environment File
1. Copy the `.env.example` file and rename it to `.env`.
2. Configure the application settings as shown below:

```env
APP_NAME=YourProjectName
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://yourdomain.com
```

### 4.2 Generate Application Key
Run the following command:

```bash
php artisan key:generate
```

---

## 5. Database Configuration

### 5.1 Create Database
1. Create a new MySQL database using cPanel or phpMyAdmin.
2. Note the database name, username, and password.

### 5.2 Update Database Credentials in .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

---

## 6. Database Migration and Seeding

Run the following commands to create the database tables automatically:

```bash
php artisan migrate
php artisan db:seed
```

This ensures all required tables are generated without manual SQL queries.

---

## 7. Dependency Installation

Install all required Laravel dependencies using Composer:

```bash
composer install
```

---

## 8. Email Notification Configuration

The system supports email notifications using SMTP. Configure the email settings in the `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=example@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=example@gmail.com
MAIL_FROM_NAME="Your Project Name"
```

> Note: For Gmail, an App Password is required.

---

## 9. Storage and Permissions

Ensure proper permissions are set for the following directories:

```bash
storage/
bootstrap/cache/
```

These directories must be writable by the web server.

---

## 10. Final Verification

1. Access the system via the browser using the configured domain.
2. Confirm the homepage loads successfully.
3. Test user login and basic functionalities.
4. Verify email notifications are sent correctly.

---

## 11. Conclusion

The system can be installed by following a clear and structured installation process, including environment setup, database migration, and email configuration. This demonstrates a high level of installability for the Laravel-based web application.

