# Fix Login Issue - Step by Step

## The Problem

Your login is failing because the database password in `config/config.php` is set to `'YOUR_PASSWORD_HERE'` (a placeholder). The PHP backend cannot connect to your MySQL database.

## Solution for Hostinger Hosting

### Option 1: Edit Config File on Server (Recommended)

1. **Access your Hostinger File Manager:**
   - Log in to Hostinger Control Panel
   - Go to "Files" → "File Manager"
   - Navigate to your website's root directory

2. **Edit the config file:**
   - Find and open: `config/config.php`
   - Look for line 5: `define('DB_PASS', 'YOUR_PASSWORD_HERE');`

3. **Get your database password:**
   - In Hostinger panel, go to "Databases" → "MySQL Databases"
   - Find database: `u677493242_plugins`
   - Copy your database password (or reset it if you forgot)

4. **Update the config file:**
   ```php
   define('DB_PASS', 'your_actual_password');  // Replace with real password
   ```

5. **Save the file**

### Option 2: Use SSH (if available)

If you have SSH access:

```bash
# Connect to your server
ssh your_username@aqua-sparrow-114102.hostingersite.com

# Navigate to your website directory
cd public_html  # or wherever your site is located

# Edit config file
nano config/config.php

# Update DB_PASS line, then save (Ctrl+X, Y, Enter)
```

### Verify the Fix

After updating the password:

1. **Test database connection:**
   - Visit: `https://aqua-sparrow-114102.hostingersite.com/api/test-db.php`
   - You should see: "✓ Database connected successfully!"

2. **Create admin user (if needed):**
   - SSH into server and run: `php sql/seed.php`
   - Or use File Manager to run the script via browser

3. **Try logging in again:**
   - URL: `https://aqua-sparrow-114102.hostingersite.com/frontend/public/login.html`
   - Email: `admin@mixlarlabs.com`
   - Password: `admin123`

## Additional Configuration Needed

Also update these in `config/config.php`:

```php
// Email settings (for password reset to work)
define('SMTP_USER', 'noreply@yourdomain.com');  // Your actual email
define('SMTP_PASS', 'your-email-password');

// Site URL
define('SITE_URL', 'https://aqua-sparrow-114102.hostingersite.com');
```

## Check Error Logs

If login still fails after updating the password:

1. In Hostinger panel → "Advanced" → "Error Logs"
2. Look for recent PHP errors
3. The improved login.php now provides detailed logs:
   - Database connection status
   - User lookup results
   - Password verification results

## Common Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| "Server error. Please check if database is configured correctly." | Wrong DB password | Update config.php with correct password |
| "Invalid credentials" | User doesn't exist or wrong password | Run `sql/seed.php` to create admin user |
| "Email and password are required" | Frontend not sending data | Check browser console for JavaScript errors |

## Security Warning ⚠️

After successful login:
1. Immediately change the admin password from 'admin123'
2. Never commit `config/config.php` with real passwords to git
3. Keep database credentials secure

## Still Not Working?

Run the diagnostic script:
```bash
php api/test-db.php
```

This will tell you exactly what's wrong:
- Database connection status
- Admin user status
- Password hash verification

---

**Quick Summary:**
1. Edit `config/config.php` on your Hostinger server
2. Replace `'YOUR_PASSWORD_HERE'` with your actual MySQL password
3. Save file
4. Run `sql/seed.php` to create admin user
5. Try login again
