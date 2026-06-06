# ⚙️ HTTPS Assets Fix - Using `php artisan serve` (No Dev Server)

## 📋 Problem

Anda menggunakan **`php artisan serve`** (bukan `npm run dev`), dan CSS/JS tidak ter-render di HTTPS.

Ini karena:
1. ❌ Assets belum di-compile dengan `npm run build`
2. ❌ Laravel mencari compiled assets di `public/build/` (tidak ada)
3. ❌ Vite manifest tidak digenerate

---

## ✅ Solution: Build Assets untuk Production

Ketika menggunakan `php artisan serve` tanpa dev server, Anda harus compile assets terlebih dahulu.

### Step 1: Build Vite Assets
```bash
npm run build
```

**Output yang diharapkan**:
```
✓ 1234 modules transformed. xxx ms
dist files written in 0.00s

✓ built in 2.34s
```

**Yang terjadi**:
- ✅ Compile TypeScript/JSX
- ✅ Bundle CSS & JS
- ✅ Minify & optimize
- ✅ Generate manifest.json
- ✅ Output ke `public/build/`

### Step 2: Verify Output
```bash
# Cek apakah build folder ada
ls -la public/build/
# atau di Windows:
dir public\build\
```

**Harus ada file-file ini**:
```
public/build/
├── manifest.json          ✅ Manifest file (Vite map)
├── assets/
│   ├── app-xxxxx.css      ✅ Compiled CSS
│   └── app-xxxxx.js       ✅ Compiled JavaScript
└── ...
```

### Step 3: Clear Laravel Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 4: Start Server
```bash
php artisan serve
```

### Step 5: Verify in Browser
1. Open: `https://your-domain`
2. Inspect (F12) → Sources tab
3. Check Network tab:
   - All CSS files should show 200 ✅
   - All JS files should show 200 ✅
   - No 404 errors ❌

---

## 🔧 Full Setup Checklist

### Before Running `npm run build`:

- [ ] `.env` configured: `APP_URL=https://...`
- [ ] `APP_ENV=local` or `production` (doesn't matter for build)
- [ ] All dependencies installed: `npm install`
- [ ] `node_modules/` folder exists

### Build Command:
```bash
npm run build
```

### After Build:

- [ ] `public/build/manifest.json` exists
- [ ] `public/build/assets/` folder has `.css` and `.js` files
- [ ] Cache cleared: `php artisan cache:clear`
- [ ] Server started: `php artisan serve`
- [ ] Browser shows styled page (not plain HTML)

---

## 📊 What `npm run build` Does

```
resources/css/app.css
resources/js/app.js
(+ other imported files)
        ↓
   [Vite Build]
        ↓
public/build/
├── manifest.json
└── assets/
    ├── app-abc123.css
    └── app-abc123.js
```

**Vite then replaces**:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

**With**:
```html
<link href="/build/assets/app-abc123.css" rel="stylesheet">
<script src="/build/assets/app-abc123.js" type="module"></script>
```

---

## ⚡ Common Mistakes

### ❌ Mistake 1: Run `npm run dev` dengan `php artisan serve`
```bash
# DON'T do both:
npm run dev          # ← This starts dev server on port 5173
php artisan serve    # ← And this serves on port 8000
```

**Choose ONE**:
- Option A: `npm run dev` only (dev server at 5173)
- Option B: `npm run build` + `php artisan serve` (compile assets)

### ❌ Mistake 2: Forget to build after changing CSS/JS
```bash
# If you change resources/css/app.css or resources/js/app.js:
# You MUST rebuild:
npm run build   # ← Don't forget this!
```

### ❌ Mistake 3: Don't clear cache
```bash
npm run build
# Need to do this too:
php artisan config:clear
php artisan cache:clear
```

---

## 🚀 Complete Workflow for `php artisan serve`

### Initial Setup
```bash
# 1. Install dependencies
npm install

# 2. Build assets
npm run build

# 3. Clear cache
php artisan config:clear
php artisan cache:clear

# 4. Start server
php artisan serve
```

### During Development (Make Changes)
```bash
# 1. Edit CSS/JS/Blade files
# Edit: resources/css/app.css
# Edit: resources/js/app.js
# Or: resources/views/...

# 2. Rebuild assets
npm run build

# 3. Clear cache & refresh browser
php artisan cache:clear
# Then Ctrl+Shift+R in browser (hard refresh)
```

### When Code is Ready for Production
```bash
npm run build                   # Build optimized assets
php artisan config:cache       # Cache config
php artisan route:cache        # Cache routes
php artisan view:cache         # Cache views
```

---

## 🔍 Troubleshooting

### Problem: "Could not find manifest.json"
```
Laravel Error: VITE_MANIFEST_PATH (public/build/manifest.json) does not exist
```

**Solution**:
```bash
npm run build
php artisan config:clear
```

### Problem: Old CSS/JS still showing
```
Browser shows old styling or JavaScript not working
```

**Solution**:
```bash
# 1. Rebuild
npm run build

# 2. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 3. Hard refresh browser
# Ctrl+Shift+R (or Cmd+Shift+R on Mac)

# 4. Check if node_modules/.vite still has old files
rm -rf node_modules/.vite
npm run build
```

### Problem: Assets return 404
```
Network tab shows: GET /build/assets/app-xxx.css → 404
```

**Solution**:
```bash
# 1. Verify build folder exists
ls -la public/build/

# 2. If not exist, rebuild
npm run build

# 3. Verify manifest.json
cat public/build/manifest.json

# 4. Check if folder is gitignored
# public/build/ should NOT be gitignored in production
```

### Problem: "Mixed content" error over HTTPS
```
Mixed Content: The page was loaded over HTTPS, but requested an insecure resource 'http://...'
```

**Solution**:
Add to `app/Providers/AppServiceProvider.php`:
```php
public function boot(): void
{
    if ($this->app->environment('production')) {
        URL::forceScheme('https');
    }
}
```

---

## 📋 Production Deployment Checklist

When deploying to production with `php artisan serve`:

- [ ] `.env` has correct `APP_URL=https://...`
- [ ] Run `npm install` on production server
- [ ] Run `npm run build` on production server
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Verify `public/build/` exists with assets
- [ ] Start server: `php artisan serve`
- [ ] Test: Open in browser, check Network tab
- [ ] All CSS/JS show 200 OK ✅
- [ ] Page displays correctly with styling ✅

---

## 🎯 Quick Summary

| If You Use | What to Do |
|-----------|-----------|
| `npm run dev` (dev server) | ✅ Use this, don't need `npm run build` |
| `php artisan serve` (no dev) | ✅ Must run `npm run build` first |
| Production deployment | ✅ Run `npm run build` on server |

---

## ✨ Your Situation

Since you're using `php artisan serve`:

**Do this:**
```bash
cd c:\web-batik\web-batik

# 1. Build assets
npm run build

# 2. Clear caches
php artisan config:clear
php artisan cache:clear

# 3. Start server
php artisan serve

# 4. Open in browser
# https://localhost:8000
# Check Network tab → All should show 200 ✅
```

---

## 🔗 References

- Vite Documentation: https://vitejs.dev/
- Laravel Vite: https://laravel.com/docs/vite
- Build Output: https://vitejs.dev/config/#build-outdir

---

Generated: December 3, 2025
For: Users running `php artisan serve` without dev server
Status: READY ✅
