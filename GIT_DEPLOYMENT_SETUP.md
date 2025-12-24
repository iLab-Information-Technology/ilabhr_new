# Git-Based Deployment to Namecheap cPanel - Complete Setup

## Overview

You push code to GitHub → cPanel pulls and deploys automatically (or with 1 command)

---

## Step 1: Create GitHub Repository

### 1.1 Sign up at GitHub

Go to https://github.com and create account if you don't have one

### 1.2 Create New Repository

1. Click **"New"** (green button)
2. Name: `ilabhr` (or your preferred name)
3. Description: `iLab HR Management System`
4. Choose **Private** (recommended)
5. Click **Create repository**

### 1.3 Get Your Repository URL

Copy the SSH URL: `git@github.com:yourusername/ilabhr.git`

---

## Step 2: Push Local Code to GitHub

### On Your Local Machine:

```bash
cd C:\Users\usman\OneDrive - iLab\Documents\GitHub\ilabhr_new

# Initialize git (if not already done)
git init
git add .
git commit -m "Initial iLab HR project with branding"

# Add GitHub as remote
git remote add origin git@github.com:yourusername/ilabhr.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**Wait until upload completes** (may take 5-10 minutes for large project)

---

## Step 3: Generate SSH Key in cPanel

### 3.1 Access SSH Terminal

1. Log into Namecheap cPanel
2. Go to **Terminal** (under "Advanced" section)
3. Or use SSH client (PuTTY, Git Bash)

### 3.2 Generate SSH Key

```bash
# Generate SSH key for cPanel user
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""

# Display public key
cat ~/.ssh/id_rsa.pub
```

**Copy the entire output** (starts with `ssh-rsa`)

---

## Step 4: Add SSH Key to GitHub

### 4.1 Go to GitHub Settings

1. Log into GitHub
2. Click **Profile icon** → **Settings**
3. Go to **SSH and GPG keys**
4. Click **New SSH key**

### 4.2 Add cPanel's SSH Key

- **Title**: `Namecheap cPanel`
- **Key**: Paste the output from Step 3.2
- Click **Add SSH key**

---

## Step 5: Deploy to cPanel

### 5.1 Clone Repository to cPanel

```bash
# SSH into cPanel
ssh yourusername@yourdomain.com

# Navigate to public_html
cd ~/public_html

# Remove old files (BACKUP FIRST!)
# Or use a subdomain folder instead

# Clone GitHub repository
git clone git@github.com:yourusername/ilabhr.git .

# Accept fingerprint when prompted (type 'yes')
```

### 5.2 Install Dependencies

```bash
# Install PHP dependencies (removes dev packages)
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build
npm ci
npm run production
```

### 5.3 Create .env File

```bash
# Copy example env
cp .env.example .env

# Generate app key
php artisan key:generate
```

### 5.4 Configure .env for Production

Edit `.env` with database credentials, email, etc:

```bash
nano .env
```

Settings needed:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_DATABASE=cpanel_dbname
DB_USERNAME=cpanel_dbuser
DB_PASSWORD=strong_password

MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

Press `Ctrl+X`, then `Y`, then `Enter` to save.

### 5.5 Run Database Migrations

```bash
# Create database tables
php artisan migrate --force

# Seed initial data
php artisan db:seed --class=FrontSeeder
```

### 5.6 Set Permissions

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 755 public/
```

### 5.7 Create Storage Link

```bash
php artisan storage:link
```

### 5.8 Final Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## Step 6: Update Site (Going Live)

Once deployed, updating is **super easy**:

### Local Machine:

```bash
# Make changes to code
git add .
git commit -m "Updated branding colors"
git push origin main
```

### On cPanel (via SSH):

```bash
cd ~/public_html
git pull origin main
composer install --no-dev
npm ci && npm run production
php artisan migrate --force
php artisan optimize
```

**That's it!** Site is updated with latest code.

---

## Step 7: (Optional) Auto-Deploy with Webhooks

To make it **fully automatic** without manual SSH:

### 7.1 Create Deployment Script

Create file: `public_html/deploy.php`

```php
<?php
// GitHub Webhook Receiver
$secret = 'your_random_secret_key';
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Verify webhook signature
$payload = file_get_contents('php://input');
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($hash, $signature)) {
    http_response_code(403);
    die('Unauthorized');
}

// Only deploy on main branch push
$data = json_decode($payload, true);
if ($data['ref'] !== 'refs/heads/main') {
    die('Not main branch');
}

// Deploy
chdir(__DIR__);
$output = shell_exec('cd ' . __DIR__ . ' && git pull origin main 2>&1');
$output .= shell_exec('composer install --no-dev 2>&1');
$output .= shell_exec('php artisan migrate --force 2>&1');
$output .= shell_exec('php artisan optimize 2>&1');

file_put_contents('deploy.log', date('Y-m-d H:i:s') . "\n" . $output . "\n\n", FILE_APPEND);

http_response_code(200);
echo 'Deployed successfully';
```

### 7.2 Add Webhook to GitHub

1. Go to GitHub repo → **Settings** → **Webhooks**
2. Click **Add webhook**
3. **Payload URL**: `https://yourdomain.com/deploy.php`
4. **Secret**: Use same secret from script
5. **Content type**: `application/json`
6. Click **Add webhook**

**Now every `git push` deploys automatically!** ✨

---

## Troubleshooting

### SSH Key Permission Denied

```bash
# Ensure correct permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/id_rsa
```

### Git Clone Fails

```bash
# Test SSH connection
ssh -T git@github.com

# Expected: Hi yourusername! You've successfully authenticated...
```

### Composer Install Timeout

```bash
# Increase timeout
composer install --no-dev -vvv --no-interaction --no-ansi
```

### Database Migration Fails

```bash
# Check database credentials in .env
# Verify database exists in cPanel MySQL
# Check error log
tail -f storage/logs/laravel.log
```

---

## Daily Workflow

### Update Code:

```bash
# Local machine
git add .
git commit -m "Description of changes"
git push origin main
```

### Deploy (Manual):

```bash
# SSH into cPanel
cd ~/public_html
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan optimize
```

### Deploy (Automatic - if using webhooks):

Just push to GitHub - that's it!

---

## Security Best Practices

1. **Keep .env secure** - Never commit to git
   ```bash
   # Add to .gitignore
   echo ".env" >> .gitignore
   git rm --cached .env
   ```

2. **Use SSH keys** - Never store passwords
3. **Set repository to Private** on GitHub
4. **Rotate database passwords** regularly
5. **Monitor deploy.log** for errors
6. **Backup database weekly**

---

## Rollback (If Something Goes Wrong)

```bash
# See previous commits
git log --oneline

# Revert to previous version
git revert HEAD

# Or reset to specific commit
git reset --hard abc123def456

git push origin main
```

Then pull on cPanel and redeploy.

---

## Summary

| Step | Command | Time |
|------|---------|------|
| 1. Push to GitHub | `git push origin main` | 5 min |
| 2. SSH into cPanel | SSH terminal or PuTTY | 1 min |
| 3. Pull code | `git pull origin main` | 2 min |
| 4. Install deps | `composer install` | 5 min |
| 5. Migrate | `php artisan migrate --force` | 2 min |
| 6. Optimize | `php artisan optimize` | 1 min |
| **Total** | **One command** | **15-20 min** |

**With webhooks:** Just `git push` and it's live! ⚡

---

## Support

- GitHub Help: https://docs.github.com
- Namecheap SSH: https://www.namecheap.com/support/
- Laravel Deployment: https://laravel.com/docs/deployment

**Ready to deploy? Let me know if you need help with any step!**
