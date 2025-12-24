# iLab HR Deployment - Complete Command Reference

## 🚀 Essential Commands for hr.dobs.cloud

---

## Database Management

### Seed Database (Fix 500 Error)
```bash
cd ~/hr.dobs.cloud
php artisan db:seed --class=FrontSeeder
php artisan db:seed --class=CountriesTableSeeder
```

### Fresh Install (Nuclear Option)
```bash
php artisan migrate:fresh --force --seed
```

### Check Database Status
```bash
php artisan migrate:status
php artisan tinker --execute="DB::table('front_details')->count();"
```

### Rollback Last Migration
```bash
php artisan migrate:rollback --step=1
```

---

## Cache Management

### Clear All Caches
```bash
php artisan optimize:clear
```

### Individual Cache Clears
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild Caches (Production)
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## File Permissions

### Set Correct Permissions
```bash
chmod -R 775 ~/hr.dobs.cloud/storage
chmod -R 775 ~/hr.dobs.cloud/bootstrap/cache
chmod -R 755 ~/hr.dobs.cloud/public
```

### Fix Ownership
```bash
chown -R dobsykjq:dobsykjq ~/hr.dobs.cloud
```

### Create Storage Link
```bash
cd ~/hr.dobs.cloud
php artisan storage:link
```

### Verify Symlink
```bash
ls -la ~/hr.dobs.cloud/public/storage
```

---

## Git Deployment

### Pull Latest Changes
```bash
cd ~/hr.dobs.cloud
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

### Check Git Status
```bash
cd ~/hr.dobs.cloud
git status
git log --oneline -5
```

### Force Reset (Discard Local Changes)
```bash
git fetch origin
git reset --hard origin/main
```

---

## Debugging & Logs

### View Latest Errors
```bash
tail -50 ~/hr.dobs.cloud/storage/logs/laravel.log
```

### Real-Time Log Monitoring
```bash
tail -f ~/hr.dobs.cloud/storage/logs/laravel.log
```

### Clear Old Logs
```bash
echo "" > ~/hr.dobs.cloud/storage/logs/laravel.log
```

### Check PHP Errors
```bash
tail -50 ~/hr.dobs.cloud/storage/logs/php_errors.log
```

---

## System Information

### Check PHP Version
```bash
php -v
```

### Check PHP Modules
```bash
php -m | grep -E "(pdo|mysql|mbstring|openssl)"
```

### Check Disk Space
```bash
df -h
du -sh ~/hr.dobs.cloud
```

### Check Memory Usage
```bash
free -m
```

---

## Database Queries

### Enter Tinker (Interactive)
```bash
cd ~/hr.dobs.cloud
php artisan tinker
```

**Inside Tinker:**
```php
// Check front_details
DB::table('front_details')->first();

// Update primary color
DB::table('front_details')->update(['primary_color' => '#544088']);

// Count users
DB::table('users')->count();

// Exit
exit
```

### One-Line Database Queries
```bash
php artisan tinker --execute="DB::table('front_details')->first();"
php artisan tinker --execute="DB::table('users')->count();"
```

---

## Site Maintenance

### Put Site in Maintenance Mode
```bash
php artisan down --message="Upgrading database" --retry=60
```

### Bring Site Back Up
```bash
php artisan up
```

### Generate App Key (if missing)
```bash
php artisan key:generate
```

---

## Composer Operations

### Install Dependencies (Production)
```bash
cd ~/hr.dobs.cloud
composer install --no-dev --optimize-autoloader
```

### Update Dependencies
```bash
composer update --no-dev
```

### Rebuild Autoloader
```bash
composer dump-autoload -o
```

---

## Testing & Verification

### Test Database Connection
```bash
php artisan tinker --execute="DB::connection()->getPdo();"
```

### Check Site Status (CLI)
```bash
curl -I https://hr.dobs.cloud
```

### Test Email Configuration
```bash
php artisan tinker
Mail::raw('Test', function($msg) { 
    $msg->to('test@example.com')->subject('Test'); 
});
exit
```

---

## Quick Fixes

### Fix "Storage Not Found"
```bash
cd ~/hr.dobs.cloud
php artisan storage:link
chmod -R 775 storage
```

### Fix "Config Cached"
```bash
php artisan config:clear
php artisan cache:clear
```

### Fix "Route List Not Working"
```bash
php artisan route:clear
php artisan route:list
```

### Fix "Class Not Found"
```bash
composer dump-autoload
php artisan optimize:clear
```

---

## Backup Commands

### Backup Database (Manual)
```bash
mysqldump -u dobsykjq_dms -p'9gj*X]MwPPy+' dobsykjq_hrsystem > backup_$(date +%Y%m%d).sql
```

### Backup Files
```bash
cd ~
tar -czf hr_backup_$(date +%Y%m%d).tar.gz hr.dobs.cloud/
```

### Download Backup (to local)
```bash
# On Windows (PowerShell)
scp dobsykjq@hr.dobs.cloud:~/backup_20251224.sql C:\Backups\
```

---

## Emergency Recovery

### If Site is Completely Broken
```bash
cd ~/hr.dobs.cloud
git reset --hard origin/main
composer install --no-dev
php artisan migrate:fresh --force --seed
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan optimize
```

### If Database is Corrupted
```bash
php artisan migrate:fresh --force
php artisan db:seed
```

### If Permissions are Wrong
```bash
chown -R dobsykjq:dobsykjq ~/hr.dobs.cloud
find ~/hr.dobs.cloud/storage -type d -exec chmod 775 {} \;
find ~/hr.dobs.cloud/storage -type f -exec chmod 664 {} \;
```

---

## Useful Aliases (Add to ~/.bashrc)

```bash
# Add these to ~/.bashrc for quick access
echo 'alias cdhr="cd ~/hr.dobs.cloud"' >> ~/.bashrc
echo 'alias art="php artisan"' >> ~/.bashrc
echo 'alias logs="tail -f ~/hr.dobs.cloud/storage/logs/laravel.log"' >> ~/.bashrc
echo 'alias deploy="cd ~/hr.dobs.cloud && git pull && composer install --no-dev && php artisan migrate --force && php artisan optimize"' >> ~/.bashrc

# Then reload
source ~/.bashrc
```

**Usage after adding:**
```bash
cdhr        # Go to project
art cache:clear    # Run artisan
logs        # Watch logs
deploy      # Full deployment
```

---

## Production Checklist

Before going live, verify:

```bash
# 1. Environment is production
grep APP_ENV ~/hr.dobs.cloud/.env

# 2. Debug is off
grep APP_DEBUG ~/hr.dobs.cloud/.env

# 3. Caches are built
ls -la ~/hr.dobs.cloud/bootstrap/cache/

# 4. Storage is writable
ls -ld ~/hr.dobs.cloud/storage

# 5. Symlink exists
ls -la ~/hr.dobs.cloud/public/storage

# 6. Database is seeded
php artisan tinker --execute="DB::table('front_details')->count();"

# 7. Site responds
curl -I https://hr.dobs.cloud
```

---

## Quick Reference

| Task | Command |
|------|---------|
| **Fix 500 Error** | `php artisan db:seed --class=FrontSeeder` |
| **Update Code** | `git pull && composer install --no-dev` |
| **Clear Cache** | `php artisan optimize:clear` |
| **View Logs** | `tail -50 storage/logs/laravel.log` |
| **Database Query** | `php artisan tinker` |
| **Permissions** | `chmod -R 775 storage` |
| **Deploy** | `git pull && php artisan migrate --force && php artisan optimize` |

---

**📖 Full Guides:**
- `QUICK_START.md` - Get site live in 5 minutes
- `500_ERROR_FIX_GUIDE.md` - Fix current 500 error
- `NAMECHEAP_CPANEL_DEPLOYMENT.md` - Complete deployment guide
- `GIT_DEPLOYMENT_SETUP.md` - Git-based deployment setup

**💾 Backup First:** Always backup before major changes!
