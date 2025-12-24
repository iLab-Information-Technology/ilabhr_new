# SSH Key Setup for cPanel & GitHub

## Step 1: Generate SSH Key in cPanel

### 1.1 Access cPanel Terminal

1. Log into your **Namecheap cPanel**
2. Look for **Terminal** (under "Advanced" section)
3. Click it to open terminal

### 1.2 Generate SSH Key

Copy & paste this command in cPanel Terminal:

```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa -N ""
```

**What it does:** Creates a secure SSH key pair

---

## Step 2: Get Your Public Key

### 2.1 Display Public Key

In cPanel Terminal, run:

```bash
cat ~/.ssh/id_rsa.pub
```

**Output will look like:**
```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDab... (very long string) ...xyz123== user@yourdomain.com
```

### 2.2 Copy Entire Output

- Select ALL the text
- Right-click and copy
- **Keep this open** - you'll need it in next step

---

## Step 3: Add SSH Key to GitHub

### 3.1 Go to GitHub Settings

1. Log into **GitHub.com**
2. Click your **Profile icon** (top right)
3. Click **Settings**
4. On left menu, click **SSH and GPG keys**

### 3.2 Create New SSH Key

1. Click **New SSH key** (green button)
2. **Title field**: Enter `Namecheap cPanel` or `iLab Server`
3. **Key field**: Paste the SSH key from Step 2.2
4. Click **Add SSH key**

**GitHub will ask for password confirmation** - enter your GitHub password

---

## Step 4: Test SSH Connection

### 4.1 Back in cPanel Terminal

Run this command:

```bash
ssh -T git@github.com
```

### 4.2 Expected Response

You should see:
```
Hi yourusername! You've successfully authenticated, but GitHub does not provide shell access.
```

If you see this ✅ **SSH is working!**

---

## Step 5: Ready to Clone Repository

Now in cPanel Terminal, run:

```bash
# Navigate to public_html
cd ~/public_html

# Clone your GitHub repository
git clone git@github.com:yourusername/ilabhr.git .
```

**Replace `yourusername` with your actual GitHub username**

### Expected Output:
```
Cloning into '.'...
remote: Counting objects: 1234, done.
remote: Compressing objects: 100% (456/456), done.
Receiving objects: 100% (1234/1234), 2.5 MiB | 1.2 MiB/s, done.
Resolving deltas: 100% (789/789), done.
```

**This may take 5-10 minutes for large project - be patient!**

---

## Step 6: Verify Clone Success

After clone finishes:

```bash
# Check if files are there
ls -la ~/public_html

# You should see: artisan, app, config, public, resources, etc.
```

---

## Troubleshooting

### "Permission denied (publickey)"

**Solution:**
```bash
# Verify SSH key exists
ls -la ~/.ssh/

# Should show: id_rsa and id_rsa.pub

# Check permissions
chmod 700 ~/.ssh
chmod 600 ~/.ssh/id_rsa
chmod 644 ~/.ssh/id_rsa.pub
```

### "Connection refused"

**Solution:** Wait a moment after adding SSH key to GitHub (takes 30 seconds to sync)

### "Repository not found"

**Solution:**
- Verify GitHub repo name is correct
- Make sure you're in a **Private** repo (or it can be public)
- Check username spelling

### "Already exists"

If you see "directory not empty" error:

```bash
# Use this instead to clone into existing directory
git clone git@github.com:yourusername/ilabhr.git ilabhr_temp
# Then move files if needed
```

---

## What's Next?

Once clone succeeds, run these commands in order:

```bash
# 1. Navigate to project
cd ~/public_html

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install Node dependencies
npm ci

# 4. Build assets
npm run production

# 5. Copy example env
cp .env.example .env

# 6. Generate app key
php artisan key:generate
```

---

## Quick Reference

| Command | What It Does |
|---------|-------------|
| `ssh-keygen...` | Creates SSH key pair |
| `cat ~/.ssh/id_rsa.pub` | Shows public key for GitHub |
| `ssh -T git@github.com` | Tests SSH connection |
| `git clone...` | Downloads code from GitHub |
| `composer install` | Installs PHP packages |
| `npm ci` | Installs Node packages |
| `npm run production` | Builds CSS/JS for production |

---

**Let me know when you:**
1. ✅ Generated SSH key
2. ✅ Added to GitHub
3. ✅ Tested connection (step 4)
4. ✅ Cloned repository

Then I'll guide you through the final installation steps!
