docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker-compose build

# Tagging
docker tag etraining_backend:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/backend
docker tag etraining_backend-worker:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/worker
docker tag etraining_backend-schedule:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/schedule
docker tag etraining_backend-web:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/frontend

# Pushing
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/backend
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/worker
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/schedule
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/frontend

# Updating cluster
aws ecs update-service --cluster prod-ecs-cluster --service backend-service --force-new-deployment
aws ecs update-service --cluster prod-ecs-cluster --service worker-service --force-new-deployment
aws ecs update-service --cluster prod-ecs-cluster --service scheduler --force-new-deployment
aws ecs update-service --cluster prod-ecs-cluster --service frontend-service --force-new-deployment

