# Cordillera Adivay Lions Club Volunteer Portal

A comprehensive volunteer management system built with Laravel 11, featuring role-based access control, event management, and community engagement tools.

## Features

### 🏠 **Public Pages**
- **Home Page**: Welcome banner, member carousel, and call-to-action
- **About Page**: Club history, mission & vision, and officer profiles
- **Events Page**: List of upcoming volunteer events
- **Contact Page**: Contact form with Google Maps integration

### 👥 **User Roles**
- **Superadmin**: Full access to manage events, approve registrations, and update content
- **Volunteer**: Can view events, register for activities, and manage profile

### 🎯 **Core Functionality**
- **Event Management**: Create, edit, and manage volunteer events
- **Event Registration**: Volunteers can join/leave events with approval workflow
- **Member Gallery**: View club members and their roles
- **Settings**: Profile management with dark mode toggle
- **Contact System**: Store and manage contact form submissions

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL database

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd volunteer-portal
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=volunteer_portal
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Accounts

After running the seeders, you'll have these default accounts:

### Superadmin
- **Email**: admin@cordilleraadivaylions.org
- **Password**: password

### Sample Volunteers
- **Email**: john@example.com | **Password**: password
- **Email**: jane@example.com | **Password**: password
- **Email**: mike@example.com | **Password**: password

## Usage

### For Superadmins
1. **Login** with superadmin credentials
2. **Create Events**: Navigate to Events → Create Event
3. **Manage Registrations**: View event details to approve/reject volunteer registrations
4. **Update Content**: Modify About page content and member information

### For Volunteers
1. **Register** for a new account or use sample credentials
2. **Browse Events**: View upcoming volunteer opportunities
3. **Join Events**: Click "Join Event" to register for activities
4. **Manage Profile**: Update settings, preferences, and personal information

## Database Schema

### Tables
- **users**: User accounts with roles and preferences
- **events**: Volunteer events and activities
- **event_registrations**: Volunteer event registrations with approval status
- **contacts**: Contact form submissions
- **members**: Club member information and roles

### Key Relationships
- Users can create multiple events
- Users can register for multiple events
- Events can have multiple registrations
- Each registration belongs to one user and one event

## Technology Stack

- **Backend**: Laravel 11 with PHP 8.2+
- **Frontend**: Blade templates with TailwindCSS
- **Authentication**: Laravel Breeze
- **Database**: MySQL with Eloquent ORM
- **Styling**: TailwindCSS with custom components
- **JavaScript**: Alpine.js for interactive elements

## Features in Detail

### Event Management
- Create events with title, description, date, time, and location
- Edit and delete events (superadmin only)
- View event details with registration status
- Approve/reject volunteer registrations

### User Management
- Role-based access control (superadmin/volunteer)
- Profile management with settings
- Dark mode preference storage
- Notification preferences

### Member Gallery
- Display club members with photos and roles
- Responsive grid layout
- Officer and member categorization

### Contact System
- Contact form with validation
- Message storage in database
- Contact information display
- Google Maps integration placeholder

## Customization

### Adding New Roles
1. Update the `role` enum in the users migration
2. Modify the `RoleMiddleware` to handle new roles
3. Update user model methods for role checking

### Styling
- Modify TailwindCSS classes in Blade templates
- Update the main layout in `resources/views/layouts/app.blade.php`
- Customize navigation in `resources/views/layouts/navigation.blade.php`

### Adding New Features
- Create new controllers and routes
- Add corresponding Blade templates
- Update navigation menu as needed

## Security Features

- CSRF protection on all forms
- Role-based middleware for access control
- Input validation and sanitization
- Password hashing with Laravel's built-in methods
- SQL injection prevention through Eloquent ORM

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please contact the development team or create an issue in the repository.

---

**Cordillera Adivay Lions Club** - Making a Difference Together