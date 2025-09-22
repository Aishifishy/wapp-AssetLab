# 🔬 AssetLab

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-red?style=for-the-badge&logo=laravel" alt="Laravel 12.0">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue?style=for-the-badge&logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/TailwindCSS-3.1-38B2AC?style=for-the-badge&logo=tailwind-css" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License">
</p>

<p align="center">
  <strong>Integrated Laboratory & Equipment Management System</strong>
</p>

<p align="center">
  A comprehensive web application for educational institutions to manage laboratory resources, equipment tracking, and academic scheduling with automated workflows and real-time notifications.
</p>

---

## 📋 Table of Contents

- [✨ Features](#-features)
- [🛠️ Tech Stack](#️-tech-stack)
- [🚀 Getting Started](#-getting-started)
- [⚙️ Installation](#️-installation)
- [🔧 Configuration](#-configuration)
- [💾 Database Setup](#-database-setup)
- [📱 Usage](#-usage)
- [🏗️ Architecture](#️-architecture)
- [📧 Email System](#-email-system)
- [🔐 Security Features](#-security-features)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

## ✨ Features

### 👥 Multi-Role Management
- **Students**: Equipment borrowing, laboratory reservations
- **Faculty/Staff**: Advanced booking capabilities, extended access
- **Administrators**: Full system control, user management, analytics

### 📦 Equipment Management
- Real-time inventory tracking
- Barcode generation and scanning
- Equipment categorization and filtering
- Automated availability checking
- Damage reporting and maintenance logs

### 🏢 Laboratory Reservations
- **Computer laboratory management** (specific to computer labs)
- **Academic schedule integration** with class timetables
- **Conflict detection and prevention** with existing schedules
- **Capacity management** for computer lab resources
- **Schedule override capabilities** for special events

### 🔔 Smart Notifications
- **Email verification system** with session security
- **Real-time status updates** for requests and reservations
- **Admin notification system** for new requests
- **Welcome email workflows** sent after email verification
- **Automated approval notifications** with email templates

### 📊 Analytics & Reporting
- Equipment usage statistics
- Laboratory utilization reports
- User activity tracking
- PDF report generation
- Dashboard insights

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 12.0
- **Language**: PHP 8.2+
- **Database**: SQLite (configurable to MySQL/PostgreSQL)
- **Authentication**: Laravel Breeze with email verification
- **PDF Generation**: DomPDF
- **Barcode**: PHP Barcode Generator

### Frontend
- **CSS Framework**: TailwindCSS 3.1
- **JavaScript**: Alpine.js 3.4
- **Build Tool**: Vite 6.3
- **Icons**: Heroicons
- **Responsive Design**: Mobile-first approach

### Email & Communications
- **SMTP**: Gmail integration
- **Mailgun**: Production email service
- **Templates**: Blade-based email templates
- **Notifications**: Real-time email alerts

## 🚀 Getting Started

### Prerequisites

Ensure you have the following installed:

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (or MySQL/PostgreSQL)
- Git

### Quick Start

```bash
# Clone the repository
git clone https://github.com/Aishifishy/wapp-AssetLab.git
cd wapp-AssetLab

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Build assets
npm run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000` to access AssetLab.

## ⚙️ Installation

### 1. Clone and Install Dependencies

```bash
git clone https://github.com/Aishifishy/wapp-AssetLab.git
cd wapp-AssetLab
composer install
npm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
```

Edit the `.env` file with your configuration:

```env
APP_NAME=AssetLab
APP_ENV=local
APP_KEY=base64:your-app-key-here
APP_DEBUG=true
APP_TIMEZONE=Asia/Manila
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

## 🔧 Configuration

### Email Setup

For Gmail SMTP (recommended for development):

1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password
3. Use the App Password in your `.env` file

### File Storage

Configure storage for uploads:

```bash
php artisan storage:link
```

## 💾 Database Setup

### Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed with sample data (includes admin accounts and academic data)
php artisan db:seed
```

### Sample Data Includes:
- **Academic years and terms** (2023-2024 academic year with terms)
- **Computer laboratories** (3 sample labs: CL-101, CL-102, CL-201)
- **Laboratory schedules** (Sample CS courses with schedules)
- **Admin accounts** (Super admin, regular admin, specialized role admins)
- **User factories** (For generating test users via factories)

## 📱 Usage

### Default Accounts

After seeding, you can use these accounts:

**Super Administrator:**
- Email: `superadmin@assetlab.com`
- Password: `password123`

**Regular Administrator:**
- Email: `admin@resourease.com`
- Password: `password123`

> **Note:** Sample users are generated via factories. Check the SuperAdminController or create users through the admin interface.

### User Workflows

#### For Students:
1. **Register** → Email verification → Account activation
2. **Browse Equipment** → Submit borrow requests → Wait for approval
3. **Reserve Labs** → Select date/time → Submit for approval
4. **Track Requests** → View status → Receive notifications

#### For Administrators:
1. **Dashboard Overview** → Monitor system activity
2. **Manage Equipment** → Add/edit/categorize items
3. **Review Requests** → Approve/reject with notifications
4. **Generate Reports** → Export usage statistics

## 🏗️ Architecture

### Directory Structure

```
app/
├── Http/Controllers/
│   ├── Auth/           # Authentication controllers
│   ├── Admin/          # Admin-specific controllers
│   └── User/           # User-specific controllers
├── Models/             # Eloquent models
├── Services/           # Business logic services
├── Mail/               # Email notification classes
└── View/Components/    # Blade components

resources/
├── views/
│   ├── layouts/        # Base layouts
│   ├── auth/           # Authentication views
│   ├── admin/          # Admin interface
│   ├── ruser/          # User interface
│   └── emails/         # Email templates
└── js/                 # Frontend JavaScript
```

### Key Models

- `Ruser`: User accounts with roles (student, faculty, staff)
- `Radmin`: Administrator accounts with super admin capabilities
- `Equipment`: Equipment items and inventory tracking
- `EquipmentRequest`: Equipment borrowing requests
- `ComputerLaboratory`: Computer lab facilities (not generic Laboratory)
- `LaboratoryReservation`: Lab booking requests
- `LaboratorySchedule`: Academic class schedules for labs
- `AcademicYear/AcademicTerm`: Academic calendar management

## 📧 Email System

### Email Verification Flow
1. User registers → Verification email sent
2. User clicks verification link → Email confirmed
3. System sends welcome email → Account fully activated

### Notification Types
- Equipment request confirmations
- Laboratory reservation updates
- Admin notifications for new requests
- Status change alerts
- Welcome emails post-verification

### Email Templates
- Responsive design with university branding
- Consistent styling across all notifications
- Clear call-to-action buttons
- Professional layouts

## 🔐 Security Features

### Authentication & Authorization
- Email verification required for all users
- Role-based access control (RBAC)
- Session management with auto-expiry
- CSRF protection on all forms
- Secure password hashing

### Data Protection
- Input validation and sanitization
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- File upload validation
- Rate limiting on sensitive endpoints

### Session Security
- Auto-logout for unverified users
- Secure session configuration
- Token-based email verification
- Password reset with secure tokens

## 🤝 Contributing

We welcome contributions to AssetLab! Here's how you can help:

### Development Setup

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Run tests: `php artisan test`
5. Commit changes: `git commit -m 'Add amazing feature'`
6. Push to branch: `git push origin feature/amazing-feature`
7. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards
- Use meaningful commit messages
- Write tests for new features
- Update documentation as needed
- Follow Laravel best practices

### Areas for Contribution

- 🐛 Bug fixes and improvements
- ✨ New features and enhancements
- 📖 Documentation updates
- 🎨 UI/UX improvements
- 🧪 Test coverage expansion
- 🌐 Localization support

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

<p align="center">
  <strong>Developed for National University - Laguna</strong><br>
  <em>Integrated equipment tracking, laboratory reservations, and automated academic resource management</em>
</p>

<p align="center">
  <a href="mailto:itso@nu-laguna.edu.ph">📧 Contact Support</a> •
  <a href="#-contributing">🤝 Contribute</a> •
  <a href="#-getting-started">🚀 Get Started</a>
</p>
