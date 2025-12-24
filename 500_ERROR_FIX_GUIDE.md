# 500 Error Fix Guide - hr.dobs.cloud

## Current Issue
**Error:** `Attempt to read property "frontend_disable" on null`
**Cause:** Database tables exist but `front_details` table is empty
**Site:** https://hr.dobs.cloud

---

## Quick Fix (5 minutes)

### Step 1: SSH into cPanel
```bash
ssh dobsykjq@hr.dobs.cloud
# Or use cPanel Terminal
```

### Step 2: Navigate to Project
```bash
cd ~/hr.dobs.cloud
```

### Step 3: Seed Database
```bash
php artisan db:seed --class=FrontSeeder
php artisan db:seed --class=CountriesTableSeeder
```

**Expected Output:**
```
Database seeding completed successfully.
```

### Step 4: Set Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
php artisan storage:link
```

**Expected Output:**
```
The [public/storage] link has been connected to [storage/app/public].
```

### Step 5: Clear Cache & Optimize
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize
```

### Step 6: Verify Fix
```bash
php artisan tinker --execute="echo 'Front details: ' . DB::table('front_details')->count();"
```

**Expected:** `Front details: 1`

---

## Alternative: Fresh Migration (if seeding fails)

⚠️ **Warning:** This will ERASE all data and recreate tables!

```bash
cd ~/hr.dobs.cloud
php artisan migrate:fresh --force --seed
```

This runs:
- Drop all tables
- Recreate all 200+ migrations
- Seed with default data

---

## Troubleshooting

### Error: "Class 'FrontSeeder' not found"
**Fix:**
```bash
composer dump-autoload
php artisan db:seed --class=FrontSeeder
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"
**Check database credentials in `.env`:**
```bash
nano ~/hr.dobs.cloud/.env
```

Verify:
```env
DB_HOST=localhost
DB_DATABASE=dobsykjq_hrsystem
DB_USERNAME=dobsykjq_dms
DB_PASSWORD="9gj*X]MwPPy+"
```

Test connection:
```bash
php artisan tinker --execute="DB::connection()->getPdo();"
```

### Error: "permission denied"
**Fix permissions:**
```bash
chmod -R 775 ~/hr.dobs.cloud/storage
chmod -R 775 ~/hr.dobs.cloud/bootstrap/cache
chown -R dobsykjq:dobsykjq ~/hr.dobs.cloud
```

### Error: "specified key was too long"
**In `app/Providers/AppServiceProvider.php`:**
```php
use Illuminate\Support\Facades\Schema;

public function boot()
{
    Schema::defaultStringLength(191);
}
```

Then:
```bash
php artisan migrate:fresh --force --seed
```

---

## Verification Checklist

After fixing, verify these:

- [ ] **Database has data:**
  ```bash
  php artisan tinker --execute="DB::table('front_details')->first();"
  ```

- [ ] **Storage linked:**
  ```bash
  ls -la ~/hr.dobs.cloud/public/storage
  ```
  Should show: `storage -> ../../storage/app/public`

- [ ] **Permissions correct:**
  ```bash
  ls -ld ~/hr.dobs.cloud/storage
  ```
  Should show: `drwxrwxr-x`

- [ ] **Cache cleared:**
  ```bash
  ls ~/hr.dobs.cloud/bootstrap/cache/
  ```
  Should be empty or only have `.gitignore`

- [ ] **Site loads:**
  ```bash
  curl -I https://hr.dobs.cloud
  ```
  Should return: `HTTP/2 200` (not 500)

---

## Expected Result

After completing the fix:

✅ **Homepage loads** (https://hr.dobs.cloud)
✅ **No 500 errors** in browser
✅ **Installation wizard appears** (if fresh install)
✅ **Or login page** (if already installed)

---

## Next Steps After Fix

1. **Complete Installation Wizard:**
   - Visit https://hr.dobs.cloud
   - Follow setup prompts
   - Create admin account
   - Configure company settings

2. **Upload Your Branding:**
   ```bash
   # Upload ilabhr-logo.png
   scp public/img/ilabhr-logo.png dobsykjq@hr.dobs.cloud:~/hr.dobs.cloud/public/img/
   
   # Upload branded CSS
   scp public/css/main.css dobsykjq@hr.dobs.cloud:~/hr.dobs.cloud/public/css/
   scp public/saas/css/main.css dobsykjq@hr.dobs.cloud:~/hr.dobs.cloud/public/saas/css/
   ```

3. **Update Database Colors:**
   ```bash
   php artisan tinker
   DB::table('front_details')->update(['primary_color' => '#544088']);
   exit
   ```

4. **Enable SSL:**
   - cPanel → SSL/TLS Status
   - Run AutoSSL for hr.dobs.cloud
   - Force HTTPS in `.htaccess`

5. **Setup Email:**
   - cPanel → Email Accounts
   - Create: noreply@dobs.cloud
   - Update `.env` with SMTP details

---

## Current Configuration

**Domain:** hr.dobs.cloud
**Server:** premium139.web-hosting.com
**User:** dobsykjq
**Database:** dobsykjq_hrsystem
**DB User:** dobsykjq_dms
**Project Path:** /home/dobsykjq/hr.dobs.cloud/

**Brand Colors:**
- Primary: #544088 (Purple)
- Secondary: #e96920 (Orange)

---

## Support Commands

**View latest logs:**
```bash
tail -f ~/hr.dobs.cloud/storage/logs/laravel.log
```

**Check disk space:**
```bash
df -h
```

**Check PHP version:**
```bash
php -v
```

**List migrations:**
```bash
php artisan migrate:status
```

**Rollback if needed:**
```bash
php artisan migrate:rollback
```

---

Need help? Check error logs first:
```bash
tail -50 ~/hr.dobs.cloud/storage/logs/laravel.log
```
