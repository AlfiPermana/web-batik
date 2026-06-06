# Admin & User Login Guide

## Credentials (After Fresh Migration)

### Admin Account
```
Email: admin@batik.local
Password: password123
Role: admin
```

### Test Customer Account
```
Email: customer@example.com
Password: customer123
Role: customer
```

## How to Login

### Method 1: Laravel Fortify (Built-in)
The application uses **Laravel Fortify** for authentication.

1. Navigate to: `/login` (or `/register`)
2. Use credentials above
3. After login:
   - **Admin**: Can access admin dashboard at `/admin` or `/admin/dashboard`
   - **Customer**: Can access user dashboard at `/dashboard` or `/customer`

### Method 2: API Login (For Testing)
```bash
# Get CSRF token first
curl -c cookies.txt https://localhost:8000/

# Login via API
curl -b cookies.txt -X POST https://localhost:8000/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@batik.local",
    "password": "password123"
  }'
```

## Change Admin Credentials

### Option 1: Update .env variables (before seeding)

Edit `.env`:
```env
ADMIN_EMAIL=your-email@domain.com
ADMIN_PASSWORD=your-strong-password
ADMIN_NAME="Your Name"

# Optional: For test customer
CUSTOMER_EMAIL=customer@domain.com
CUSTOMER_PASSWORD=customer-password
CUSTOMER_NAME="Customer Name"
```

Then re-seed:
```bash
php artisan db:seed --class=AdminUserSeeder
```

### Option 2: Create user via Artisan

```bash
php artisan tinker
```

Then in tinker:
```php
$user = new App\Models\User();
$user->name = 'New Admin';
$user->email = 'newadmin@email.com';
$user->password = Hash::make('newpassword123');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();
```

### Option 3: Create user with SQL

```sql
INSERT INTO users (name, email, role, password, email_verified_at, created_at, updated_at) 
VALUES ('Admin Name', 'admin@email.com', 'admin', '$2y$12$...hash...', NOW(), NOW(), NOW());
```

For password hash, generate with:
```php
php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT, ['cost' => 12]);"
```

## Workshop Admin Panel Access

### After logging in as admin:

1. Navigate to workshop admin section (check routes)
2. Available endpoints:

**To create time slots:**
```
POST /api/admin/workshops/{workshopId}/time-slots
Body: {
  "name": "Slot 1: Pagi",
  "start_time": "10:00:00",
  "end_time": "13:00:00",
  "max_capacity": 20
}
```

**To add available dates:**
```
POST /api/admin/workshops/{workshopId}/available-dates
Body: {
  "date": "2025-12-25"
}
```

## Test Data Included

After seeding, database includes:

### Workshop
- Name: Batik Workshop 101
- Price: Rp 500,000
- Location: Jakarta

### Time Slots
- Slot 1: Pagi (10:00-13:00) - 20 capacity
- Slot 2: Sore (14:00-17:00) - 20 capacity

### Available Dates (Test)
- Today (2025-12-17)
- Tomorrow (2025-12-18)

### Schedules (Auto-generated)
- 4 schedules total (2 dates × 2 slots)
- All available for booking

## Reset/Create New Admin

To reset database with new credentials:

```bash
# Drop all tables and re-create
php artisan migrate:fresh

# Seed with custom admin (via .env variables)
ADMIN_EMAIL=your@email.com ADMIN_PASSWORD=strong_pass php artisan db:seed --class=AdminUserSeeder

# Also seed workshop test data
php artisan db:seed --class=WorkshopSlotSeeder
```

## Troubleshooting

### Login not working
- Check if users table has entries: `php check_admin_users.php`
- Verify role column has 'admin' for admin user
- Clear session cache: `php artisan cache:clear`

### Forgot password not working
- Check `.env` has correct email driver (MAIL_DRIVER)
- Check `config/fortify.php` has password reset enabled

### Can't access admin panel
- Check if user has role='admin' in database
- Verify AdminMiddleware exists in `app/Http/Middleware/AdminMiddleware.php`

## Database Users Check

```bash
php check_admin_users.php
```

This shows all users and their roles.
