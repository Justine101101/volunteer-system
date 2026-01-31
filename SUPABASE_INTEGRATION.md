# Supabase Integration Guide

This document explains how to connect your Laravel Volunteer Portal to Supabase and use the database query services.

## 🚀 Quick Setup

### 1. Get Your Supabase Credentials

1. Go to [Supabase Dashboard](https://app.supabase.com)
2. Create a new project or select existing one
3. Go to **Settings** → **API**
4. Copy your:
   - **Project URL** (e.g., `https://your-project.supabase.co`)
   - **Anon Key** (public key)
   - **Service Role Key** (secret key)

### 2. Update Environment Variables

Edit your `.env` file and replace the placeholder values:

```env
# Supabase Configuration
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-actual-anon-key-here
SUPABASE_SERVICE_ROLE_KEY=your-actual-service-role-key-here
SUPABASE_BUCKET_NAME=volunteer-portal
```

### 3. Set Up Database Schema

1. Go to your Supabase project dashboard
2. Navigate to **SQL Editor**
3. Copy and paste the contents of `database/supabase/schema.sql`
4. Click **Run** to execute the schema

### 4. Test the Connection

```bash
php artisan serve
```

Visit: `http://localhost:8000/api/test-connection`

## 📊 Database Query Services

### DatabaseQueryService

The `DatabaseQueryService` provides comprehensive database operations:

```php
use App\Services\DatabaseQueryService;

$queryService = app(DatabaseQueryService::class);

// Get events with pagination and filters
$events = $queryService->getEvents(
    page: 1,
    limit: 10,
    filters: ['status' => 'active', 'date_from' => '2024-01-01']
);

// Get dashboard statistics
$stats = $queryService->getDashboardStats();

// Search across tables
$results = $queryService->search('volunteer', ['events', 'users']);
```

### Available Methods

#### Events
- `getEvents($page, $limit, $filters)` - Get paginated events
- `getEventById($eventId)` - Get single event with details
- `createEvent($eventData)` - Create new event
- `updateEvent($eventId, $eventData)` - Update event
- `deleteEvent($eventId)` - Delete event

#### Users
- `getUsers($page, $limit, $filters)` - Get paginated users
- `getUserById($userId)` - Get single user

#### Event Registrations
- `getEventRegistrations($page, $limit, $filters)` - Get registrations
- `registerForEvent($userId, $eventId, $additionalData)` - Register for event
- `updateRegistrationStatus($registrationId, $status)` - Update status

#### Analytics & Search
- `getDashboardStats()` - Get dashboard statistics
- `getAnalytics($period)` - Get analytics data
- `search($query, $tables)` - Search across tables

## 🔌 API Endpoints

### Public Endpoints
- `GET /api/test-connection` - Test Supabase connection

### Protected Endpoints (require authentication)

#### Events
- `GET /api/events` - List events
- `POST /api/events` - Create event
- `GET /api/events/{id}` - Get event details
- `PUT /api/events/{id}` - Update event
- `DELETE /api/events/{id}` - Delete event

#### Users
- `GET /api/users` - List users
- `GET /api/users/{id}` - Get user details

#### Event Registrations
- `GET /api/registrations` - List registrations
- `POST /api/registrations/register` - Register for event
- `PUT /api/registrations/{id}/status` - Update registration status

#### Dashboard & Analytics
- `GET /api/dashboard/stats` - Get dashboard statistics
- `GET /api/analytics` - Get analytics data
- `GET /api/search` - Search across tables

#### File Upload
- `POST /api/upload` - Upload file to Supabase Storage

### Example API Usage

```javascript
// Get events
fetch('/api/events?page=1&limit=10&status=active', {
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data));

// Create event
fetch('/api/events', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json'
    },
        body: JSON.stringify({
            title: 'Volunteer Event',
            description: 'Help the community',
            event_date: '2024-12-01',
            event_time: '10:00:00',
            location: 'Community Center'
        })
})
.then(response => response.json())
.then(data => console.log(data));
```

## 🔄 Data Synchronization

### Sync Laravel Data to Supabase

```bash
# Sync all tables
php artisan supabase:sync

# Sync specific table
php artisan supabase:sync --table=events

# Force sync (overwrite existing data)
php artisan supabase:sync --force
```

### Available Tables for Sync
- `users` - User accounts
- `events` - Volunteer events
- `event_registrations` - Event registrations
- `contacts` - Contact form submissions
- `members` - Member information
- `settings` - Application settings

## 🗄️ Database Schema

### Tables Created

1. **users** - User accounts and authentication
2. **events** - Volunteer events and activities
3. **event_registrations** - User event registrations
4. **contacts** - Contact form submissions
5. **members** - Member profiles and information
6. **settings** - Application configuration

### Key Features

- **Row Level Security (RLS)** - Data access control
- **UUID Primary Keys** - Globally unique identifiers
- **Automatic Timestamps** - Created/updated tracking
- **Foreign Key Constraints** - Data integrity
- **Indexes** - Optimized query performance
- **Triggers** - Automatic updated_at timestamps

## 🔐 Security Features

### Row Level Security Policies

- **Users**: Can view/update own data, admins can view all
- **Events**: Public read access, admin-only modifications
- **Registrations**: Users can manage own registrations
- **Contacts**: Public creation, admin-only viewing
- **Members**: Authenticated users can view, admin-only modifications
- **Settings**: Authenticated users can view, admin-only modifications

### Authentication

The API uses Laravel Sanctum for authentication. Include the bearer token in requests:

```javascript
headers: {
    'Authorization': 'Bearer ' + token
}
```

## 📈 Analytics & Reporting

### Dashboard Statistics

```php
$stats = $queryService->getDashboardStats();
// Returns:
// - total_events
// - total_users
// - total_registrations
// - pending_registrations
// - recent_events
// - recent_registrations
```

### Analytics Data

```php
$analytics = $queryService->getAnalytics('30d');
// Returns:
// - registrations_over_time
// - popular_events
// - user_activity
```

## 🚨 Error Handling

All database operations include comprehensive error handling:

```php
try {
    $result = $queryService->getEvents();
    if (isset($result['error'])) {
        // Handle error
        Log::error($result['error']);
    }
} catch (\Exception $e) {
    Log::error('Database operation failed: ' . $e->getMessage());
}
```

## 🔧 Configuration

### Supabase Configuration

Edit `config/supabase.php`:

```php
return [
    'url' => env('SUPABASE_URL'),
    'anon_key' => env('SUPABASE_ANON_KEY'),
    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
    'bucket_name' => env('SUPABASE_BUCKET_NAME', 'volunteer-portal'),
];
```

### Service Provider

The `SupabaseServiceProvider` automatically registers the Supabase client when available.

## 📝 Usage Examples

### In Controllers

```php
use App\Services\DatabaseQueryService;

class EventController extends Controller
{
    public function __construct(
        private DatabaseQueryService $queryService
    ) {}

    public function index(Request $request)
    {
        $events = $this->queryService->getEvents(
            page: $request->get('page', 1),
            limit: $request->get('limit', 10),
            filters: $request->only(['status', 'date_from', 'date_to'])
        );

        return response()->json($events);
    }
}
```

### In Blade Templates

```php
@php
    $queryService = app(\App\Services\DatabaseQueryService::class);
    $stats = $queryService->getDashboardStats();
@endphp

<div class="stats">
    <div>Total Events: {{ $stats['total_events'] ?? 0 }}</div>
    <div>Total Users: {{ $stats['total_users'] ?? 0 }}</div>
</div>
```

## 🐛 Troubleshooting

### Common Issues

1. **Connection Failed**
   - Check your Supabase URL and keys
   - Verify network connectivity
   - Check Supabase project status

2. **Permission Denied**
   - Verify RLS policies are correctly set
   - Check user authentication
   - Ensure proper role assignments

3. **Data Sync Issues**
   - Check Laravel database connection
   - Verify Supabase schema is up to date
   - Review error logs

### Debug Commands

```bash
# Test connection
php artisan tinker
>>> app(\App\Services\DatabaseQueryService::class)->getDashboardStats()

# Check configuration
php artisan config:show supabase

# Clear caches
php artisan config:clear
php artisan cache:clear
```

## 📚 Additional Resources

- [Supabase Documentation](https://supabase.com/docs)
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

## 🤝 Support

For issues or questions:
1. Check the error logs in `storage/logs/laravel.log`
2. Verify your Supabase configuration
3. Test the connection using the API endpoint
4. Review the database schema in Supabase dashboard
