# 🔐 HTTPS with Vite - CSS/JS Not Rendering Fix

## 📋 Problem

Ketika menggunakan HTTPS (dengan Cloudflare tunnel atau proxy):
- ❌ CSS tidak ter-load (halaman tidak ter-style)
- ❌ JavaScript tidak ter-load
- ❌ Halaman kosong atau basic HTML only
- ❌ Inspect element shows 404 or CORS errors

**Example Error Di Browser Console**:
```
Failed to load module script: expected a javascript module script but the server responded with a mime type of "text/html". 

Mixed Content: The page at 'https://...' was loaded over HTTPS, but requested an insecure resource 'http://localhost:5173/...'. This request has been blocked; the content must be served over HTTPS.
```

---

## 🔍 Root Cause

### Masalah #1: Vite Dev Server HTTP saat App HTTPS
Ketika menggunakan Cloudflare tunnel atau reverse proxy:
- App berjalan di HTTPS (tunnel)
- Vite dev server masih di HTTP (localhost:5173)
- Browser block mixed content (HTTPS page + HTTP resources)

### Masalah #2: Vite Manifest Path Salah
Saat production build:
- Manifest file tidak di-update dengan benar
- Asset paths di-generate dengan scheme yang salah

### Masalah #3: Asset Helper Tidak Detect HTTPS
Laravel `asset()` helper tidak mengetahui protocol sebenarnya dari proxy/tunnel

---

## ✅ Solutions

### Solution #1: Configure Vite untuk HTTPS (Development)

**File**: `vite.config.js`

```javascript
import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    
    // ✅ ADDED: Configure server untuk HTTPS tunnel
    server: {
        cors: true,
        hmr: {
            // Jika menggunakan tunnel/proxy, disable HMR atau configure properly
            host: 'localhost',
            port: 5173,
            protocol: 'ws', // Use 'wss' jika tunnel support WebSocket over HTTPS
        },
        // Jika absolute URLs diperlukan:
        // middlewareMode: true,
    },
});
```

### Solution #2: Trust Proxies (Already Done ✓)

**File**: `app/Http/Middleware/TrustProxies.php`
```php
protected $proxies = '*';

protected $headers =
    Request::HEADER_X_FORWARDED_FOR |
    Request::HEADER_X_FORWARDED_HOST |
    Request::HEADER_X_FORWARDED_PORT |
    Request::HEADER_X_FORWARDED_PROTO |
    Request::HEADER_X_FORWARDED_AWS_ELB;
```

✅ Sudah dikonfigurasi dengan benar!

### Solution #3: Ensure APP_URL Correct

**File**: `.env`
```env
APP_URL=https://things-womens-gender-exhibits.trycloudflare.com
```

✅ Sudah HTTPS dengan benar!

### Solution #4: Force HTTPS in AppServiceProvider

**File**: `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        
        // ✅ Force HTTPS for asset URLs
        // This ensures Vite asset URLs are generated with https:// protocol
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
```

---

## 🛠️ Step-by-Step Fix Guide

### Step 1: Update AppServiceProvider
```php
// Add to boot() method:
if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```

### Step 2: Rebuild Vite Assets (Production)
```bash
npm run build
```

This generates fresh manifest with correct paths.

### Step 3: Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 4: Restart Dev Server (or Web Server)
```bash
# If using dev server:
php artisan serve

# If using production:
# Restart PHP-FPM or Apache/Nginx
```

### Step 5: Verify Assets Loading
```bash
# 1. Open browser DevTools (F12)
# 2. Go to Network tab
# 3. Reload page
# 4. Check that assets load:
#    - app.css (200 OK)
#    - app.js (200 OK)
# 5. All should be HTTPS URLs
```

---

## 📊 Before vs After

### ❌ BEFORE
```html
<!-- Mixed content - causes blocking -->
<link href="http://localhost:5173/resources/css/app.css" rel="stylesheet">
<script src="http://localhost:5173/resources/js/app.js" type="module"></script>

<!-- Asset path using HTTP -->
<img src="http://things-womens-gender-exhibits.trycloudflare.com/assets/logo.png">
```

### ✅ AFTER
```html
<!-- All HTTPS - no mixed content -->
<link href="https://things-womens-gender-exhibits.trycloudflare.com/assets/app.css" rel="stylesheet">
<script src="https://things-womens-gender-exhibits.trycloudflare.com/assets/app.js" type="module"></script>

<!-- Asset path using correct HTTPS -->
<img src="https://things-womens-gender-exhibits.trycloudflare.com/assets/logo.png">
```

---

## 🧪 Testing

### Test 1: Verify Manifest File
```bash
# Production build manifest
cat public/build/manifest.json

# Should show HTTPS URLs for all assets:
{
  "resources/css/app.css": {
    "file": "assets/app-xxxxx.css",
    "src": "resources/css/app.css",
    "isEntry": true
  },
  "resources/js/app.js": {
    "file": "assets/app-xxxxx.js",
    "src": "resources/js/app.js",
    "isEntry": true
  }
}
```

### Test 2: Check Generated HTML
```bash
# After rendering page, view source:
# - All <link> tags should use HTTPS
# - All <script> tags should use HTTPS
# - DevTools Network tab should show all 200 OK
```

### Test 3: Browser Console
```javascript
// In browser console, check for errors:
// Should see NO errors about:
// - Mixed content
// - CORS
// - 404 not found
// - Failed to load
```

---

## 🔗 Related Configuration

### Vite Config Best Practices for Tunnels:
```javascript
export default defineConfig({
    plugins: [...],
    
    server: {
        // Disable HMR if issues occur
        middlewareMode: process.env.NODE_ENV === 'production',
        
        // OR configure HMR for tunnel:
        hmr: {
            protocol: 'ws',    // or 'wss' if tunnel support HTTPS WS
            host: 'localhost',
            port: 5173,
        },
    },
    
    // Don't change manifest path
    build: {
        manifest: true,
        outDir: 'public/build',
        assetsDir: 'assets',
    },
});
```

### AppServiceProvider for Production:
```php
public function boot(): void
{
    // ... other boot code ...
    
    // Force HTTPS
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
    
    // Optional: Force domain as well
    if ($this->app->environment('production')) {
        URL::forceRootUrl(config('app.url'));
    }
}
```

---

## 📝 Configuration Checklist

- [ ] `APP_URL=https://your-domain` in `.env`
- [ ] `TrustProxies` middleware configured with `$proxies = '*'`
- [ ] Headers includes `HEADER_X_FORWARDED_PROTO`
- [ ] `AppServiceProvider` calls `URL::forceScheme('https')` in production
- [ ] Vite assets built with `npm run build`
- [ ] Laravel cache cleared: `php artisan cache:clear`
- [ ] Browser cache cleared (Ctrl+Shift+Delete)
- [ ] All resources loading as HTTPS in Network tab

---

## 🚨 Troubleshooting

### Issue: Assets still loading over HTTP
**Solution**:
1. Check `.env`: `APP_URL=https://...`
2. Check middleware: Verify TrustProxies headers
3. Clear cache: `php artisan config:clear`
4. Rebuild: `npm run build`
5. Hard refresh browser: Ctrl+Shift+R

### Issue: CORS errors in console
**Solution**:
1. Ensure `server.cors = true` in vite.config.js
2. Check if Cloudflare blocking
3. Test locally first: `npm run dev`

### Issue: Mixed content warning
**Solution**:
1. Force HTTPS in AppServiceProvider
2. Ensure all asset URLs use HTTPS
3. Check manifest.json for correct paths

### Issue: Can't find manifest.json after build
**Solution**:
1. Verify build output: `ls public/build/manifest.json`
2. Check vite.config.js has correct paths
3. Run: `npm run build` again
4. Check: `php artisan view:clear`

---

## 🎯 For Cloudflare Tunnel Specifically

When using Cloudflare tunnel (like your setup):

```
Your Browser
    ↓ (HTTPS)
Cloudflare Tunnel
    ↓ (HTTP)
Your Local App:8000
```

**Configuration**:
1. `.env`: `APP_URL=https://tunnel-url`
2. App: Trust all proxies (`$proxies = '*'`)
3. Assets: Force HTTPS scheme

This ensures:
- Browser sees HTTPS (tunnel URL)
- App knows it's HTTPS (via X-Forwarded-Proto header)
- Assets generated with HTTPS URLs

---

## ✅ Solution Applied

For your setup, apply these changes:

**1. Update `app/Providers/AppServiceProvider.php`**:
```php
public function boot(): void
{
    User::observe(UserObserver::class);
    
    // Force HTTPS scheme for asset generation
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

**2. Run in production/build mode**:
```bash
npm run build
php artisan cache:clear
php artisan config:clear
```

**3. Verify**:
- Check browser Network tab
- All CSS/JS should show 200 OK
- All URLs should be HTTPS

---

## 📚 Laravel & Vite Docs

- URL Scheme: https://laravel.com/docs/helpers#method-url
- Vite Config: https://vitejs.dev/config/
- Trust Proxies: https://laravel.com/docs/requests#configuring-trusted-proxies

---

Generated: December 3, 2025
Status: READY FOR IMPLEMENTATION ✅
