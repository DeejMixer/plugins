# How to Run Setup on Hostinger

## The Easy Way: Use Your Web Browser ✨

I've created a special web-based setup tool for you!

### Step 1: First, Import the Database Schema

1. **Log in to Hostinger Control Panel**
2. **Go to Databases → phpMyAdmin**
3. **Select your database:** `u677493242_plugins`
4. **Click the "Import" tab** at the top
5. **Click "Choose File"** and select `sql/schema.sql` from your computer
   - If you don't have the file locally, download it from GitHub first
6. **Click "Go"** to import
7. You should see: "Import has been successfully finished"

### Step 2: Update Database Password

1. **In Hostinger File Manager**, navigate to your website directory
2. **Open:** `config/config.php`
3. **Find line 5:** `define('DB_PASS', 'YOUR_PASSWORD_HERE');`
4. **Replace** `YOUR_PASSWORD_HERE` with your actual database password
   - Get it from: Hostinger → Databases → MySQL Databases
5. **Save the file**

### Step 3: Run the Setup Script

**Simply visit this URL in your browser:**
```
https://aqua-sparrow-114102.hostingersite.com/setup.php
```

That's it! The script will:
- ✓ Test database connection
- ✓ Create admin user
- ✓ Import all plugins
- ✓ Show you detailed progress

After it's done, you can delete the `setup.php` file for security (there's a button to do it).

---

## Alternative Method 1: SSH (If You Have Access)

If your Hostinger plan includes SSH access:

### 1. Enable SSH in Hostinger
- Hostinger Panel → Advanced → SSH Access
- Enable SSH and note your credentials

### 2. Connect via SSH
```bash
ssh u677493242@yourdomain.com
# Enter your password when prompted
```

### 3. Navigate to Your Website
```bash
cd domains/yourdomain.com/public_html
# or wherever your site files are located
```

### 4. Run the Seed Script
```bash
php sql/seed.php
```

### 5. You'll see output like:
```
✓ Database connected successfully
✓ Admin user created (ID: 1)
  Email: admin@mixlarlabs.com
  Password: admin123
✓ Imported 7 plugins
```

---

## Alternative Method 2: Cron Job (One-Time)

You can use Hostinger's Cron Job feature to run the script once:

### 1. Go to Hostinger Panel → Advanced → Cron Jobs

### 2. Create a new cron job:
- **Type:** Custom
- **Command:**
  ```
  php /home/u677493242/domains/yourdomain.com/public_html/sql/seed.php
  ```
  _(Replace path with your actual website path)_
- **Schedule:** Set to run once (e.g., in 1 minute from now)

### 3. Wait for it to run

### 4. Check the cron job logs to see if it succeeded

### 5. Delete the cron job after it runs

---

## Alternative Method 3: Create a Temporary Web Script

If you prefer, you can create a simple web page that runs the script:

### 1. Create a file called `run-setup.php` in your website root:

```php
<?php
// Simple wrapper to run setup
require_once 'sql/seed.php';
?>
```

### 2. Visit in browser:
```
https://aqua-sparrow-114102.hostingersite.com/run-setup.php
```

### 3. Delete the file after running

---

## Troubleshooting

### "Database connection failed"
- Check that you updated the password in `config/config.php`
- Verify database exists in Hostinger panel

### "Table doesn't exist"
- You need to import `sql/schema.sql` first via phpMyAdmin

### "Permission denied"
- Make sure the PHP files have proper permissions (644)
- Check that your database user has all privileges

---

## Recommended Approach

**For easiest setup, use Method 1 (Web Browser):**

1. Import `sql/schema.sql` via phpMyAdmin
2. Update `config/config.php` with database password
3. Visit `https://aqua-sparrow-114102.hostingersite.com/setup.php`
4. Follow the on-screen instructions
5. Delete `setup.php` when done

This is the safest and easiest method for Hostinger shared hosting! 🚀
