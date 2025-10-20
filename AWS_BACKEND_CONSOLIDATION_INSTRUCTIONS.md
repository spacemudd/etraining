# AWS Backend Consolidation Instructions

This document outlines the AWS infrastructure changes needed to complete the backend consolidation after removing the backend-web (frontend-service) proxy.

## Overview

The backend service now serves the application directly on port 8085, eliminating the need for the separate nginx proxy (frontend-service). The internet-facing load balancer should route traffic directly to the backend service.

## Current State

- **frontend-service** (backend-web):
  - Container Port: 8085
  - Target Group: `arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-frontend-service/8751f68a26de3afe`
  - Connected to internet-facing load balancer
  - Status: ACTIVE (needs to be removed)

- **backend-service**:
  - Container Port: 9000 (needs to change to 8085)
  - Target Group: `arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-backend-service/9263ab2aecbe8559`
  - Connected to internal ALB
  - Task Definition: `backend:5`

## Required AWS Changes

### Step 1: Create New Task Definition for Backend

Create a new task definition revision with port 8085:

```bash
# Register new task definition (backend:6)
aws ecs register-task-definition \
  --family backend \
  --task-role-arn arn:aws:iam::912413319130:role/ecsTaskExecutionRole \
  --execution-role-arn arn:aws:iam::912413319130:role/ecsTaskExecutionRole \
  --network-mode awsvpc \
  --requires-compatibilities FARGATE \
  --cpu 512 \
  --memory 1024 \
  --runtime-platform cpuArchitecture=X86_64,operatingSystemFamily=LINUX \
  --container-definitions '[
    {
      "name": "backend",
      "image": "912413319130.dkr.ecr.eu-central-1.amazonaws.com/backend:latest",
      "cpu": 0,
      "memory": 256,
      "memoryReservation": 128,
      "portMappings": [
        {
          "containerPort": 8085,
          "hostPort": 8085,
          "protocol": "tcp",
          "name": "backend-8085-tcp"
        }
      ],
      "essential": true,
      "environment": [
        {"name": "REDIS_HOST", "value": "prod-redis-etrainings.hufd1f.ng.0001.euc1.cache.amazonaws.com"},
        {"name": "DB_USERNAME", "value": "admin"},
        {"name": "QUEUE_CONNECTION", "value": "redis"},
        {"name": "DB_PORT", "value": "3306"},
        {"name": "APP_USE_HTTPS", "value": "true"},
        {"name": "DB_HOST", "value": "prod-db-etrainings.coqhw9gwcg0x.eu-central-1.rds.amazonaws.com"},
        {"name": "APP_URL", "value": "https://app.jasarah-ksa.com"},
        {"name": "DB_DATABASE", "value": "etrainings"},
        {"name": "DB_PASSWORD", "value": "B1U9AlV85RdEjD9f3"}
      ],
      "logConfiguration": {
        "logDriver": "awslogs",
        "options": {
          "awslogs-group": "/ecs/backend",
          "awslogs-create-group": "true",
          "awslogs-region": "eu-central-1",
          "awslogs-stream-prefix": "ecs"
        }
      }
    }
  ]'
```

### Step 2: Find the Internet-Facing Load Balancer

```bash
# List load balancers to find the internet-facing one
aws elbv2 describe-load-balancers --region eu-central-1

# List listeners for the load balancer
aws elbv2 describe-listeners \
  --load-balancer-arn <INTERNET_FACING_ALB_ARN>
```

### Step 3: Update Target Group Health Check

The backend target group needs to be updated to check port 8085:

```bash
# Modify target group to use port 8085
aws elbv2 modify-target-group \
  --target-group-arn arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-backend-service/9263ab2aecbe8559 \
  --health-check-path /healthcheck \
  --health-check-port 8085 \
  --health-check-protocol HTTP
```

### Step 4: Update Backend Service

Update the backend service to use the new task definition and attach it to the internet-facing load balancer:

```bash
# First, get the internet-facing load balancer's target group ARN
# (This should be the current frontend-service target group)
INTERNET_TG_ARN="arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-frontend-service/8751f68a26de3afe"

# Update backend service with new task definition and load balancer
aws ecs update-service \
  --cluster prod-ecs-cluster \
  --service backend-service \
  --task-definition backend:6 \
  --load-balancers "targetGroupArn=${INTERNET_TG_ARN},containerName=backend,containerPort=8085" \
  --force-new-deployment
```

**Alternative approach:** Modify the listener rules to point to the backend target group instead of frontend target group:

```bash
# Get listener rules
aws elbv2 describe-listeners \
  --load-balancer-arn <INTERNET_FACING_ALB_ARN>

# Update listener default action to use backend target group
aws elbv2 modify-listener \
  --listener-arn <LISTENER_ARN> \
  --default-actions Type=forward,TargetGroupArn=arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-backend-service/9263ab2aecbe8559
```

### Step 5: Verify Deployment

Monitor the deployment and ensure tasks are healthy:

```bash
# Check service status
aws ecs describe-services \
  --cluster prod-ecs-cluster \
  --services backend-service

# Check task health
aws ecs list-tasks \
  --cluster prod-ecs-cluster \
  --service-name backend-service

# Check target health
aws elbv2 describe-target-health \
  --target-group-arn arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-backend-service/9263ab2aecbe8559
```

### Step 6: Scale Down Frontend Service

Once the backend service is receiving traffic successfully:

```bash
# Scale frontend service to 0
aws ecs update-service \
  --cluster prod-ecs-cluster \
  --service frontend-service \
  --desired-count 0
```

### Step 7: Delete Frontend Service (After Verification)

After confirming everything works correctly (recommended to wait 24-48 hours):

```bash
# Delete frontend service
aws ecs delete-service \
  --cluster prod-ecs-cluster \
  --service frontend-service \
  --force

# Deregister old frontend task definitions
aws ecs deregister-task-definition \
  --task-definition frontend:2

# Optionally delete the frontend target group (after removing from load balancer)
aws elbv2 delete-target-group \
  --target-group-arn arn:aws:elasticloadbalancing:eu-central-1:912413319130:targetgroup/ecs-prod-e-frontend-service/8751f68a26de3afe
```

### Step 8: Update Security Groups

Verify security group rules allow traffic on port 8085:

```bash
# Check backend security group rules
aws ec2 describe-security-groups \
  --group-ids sg-0969fc94a78aacac6 \
  --region eu-central-1

# If needed, add ingress rule for port 8085
aws ec2 authorize-security-group-ingress \
  --group-id sg-0969fc94a78aacac6 \
  --protocol tcp \
  --port 8085 \
  --source-group <LOAD_BALANCER_SECURITY_GROUP_ID> \
  --region eu-central-1
```

## Rollback Plan

If issues occur:

1. Scale backend service back down
2. Scale frontend service back up to 2
3. Revert listener rules to point to frontend target group
4. Investigate issues

## Verification Checklist

- [ ] New task definition created with port 8085
- [ ] Backend service updated with new task definition
- [ ] Load balancer routing traffic to backend service
- [ ] Health checks passing
- [ ] Application accessible via public URL
- [ ] No errors in CloudWatch logs
- [ ] Frontend service scaled to 0
- [ ] Monitored for 24-48 hours
- [ ] Frontend service deleted
- [ ] Old resources cleaned up

## Notes

- The backend service currently has 2 running tasks (desired count: 2)
- The frontend service currently has 2 running tasks (desired count: 2)
- SSL termination should remain at the load balancer level
- Ensure the backend Dockerfile and nginx configuration changes are deployed before updating the task definition

## Related Files Changed

- `backend/nginx.server.conf` - Changed port from 9000 to 8085
- `backend/Dockerfile` - Changed EXPOSE and healthcheck to use 8085
- `docker-compose.yml` - Removed backend-web service, added port mapping for backend
- `deploy-all.sh` - Removed backend-web build and deployment steps
- `backend-web/` directory - Deleted
- `deploy-backend-web.sh` - Deleted

