# Cloudflare "invalid drx2 host" Investigation Guide

## Problem Confirmed

✅ Successfully captured the error!
- **Error Message:** `"invalid drx2 host"`  
- **HTTP Status:** 503
- **Source:** Cloudflare (server: cloudflare)
- **Frequency:** ~40% of rapid requests fail
- **Pattern:** Intermittent, requires refresh to fix

## What We've Ruled Out

❌ **NOT** in your Laravel application code  
❌ **NOT** in your nginx configurations (backend-web, web-proxy)  
❌ **NOT** in AWS Load Balancer configurations  
❌ **NOT** a DNS issue  
❌ **NOT** from health check failures (those are now fixed!)

## Where "drx2" Must Be

Since we can't find "drx2" anywhere in your infrastructure, it MUST be in one of these Cloudflare configurations:

1. **Cloudflare Load Balancer** - Custom origin pools with "drx2" as an origin name
2. **Cloudflare Workers** - Custom Worker script handling requests
3. **Cloudflare Custom Error Pages** - Custom 503 error page
4. **Cloudflare Transform Rules** - Header modifications or routing rules
5. **Cloudflare Origin Rules** - Origin server selection based on conditions

## Investigation Steps

### Step 1: Check Cloudflare Load Balancing

1. Login to Cloudflare Dashboard: https://dash.cloudflare.com
2. Select zone: `jasarah-ksa.com`
3. Go to: **Traffic → Load Balancing**
4. Look for:
   - Any Load Balancer configured for `app.jasarah-ksa.com`
   - Origin pools named "drx2" or containing "drx2"
   - Health checks failing on any origins
   
**If found:** Delete or fix the "drx2" origin

### Step 2: Check Cloudflare Workers

1. Go to: **Workers & Pages**
2. Check if there are any Workers configured
3. Check Worker routes for `app.jasarah-ksa.com/*`
4. Look for "drx2" in any Worker code

**If found:** Edit or delete the Worker

### Step 3: Check Origin Rules

1. Go to: **Rules → Origin Rules**
2. Check for any rules affecting `app.jasarah-ksa.com`
3. Look for rules that might route to "drx2" origin

**If found:** Disable or modify the rule

### Step 4: Check Transform Rules

1. Go to: **Rules → Transform Rules**
2. Check:
   - HTTP Request Header Modifications
   - HTTP Response Header Modifications
   - URL Rewrites
3. Look for any rules mentioning "drx2"

**If found:** Disable the rule

### Step 5: Enable Cloudflare MCP Investigation

To investigate via Cloudflare MCP servers, you need an API token:

1. Go to: https://dash.cloudflare.com/profile/api-tokens
2. Click "Create Token"
3. Use template: **Edit zone DNS** or **Read all resources**
4. Add the token to `.cursor/mcp.json`:

```json
{
  "mcpServers": {
    "cloudflare-audit-logs": {
      "command": "npx",
      "args": [
        "-y",
        "mcp-remote",
        "https://auditlogs.mcp.cloudflare.com/mcp"
      ],
      "env": {
        "CLOUDFLARE_API_TOKEN": "YOUR_TOKEN_HERE"
      }
    }
  }
}
```

Then restart Cursor and I can investigate programmatically.

## Quick Manual Check (Do This Now!)

### Check for Load Balancer:

In Cloudflare Dashboard:
- Traffic → Load Balancing
- Look for load balancer on `app.jasarah-ksa.com`
- Check if there's a second origin besides your main ALB
- Look for any origin called "drx2" or failing health checks

**Most Likely:** You have a Cloudflare Load Balancer with 2 origins:
1. Your main ALB (working)
2. "drx2" (broken/old origin)

When Cloudflare routes to "drx2", it fails with that error!

## Temporary Workaround (If Needed Urgently)

If you need to fix this immediately before finding the root cause:

1. In Cloudflare, set **DNS record to DNS-only** (gray cloud, not proxied)
2. This bypasses Cloudflare and goes directly to your ALB
3. Downside: Loses Cloudflare CDN benefits temporarily

To do this:
- Cloudflare Dashboard → DNS → Records
- Find `app` → `CNAME` → Click the orange cloud to make it gray
- Wait 1-2 minutes for propagation
- Test: No more "drx2" errors!

## Test Script Results

From `/tmp/etraining-test-20251015-152327`:
- **15 requests made**
- **6 failures (40%)** 
- **All failures:** HTTP 503 with "invalid drx2 host"
- **Pattern:** Random failures, not sequential

This 40% failure rate suggests Cloudflare Load Balancer is routing ~40% of traffic to a broken "drx2" origin!

---

**Next Actions:**
1. Check Cloudflare Dashboard → Traffic → Load Balancing
2. Look for "drx2" origin
3. Remove or fix it
4. Or provide API token for automated investigation

