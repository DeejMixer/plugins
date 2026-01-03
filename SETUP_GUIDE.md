# Mixlar Marketplace - Setup Guide

## Fix Login Issues

If you're experiencing login issues with the admin account, follow these steps:

### Step 1: Configure Database Connection

Edit `config/config.php` and replace the placeholder values:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u677493242_plugins');
define('DB_PASS', 'YOUR_ACTUAL_DATABASE_PASSWORD');  // ⚠️ REPLACE THIS
define('DB_NAME', 'u677493242_plugins');
```

**Important:** Get your actual database password from:
- Hostinger Control Panel → Databases → MySQL Databases
- Or from your hosting provider's database management panel

### Step 2: Test Database Connection

Run the test script to verify your database configuration:

```bash
php api/test-db.php
```

This will show you:
- ✓ If database connection works
- ✓ If admin user exists
- ✓ If password hash is correct

### Step 3: Create Admin User (if needed)

If the admin user doesn't exist or password is incorrect:

```bash
php sql/seed.php
```

This will:
- Create the admin user with email: `admin@mixlarlabs.com`
- Set password to: `admin123`
- Import all plugins from `list.json`

### Step 4: Check Error Logs

If login still fails, check the PHP error logs:

**On Hostinger:**
- Go to hosting control panel
- Look for "Error Logs" or "PHP Error Logs"
- Check recent entries for login errors

**The improved login.php now logs:**
- Database connection status
- User lookup results
- Password verification results
- Detailed error messages

### Step 5: Verify Login

Try logging in again:
- **URL:** `https://aqua-sparrow-114102.hostingersite.com/frontend/public/login.html`
- **Email:** `admin@mixlarlabs.com`
- **Password:** `admin123`

## Common Issues

### "Server error. Please check if database is configured correctly."
- Database password in `config/config.php` is wrong
- Database doesn't exist
- Database user doesn't have proper permissions

### "Invalid credentials"
Could mean:
1. Email doesn't exist in database (run `sql/seed.php`)
2. Password is incorrect
3. Password hash in database is corrupted

### "Email and password are required"
- Frontend is not sending data properly
- Check browser console (F12) for JavaScript errors

## Security Notes

⚠️ **After successful login:**
1. Change the admin password immediately
2. Update `config/config.php` with strong passwords
3. Never commit `config/config.php` to git (it should be in `.gitignore`)

## Need More Help?

Run the diagnostic script:
```bash
php api/test-db.php
```

This will identify the exact issue and guide you to the solution.
