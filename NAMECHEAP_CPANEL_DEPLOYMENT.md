# iLab HR - Namecheap cPanel Deployment Guide

## Pre-Deployment Checklist

Before deploying to Namecheap cPanel, ensure you have:
- ✅ cPanel account credentials
- ✅ Domain name pointed to Namecheap
- ✅ FTP/SFTP access details
- ✅ MySQL database credentials
- ✅ SSL certificate (Let's Encrypt is free on cPanel)

---

## Step 1: Prepare Your Local Project for Production

### 1.1 Update Environment File (.env)

On your local machine, create a production-ready `.env`:

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://hr.dobs.cloud/

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=dobsykjq_hrsystem
DB_USERNAME=dobsykjq_dms
DB_PASSWORD="9gj*X]MwPPy+"

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com

FILESYSTEM_DISK=public
APP_TIMEZONE=UTC
```

### 1.2 Build Assets for Production

```bash
npm run production
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 2: Upload Project to cPanel

### 2.1 Connect via File Manager or FTP

**Via cPanel File Manager:**
1. Log into cPanel
2. Navigate to File Manager
3. Go to `public_html` directory
4. Upload project files

**Via FTP (Recommended for large projects):**
1. Use FileZilla, WinSCP, or similar FTP client
2. Connect using FTP credentials from cPanel
3. Upload project to `public_html` or subdomain folder

### 2.2 Directory Structure on cPanel

```
public_html/
├── public/          (All web-accessible files)
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── saas/
│   ├── favicon.png
│   └── index.php
├── app/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── bootstrap/
├── vendor/
├── .env             (Upload this!)
├── .htaccess        (Apache rewrite rules)
├── artisan
├── composer.json
└── composer.lock
```

---

## Step 3: Create Database on cPanel

### 3.1 Using cPanel MySQL Database Wizard

1. Log into cPanel
2. Go to **MySQL Databases**
3. Create new database (e.g., `yourusername_ilab_prod`)
4. Create new MySQL user (e.g., `yourusername_ilab_user`)
5. Add user to database with **ALL PRIVILEGES**
6. Note the connection details

### 3.2 Update .env with cPanel Database

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourusername_ilab_prod
DB_USERNAME=yourusername_ilab_user
DB_PASSWORD=your_secure_password
```

---

## Step 4: Configure PHP & Server

### 4.1 Check PHP Version in cPanel

1. Go to **Select PHP Version**
2. Ensure PHP 8.2+ is selected
3. Install required extensions:
   - bcmath
   - ctype
   - fileinfo
   - json
   - mbstring
   - openssl
   - pdo
   - pdo_mysql
   - tokenizer
   - xml
   - xmlwriter

### 4.2 Create .htaccess in public_html

Create file: `public_html/.htaccess`

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Step 5: Run Database Migrations

### 5.1 SSH Access (Recommended)

1. In cPanel, go to **Terminal** or use SSH client
2. Navigate to project directory:
   ```bash
   cd ~/public_html
   ```

3. Run migrations:
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=FrontSeeder
   ```

### 5.2 Via File Manager (Alternative)

If SSH not available, create `public_html/migrate.php`:

```php
<?php
define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

exit($kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput([
        'artisan', 'migrate', '--force'
    ]),
    new Symfony\Component\Console\Output\BufferedOutput()
));
```

Visit: `https://yourdomain.com/migrate.php`

**⚠️ Delete migrate.php after running!**

---

## Step 6: Configure Storage & Permissions

### 6.1 Set Storage Permissions

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/storage
```

### 6.2 Create Storage Symlink

```bash
php artisan storage:link
```

---

## Step 7: Set Up SSL Certificate

### 7.1 Using cPanel AutoSSL (Recommended)

1. Go to **AutoSSL**
2. Click **Run AutoSSL**
3. Wait for certificate to be issued
4. Go to **Domains** and enable SSL for your domain

### 7.2 Force HTTPS

Update `public_html/.htaccess`:

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## Step 8: Configure Email

### 8.1 Create Email Account in cPanel

1. Go to **Email Accounts**
2. Create account (e.g., `noreply@yourdomain.com`)
3. Update `.env`:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mail.yourdomain.com
   MAIL_PORT=465
   MAIL_USERNAME=noreply@yourdomain.com
   MAIL_PASSWORD=password
   MAIL_ENCRYPTION=ssl
   ```

---

## Step 9: Final Setup

### 9.1 Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan optimize
```

### 9.2 Set Application Key (if not in .env)

```bash
php artisan key:generate
```

### 9.3 Test Your Site

Visit: `https://yourdomain.com`

---

## Troubleshooting

### 500 Internal Server Error
- Check `storage/logs/laravel.log`
- Verify `.env` database credentials
- Check PHP version compatibility
- Ensure all required PHP extensions installed

### Database Connection Error
- Verify DB_HOST is `localhost`
- Check DB_USERNAME and DB_PASSWORD
- Ensure database exists in cPanel MySQL

### File Upload Errors
- Verify `storage/` directory is writable
- Check cPanel PHP memory limit (increase to 256M+)
- Verify `public/storage` symlink exists

### Email Not Sending
- Check SMTP credentials in `.env`
- Verify email account exists in cPanel
- Check cPanel Exim mail server settings

---

## Post-Deployment

### Regular Backups
1. Go to **Backup**
2. Download backups regularly
3. Or set up automatic backups

### Monitor Performance
1. Check **Resource Usage** in cPanel
2. Monitor database size
3. Clean old logs: `php artisan tinker` → `DB::table('activity_log')->where('created_at', '<', now()->subMonths(3))->delete();`

### Update Application
1. Use Git or re-upload files
2. Run migrations: `php artisan migrate --force`
3. Clear caches: `php artisan optimize`

---

## Important Notes

- **Never commit `.env` to Git**
- **Disable APP_DEBUG in production**
- **Use strong database passwords**
- **Enable SSL immediately**
- **Set up regular backups**
- **Monitor error logs regularly**

---

## Support

For cPanel help: https://www.namecheap.com/support/
For Laravel: https://laravel.com/docs
