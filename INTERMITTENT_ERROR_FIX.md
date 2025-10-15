# Fix for Intermittent "drx2 host" or 403 Error on app.jasarah-ksa.com

## Root Cause

The intermittent error requiring page refresh is likely caused by **CSRF token mismatch** due to missing domain configuration in Sanctum.

## The Issue

When you visit `app.jasarah-ksa.com`, Laravel/Sanctum needs to know this is a trusted domain for stateful authentication. If the domain isn't listed in `SANCTUM_STATEFUL_DOMAINS`, CSRF validation can fail intermittently, causing 403 errors or blocked requests.

**Symptom:** Error occurs, then refreshing the page fixes it (because a new CSRF token is generated).

## Solution

### Step 1: Update Production Environment Variables

On your production server, update the `.env` file to include your production domain:

```bash
# Add this to your production .env
SANCTUM_STATEFUL_DOMAINS=app.jasarah-ksa.com,www.app.jasarah-ksa.com
SESSION_DOMAIN=.jasarah-ksa.com
APP_URL=https://app.jasarah-ksa.com
```

**Important:** Use just the domain names, **without** `https://` prefix for Sanctum stateful domains.

### Step 2: Update Session Cookie Security

Since you're behind Cloudflare with HTTPS, update these in production `.env`:

```bash
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### Step 3: Update Cloudflare Settings

#### Cache Configuration
Ensure Cloudflare is NOT caching:
- Session cookies
- CSRF tokens
- Dynamic pages

**Recommended Cloudflare Page Rules for `app.jasarah-ksa.com/*`:**
- Cache Level: Bypass
- OR create a cache rule to bypass for `/` and `/login`, `/dashboard`, etc.

#### Security Settings
Check if any security rules are blocking legitimate requests:
1. Go to Cloudflare Dashboard → Security → WAF
2. Check for rules that might be triggering false positives
3. Review "Security Events" for recent blocks

### Step 4: Verify TrustProxies Configuration

Your `TrustProxies` middleware should trust Cloudflare IPs. Current config (`protected $proxies = '*'`) is correct but can be more specific:

```php
// backend/app/Http/Middleware/TrustProxies.php
protected $proxies = '*'; // This is fine, or use Cloudflare IP ranges
protected $headers = Request::HEADER_X_FORWARDED_ALL;
```

### Step 5: Clear All Caches

After making changes, clear all caches:

```bash
# On production server
cd /path/to/backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Restart PHP-FPM if needed
sudo service php8.2-fpm restart  # Adjust PHP version as needed
```

Also clear Cloudflare cache:
1. Go to Cloudflare Dashboard
2. Navigate to Caching → Configuration
3. Click "Purge Everything"

## Testing

After applying these fixes:

1. **Test in incognito/private window** to ensure no old cookies interfere
2. **Navigate through multiple pages** without errors
3. **Check browser console** for CSRF or cookie errors
4. **Monitor for 24 hours** to ensure the issue doesn't recur

## Additional Diagnostics

### Check Current Environment Variables

On your production server:
```bash
cd /path/to/backend
php artisan tinker

# In tinker:
config('sanctum.stateful')
config('session.domain')
config('session.secure')
config('app.url')
```

Expected output:
```php
config('sanctum.stateful')
// Should include: ["app.jasarah-ksa.com", "www.app.jasarah-ksa.com"]

config('session.domain')
// Should be: ".jasarah-ksa.com"

config('session.secure')
// Should be: true (for HTTPS)

config('app.url')
// Should be: "https://app.jasarah-ksa.com"
```

### Monitor Logs

Watch for CSRF or session errors:

```bash
# Application logs
tail -f /path/to/backend/storage/logs/laravel.log | grep -i "csrf\|session\|token"

# Nginx logs
tail -f /var/log/nginx/error.log | grep -i "403\|csrf"
```

### Check Cloudflare Logs (After Setting Up MCP)

Once you restart Cursor with the Cloudflare MCP servers configured, we can:
1. Query security events
2. Check WAF rule triggers
3. Analyze traffic patterns
4. Review cache hit/miss ratios

## Summary of Changes

| File/Location | Setting | Old Value | New Value |
|--------------|---------|-----------|-----------|
| Production `.env` | `SANCTUM_STATEFUL_DOMAINS` | Missing or incomplete | `app.jasarah-ksa.com,www.app.jasarah-ksa.com` |
| Production `.env` | `SESSION_SECURE_COOKIE` | `false` | `true` |
| Production `.env` | `APP_URL` | May vary | `https://app.jasarah-ksa.com` |
| Cloudflare | Cache Level | May be caching | Bypass for dynamic pages |

## Why This Fixes the Issue

1. **SANCTUM_STATEFUL_DOMAINS**: Tells Laravel Sanctum that `app.jasarah-ksa.com` is a trusted domain for stateful authentication, enabling proper CSRF token handling.

2. **SESSION_SECURE_COOKIE=true**: Ensures cookies are only sent over HTTPS, preventing cookie issues when Cloudflare proxies the connection.

3. **Cloudflare Cache Bypass**: Prevents Cloudflare from serving cached responses that might have stale CSRF tokens.

## Expected Outcome

After applying these fixes:
- ✅ No more intermittent errors
- ✅ Smooth page navigation without refreshing
- ✅ Proper CSRF token validation
- ✅ Consistent user experience

## If Issue Persists

If the error continues after these changes:

1. **Check Cloudflare Security Events** (use MCP after restart)
2. **Review Laravel logs** for specific error messages
3. **Test with Cloudflare temporarily disabled** (Development Mode in Cloudflare)
4. **Verify DNS propagation** is complete
5. **Check if CDN is caching cookies** (it shouldn't)

---

**Next Step:** Restart Cursor to enable Cloudflare MCP servers, then we can investigate Cloudflare-specific issues.

