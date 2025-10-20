# Cloudflare Configuration Fix for app.jasarah-ksa.com

## Problem Diagnosed

The intermittent Elastic Beanstalk default page is **NOT coming from AWS**. It's coming from **Cloudflare's misconfiguration**.

### What We Found:
1. ✅ No Elastic Beanstalk environments running in AWS
2. ✅ ECS services are healthy (frontend-service: 2 tasks, backend-service: 1 task)
3. ✅ ALB target groups only contain ECS tasks (no EC2 instances)
4. ✅ DNS resolves to Cloudflare IPs (188.114.97.7, 188.114.96.7)
5. ❌ **Cloudflare is proxying to wrong origin(s)**

### Architecture Confirmed:
```
Users → Cloudflare → prod-alb (ALB) → frontend-service (ECS) → backend-service (ECS)
```

**Correct Origin:** `prod-alb-1043387229.eu-central-1.elb.amazonaws.com` (HTTPS only)

---

## Fix Steps in Cloudflare Dashboard

### Step 1: Check DNS Records
1. Log into Cloudflare dashboard
2. Go to your domain: `jasarah-ksa.com`
3. Click **DNS** tab
4. Find the record for `app.jasarah-ksa.com`
5. **Verify it points to:** `prod-alb-1043387229.eu-central-1.elb.amazonaws.com`
6. **Remove any other CNAME/A records** for `app` subdomain

### Step 2: Check Load Balancing (If Enabled)
1. In Cloudflare dashboard, go to **Traffic** → **Load Balancing**
2. Check if there's a load balancer for `app.jasarah-ksa.com`
3. **If yes:**
   - Click to edit
   - Check the **Origin Pools**
   - **Remove any old/incorrect origins** (especially any with "elasticbeanstalk" in the URL)
   - Keep only: `prod-alb-1043387229.eu-central-1.elb.amazonaws.com`

### Step 3: Clear Cloudflare Cache
1. In Cloudflare dashboard, go to **Caching** → **Configuration**
2. Click **Purge Everything**
3. Confirm the purge
4. Wait 1-2 minutes

### Step 4: Check Page Rules
1. Go to **Rules** → **Page Rules**
2. Look for any rules affecting `app.jasarah-ksa.com`
3. **Disable or delete** any suspicious rules

### Step 5: Check Workers
1. Go to **Workers & Pages**
2. Check if any Workers are assigned to `app.jasarah-ksa.com`
3. **Disable** any Workers routing to old origins

### Step 6: Verify SSL/TLS Settings
1. Go to **SSL/TLS** → **Overview**
2. Set mode to: **Full (strict)** or **Full**
3. Go to **SSL/TLS** → **Edge Certificates**
4. Ensure **Always Use HTTPS** is ON

---

## Verification

After making changes:

1. **Wait 5 minutes** for DNS propagation
2. **Test multiple times:**
   ```bash
   curl -I https://app.jasarah-ksa.com
   ```
3. **Open in browser** (incognito mode)
4. **Refresh multiple times** - should always show your Laravel app

---

## If Problem Persists

If you still see the EB page after the above fixes:

### Option A: Bypass Cloudflare Temporarily
1. In Cloudflare DNS settings
2. Click the orange cloud icon next to `app` record
3. Turn it to **gray** (DNS only, bypass proxy)
4. Test if the site works consistently
5. If yes → the issue is definitely in Cloudflare settings

### Option B: Check Cloudflare Analytics
1. Go to **Analytics & Logs** → **Traffic**
2. Filter by hostname: `app.jasarah-ksa.com`
3. Look for **Origin Server** information
4. Check if multiple origins are being hit

---

## Technical Details

### Your AWS Infrastructure (Confirmed Working):
- **Cluster:** prod-ecs-cluster (ACTIVE)
- **Services:**
  - frontend-service: 2/2 tasks healthy
  - backend-service: 1/1 task healthy
  - worker-service: 2/2 tasks healthy
  - scheduler: 1/1 task healthy

### Load Balancers:
- **Public ALB:** prod-alb-1043387229.eu-central-1.elb.amazonaws.com
  - Listener: Port 443 (HTTPS)
  - Target Group: ecs-prod-e-frontend-service (2 healthy targets)
  - Security Group: sg-0c74226e54df5d885

- **Internal ALB:** prod-alb-internal-1645638095.eu-central-1.elb.amazonaws.com
  - Used for service-to-service communication

### Target Groups:
- ecs-prod-e-frontend-service: 2 ECS tasks (healthy)
- ecs-prod-e-backend-service: 1 ECS task (healthy)
- ecs-prod-backend-public: 1 target (unused, safe to ignore)

---

## Next Steps After Fix

Once Cloudflare is fixed:

1. **Monitor for 24 hours** to ensure consistency
2. **Document the correct Cloudflare settings** for future reference
3. **Set up alerts** in Cloudflare for origin failures
4. **Consider reviewing all Cloudflare settings** to prevent similar issues

---

## Questions?

If the issue persists after following these steps, we may need to:
1. Check if there are multiple Cloudflare accounts managing this domain
2. Verify there's no DNS delegation causing issues
3. Review Cloudflare's audit logs to see when/why the bad configuration was introduced

