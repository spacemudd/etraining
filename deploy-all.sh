docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker-compose build backend backend-worker backend-schedule

# Tagging
docker tag etraining_backend:latest 912413319130.dkr.ecr.eu-central-1.amazonaws.com/backend
docker tag etraining_backend-worker:latest 912413319130.dkr.ecr.eu-central-1.amazonaws.com/worker
docker tag etraining_backend-schedule:latest 912413319130.dkr.ecr.eu-central-1.amazonaws.com/schedule

# Pushing
docker push 912413319130.dkr.ecr.eu-central-1.amazonaws.com/backend
docker push 912413319130.dkr.ecr.eu-central-1.amazonaws.com/worker
docker push 912413319130.dkr.ecr.eu-central-1.amazonaws.com/schedule

# Updating cluster
aws ecs update-service --cluster prod-ecs-cluster --service backend-service --force-new-deployment
aws ecs update-service --cluster prod-ecs-cluster --service worker-service --force-new-deployment
aws ecs update-service --cluster prod-ecs-cluster --service scheduler --force-new-deployment

docker image prune --force
