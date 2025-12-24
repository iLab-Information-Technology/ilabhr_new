# Quick Start - Get hr.dobs.cloud Live NOW

**Time: 5 minutes** | **Status: 500 Error → Fix → Live Site**

---

## Copy & Paste These Commands

### 1️⃣ Open cPanel Terminal
Go to: https://premium139.web-hosting.com:2083 → Terminal

### 2️⃣ Paste All Commands (One Block)

```bash
cd ~/hr.dobs.cloud && \
php artisan db:seed --class=FrontSeeder && \
php artisan db:seed --class=CountriesTableSeeder && \
chmod -R 775 storage bootstrap/cache && \
php artisan storage:link && \
php artisan config:clear && \
php artisan cache:clear && \
php artisan view:clear && \
php artisan optimize && \
echo "✅ DONE! Check hr.dobs.cloud now"
```

**Press Enter and wait 30 seconds**

### 3️⃣ Verify Database

```bash
php artisan tinker --execute="echo 'Rows: ' . DB::table('front_details')->count();"
```

**Expected:** `Rows: 1`

### 4️⃣ Visit Your Site

🌐 **https://hr.dobs.cloud** → Should load!

---

## If Seeding Fails (Alternative)

```bash
cd ~/hr.dobs.cloud
php artisan migrate:fresh --force --seed
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan optimize
```

⚠️ **Warning:** This erases all data and recreates from scratch!

---

## Next Steps After Site is Live

### Upload Your iLab Branding

#### Option A: Via cPanel File Manager
1. Login to cPanel
2. File Manager → `hr.dobs.cloud/public/img/`
3. Upload `ilabhr-logo.png`
4. Go to `hr.dobs.cloud/public/css/`
5. Upload `main.css`

#### Option B: Via Git Push (Fast!)
```bash
# On your local machine (Windows)
cd "C:\Users\usman\OneDrive - iLab\Documents\GitHub\ilabhr_new"
git add .
git commit -m "Added iLab branding - purple and orange theme"
git push origin main

# On cPanel Terminal
cd ~/hr.dobs.cloud
git pull origin main
php artisan optimize
```

### Update Brand Colors in Database

```bash
php artisan tinker
DB::table('front_details')->update(['primary_color' => '#544088']);
exit
```

### Enable SSL Certificate

1. cPanel → SSL/TLS Status
2. Run AutoSSL for hr.dobs.cloud
3. Wait 2-5 minutes
4. Visit https://hr.dobs.cloud (with https://)

---

## Current Configuration

| Setting | Value |
|---------|-------|
| **Domain** | hr.dobs.cloud |
| **Server** | premium139.web-hosting.com |
| **SSH User** | dobsykjq |
| **Database** | dobsykjq_hrsystem |
| **DB User** | dobsykjq_dms |
| **DB Pass** | 9gj*X]MwPPy+ |
| **Path** | /home/dobsykjq/hr.dobs.cloud/ |
| **PHP** | 8.2 (cPanel Select PHP Version) |

**Brand Colors:**
- Primary: `#544088` (iLab Purple)
- Secondary: `#e96920` (iLab Orange)

---

## Troubleshooting

### Still 500 Error?
```bash
tail -50 ~/hr.dobs.cloud/storage/logs/laravel.log
```

### Database Not Connecting?
```bash
nano ~/hr.dobs.cloud/.env
# Press Ctrl+X to exit
```

### Permission Denied?
```bash
chown -R dobsykjq:dobsykjq ~/hr.dobs.cloud
chmod -R 775 ~/hr.dobs.cloud/storage
```

---

## Success Checklist

After running commands, verify:

- [ ] No 500 error when visiting hr.dobs.cloud
- [ ] Installation wizard appears OR login page shows
- [ ] CSS loads properly (check browser network tab)
- [ ] Logo displays correctly
- [ ] Database has 1 row: `SELECT * FROM front_details;`

---

## Support Commands

**Check site status:**
```bash
curl -I https://hr.dobs.cloud
```

**View real-time logs:**
```bash
tail -f ~/hr.dobs.cloud/storage/logs/laravel.log
```

**Clear everything:**
```bash
php artisan optimize:clear
```

---

**🎯 Main Goal:** Get from 500 error → Working site in 5 minutes!

**📞 Need Help?** Share the output of any failed command and I'll fix it immediately.
