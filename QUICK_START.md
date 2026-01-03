# 🚀 Quick Start Guide - Hostinger Setup

## Fix Your Login Issue in 3 Simple Steps

Your login isn't working because the database isn't set up yet. Follow these steps:

---

## ⚡ Step 1: Import Database Tables (2 minutes)

1. **Open Hostinger Control Panel** → **Databases** → **phpMyAdmin**

2. **Select database:** `u677493242_plugins` (click it on the left sidebar)

3. **Click "Import" tab** at the top

4. **Click "Choose File"** button

5. **Select file:** `sql/schema-hostinger.sql`
   - Download it from your GitHub repo if needed
   - Or use the regular `sql/schema.sql` file (both work)

6. **Click "Go"** button at the bottom

7. **Wait for success message:** "Import has been successfully finished"

✅ **Tables created!**

---

## 🔧 Step 2: Update Database Password (1 minute)

1. **In Hostinger**, go to **Files** → **File Manager**

2. **Navigate to your website root directory**

3. **Find and edit:** `config/config.php`

4. **On line 5**, replace:
   ```php
   define('DB_PASS', 'YOUR_PASSWORD_HERE');
   ```

   With your actual password:
   ```php
   define('DB_PASS', 'your_real_database_password');
   ```

5. **Where to find your database password:**
   - Hostinger → **Databases** → **MySQL Databases**
   - Look for database `u677493242_plugins`
   - Copy the password (or reset it if you don't know it)

6. **Click "Save"**

✅ **Config updated!**

---

## 🎯 Step 3: Create Admin User (30 seconds)

**Just visit this URL in your browser:**

```
https://aqua-sparrow-114102.hostingersite.com/setup.php
```

The page will:
- Test your database connection ✓
- Create the admin user ✓
- Import all plugins ✓
- Show you the progress ✓

When it's done, **click the button to delete setup.php** (for security).

✅ **Setup complete!**

---

## 🔐 Now Login!

Visit: **https://aqua-sparrow-114102.hostingersite.com/frontend/public/login.html**

**Login credentials:**
- **Email:** `admin@mixlarlabs.com`
- **Password:** `admin123`

⚠️ **Important:** Change this password after your first login!

---

## ❌ Troubleshooting

### "Server error. Please check if database is configured correctly."
→ You didn't update the password in Step 2. Go back and fix it.

### "Table doesn't exist"
→ You skipped Step 1. Import the schema.sql file via phpMyAdmin.

### setup.php shows errors
→ Check that you completed both Step 1 and Step 2 correctly.

### Still stuck?
→ Run the diagnostic test: `https://aqua-sparrow-114102.hostingersite.com/api/test-db.php`
→ This will tell you exactly what's wrong.

---

## 📋 Summary Checklist

- [ ] Imported `sql/schema-hostinger.sql` via phpMyAdmin
- [ ] Updated `config/config.php` with real database password
- [ ] Visited `setup.php` to create admin and import plugins
- [ ] Deleted `setup.php` after successful setup
- [ ] Logged in successfully
- [ ] Changed admin password

---

**That's it! Your marketplace should be working now. 🎉**

If you have any issues, check `FIX_LOGIN.md` for more detailed troubleshooting.
