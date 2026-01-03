# Mixlar Plugin Marketplace - PHP Edition

A full-featured plugin marketplace with user authentication, admin portal, and plugin management system. Built with PHP, MySQL, and vanilla JavaScript.

## 🚨 Login Not Working on Hostinger?

**→ See [QUICK_START.md](QUICK_START.md) for the 3-step fix!**

**Or use the web-based setup tool:** Just visit `https://your-domain.com/setup.php` in your browser

### Setup Guides
- **[QUICK_START.md](QUICK_START.md)** - Start here! 3 easy steps (5 minutes)
- **[HOW_TO_RUN_ON_HOSTINGER.md](HOW_TO_RUN_ON_HOSTINGER.md)** - Multiple setup methods
- **[FIX_LOGIN.md](FIX_LOGIN.md)** - Detailed troubleshooting
- **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Complete configuration guide

## Features

### 🔐 Authentication System
- User signup/login with JWT tokens
- Password reset with email verification
- Role-based access control (Admin/User)
- Secure password hashing with bcrypt

### 🏪 Marketplace
- Browse and search plugins
- Filter by category
- Real-time search
- Plugin details and downloads
- Download tracking
- Elgato-style modern UI

### 👑 Admin Portal
- Dashboard with statistics
- Approve/reject plugin submissions
- Feature plugins
- User management
- Role management
- Plugin and user deletion

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache or Nginx web server
- PHP extensions: mysqli, json, mbstring

### Quick Setup

**1. Create MySQL database:**
```sql
CREATE DATABASE mixlar_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**2. Import database schema:**
```bash
mysql -u your_user -p mixlar_marketplace < sql/schema.sql
```

**3. Configure `config/config.php`:**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'mixlar_marketplace');
define('JWT_SECRET', 'change_this_secret_key');
define('SITE_URL', 'http://yoursite.com');
```

**4. (Optional) Seed initial data:**
```bash
php sql/seed.php
```

**5. Access the site:**
- Marketplace: http://yoursite.com/frontend/public/
- Admin: http://yoursite.com/frontend/public/admin.html

### Default Admin Login
- Email: admin@mixlarlabs.com
- Password: admin123

⚠️ **Change immediately after first login!**

## Project Structure

```
/plugins
├── api/              # PHP API endpoints
├── config/           # Configuration
├── includes/         # PHP classes (Database, Auth, JWT, Email)
├── frontend/public/  # HTML, CSS, JS
├── sql/             # Database schema & seed
└── list.json        # Initial plugin data
```

## Running on Different PHP Servers

### XAMPP (Windows/Mac/Linux)
1. Copy folder to `htdocs/plugins/`
2. Start Apache and MySQL
3. Import `sql/schema.sql` via phpMyAdmin
4. Edit `config/config.php`
5. Access: http://localhost/plugins/frontend/public/

### WAMP (Windows)
1. Copy to `www/plugins/`
2. Same steps as XAMPP

### MAMP (Mac)
1. Copy to `htdocs/plugins/`
2. Same steps as XAMPP

### cPanel/Shared Hosting
1. Upload via FTP to `public_html/`
2. Create database via cPanel
3. Import schema via phpMyAdmin
4. Update `config/config.php` with cPanel database credentials

### Apache (Linux)
```bash
sudo cp -r plugins /var/www/html/
sudo chown -R www-data:www-data /var/www/html/plugins
# Import SQL, configure config.php
```

### Nginx + PHP-FPM
Add to nginx config:
```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    include fastcgi_params;
}
```

## API Endpoints

### Auth
- POST `/api/auth/signup.php`
- POST `/api/auth/login.php`
- POST `/api/auth/forgot-password.php`
- POST `/api/auth/reset-password.php`

### Plugins
- GET `/api/plugins/list.php`
- GET `/api/plugins/get.php?id=X`
- POST `/api/plugins/create.php` (auth required)

### Admin (admin only)
- GET `/api/admin/stats.php`
- GET `/api/admin/plugins.php`
- PUT `/api/admin/approve.php?id=X`
- PUT `/api/admin/reject.php?id=X`
- DELETE `/api/admin/delete-plugin.php?id=X`

See full API documentation in the detailed README sections above.

## Troubleshooting

**Database Connection Error:**
- Check credentials in `config/config.php`
- Verify MySQL is running
- Ensure database exists

**404 on API calls:**
- Verify `.htaccess` exists
- Enable mod_rewrite (Apache)
- Check file permissions

**Blank pages:**
- Enable error reporting in `config/config.php`
- Check PHP error logs

## License

Copyright © 2024 MixlarLabs. All rights reserved.
