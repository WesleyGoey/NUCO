# 🐳 Docker Deployment Guide for NUCO

## 📋 Overview

This project uses Docker for deployment to Railway. The setup includes:
- **Dockerfile**: Multi-stage build for production
- **railway.json**: Railway-specific configuration
- **nixpacks.toml**: Alternative builder configuration

## 🚀 Railway Deployment

### Prerequisites
1. GitHub account with repository access
2. Railway account (https://railway.app)
3. MySQL service on Railway

### Step 1: Generate APP_KEY

```bash
cd ~/Documents/GitHub/NUCO/NUCO
php artisan key:generate --show
```

Copy the output (e.g., `base64:xxxxxxxxxxxxx`)

### Step 2: Setup Railway Project

1. Login to Railway: https://railway.app
2. Click **"New Project"** → **"Deploy from GitHub"**
3. Select repository: **NUCO**
4. Click **"Add Service"** → **"Database"** → **"MySQL"**

### Step 3: Configure Environment Variables

In Railway Dashboard → **Variables** tab, add:

```bash
# App Configuration
APP_NAME=NUCO
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

# Database (Auto-injected by Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Payment Gateway
MIDTRANS_SERVER_KEY=your_server_key_here
MIDTRANS_CLIENT_KEY=your_client_key_here
MIDTRANS_IS_PRODUCTION=false

# Cache & Session (File-based for Railway)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

# File System
FILESYSTEM_DISK=public
```

### Step 4: Deploy

1. Push to GitHub:
   ```bash
   git add .
   git commit -m "feat: add Docker deployment setup"
   git push origin main
   ```

2. Railway will automatically:
   - Detect `Dockerfile`
   - Build the image
   - Run migrations (via Procfile release phase)
   - Start the application

### Step 5: Verify Deployment

Visit your Railway URL:
```
https://your-app-name.up.railway.app
```

Check endpoints:
- `/` - Homepage
- `/debug/app` - App info
- `/debug/db` - Database connection status

## 🔧 Local Development with Docker

### Option 1: Using Docker Compose (Recommended)

```bash
# Build and start
docker-compose up -d

# View logs
docker-compose logs -f app

# Run migrations
docker-compose exec app php artisan migrate

# Stop containers
docker-compose down
```

### Option 2: Using Dockerfile Only

```bash
# Build image
docker build -t nuco-app .

# Run container
docker run -p 8000:8000 \
  -e APP_KEY="base64:..." \
  -e DB_HOST="your_db_host" \
  -e DB_DATABASE="your_db_name" \
  -e DB_USERNAME="your_db_user" \
  -e DB_PASSWORD="your_db_pass" \
  nuco-app

# Access app
open http://localhost:8000
```

## 📦 File Structure

```
NUCO/  (repository root)
├── Dockerfile              # Main Docker configuration
├── railway.json            # Railway deployment config
├── .dockerignore          # Files to exclude from Docker build
├── nixpacks.toml          # Alternative builder config
├── docker-compose.yml     # Local development setup (optional)
└── NUCO/                  # Laravel application
    ├── Procfile           # Process commands
    ├── .railwayignore    # Railway-specific ignore
    └── ...
```

## 🐛 Troubleshooting

### Issue: Build fails with "composer install" error

**Solution:** Clear composer cache
```bash
git commit -m "trigger rebuild" --allow-empty
git push
```

### Issue: "Class not found" errors

**Solution:** Run composer dump-autoload
```bash
# Already handled in Dockerfile
RUN composer dump-autoload --optimize
```

### Issue: Database connection failed

**Solution:** Check environment variables
1. Verify MySQL service is running
2. Check DB_* variables in Railway dashboard
3. Use Railway's injected variables: `${{MYSQLHOST}}`, etc.

### Issue: Storage link not created

**Solution:** Already handled in Procfile release phase
```bash
release: php artisan migrate --force && php artisan storage:link
```

### Issue: Assets not loading (404)

**Solution:** Verify Vite build
```bash
# Check build output in Railway logs
=== Vite Build Complete ===
public/build/manifest.json
```

## 📊 Environment Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| `APP_KEY` | Application encryption key | `base64:xxxxx` |
| `APP_URL` | Public application URL | `https://app.railway.app` |
| `DB_HOST` | MySQL host | `${{MYSQLHOST}}` |
| `DB_DATABASE` | Database name | `${{MYSQLDATABASE}}` |
| `MIDTRANS_SERVER_KEY` | Payment gateway server key | `Mid-server-xxx` |
| `MIDTRANS_CLIENT_KEY` | Payment gateway client key | `Mid-client-xxx` |

## 🎯 Production Checklist

- [ ] Generate and set `APP_KEY`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure MySQL service
- [ ] Set Midtrans credentials
- [ ] Verify storage permissions
- [ ] Test database connection
- [ ] Run migrations
- [ ] Test payment flow
- [ ] Check logs for errors

## 📚 Additional Resources

- [Railway Documentation](https://docs.railway.app)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)

---

**Last Updated:** January 2026  
**Maintainer:** NUCO Team
