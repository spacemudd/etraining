docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker-compose build backend-worker
docker tag etraining_backend-worker:latest 912413319130.dkr.ecr.eu-central-1.amazonaws.com/worker
docker push 912413319130.dkr.ecr.eu-central-1.amazonaws.com/worker
aws ecs update-service --cluster prod-ecs-cluster --service worker-service --force-new-deployment
