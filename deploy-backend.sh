docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker build backend
docker tag etraining_backend:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/backend
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/backend
aws ecs update-service --cluster prod-ecs-cluster --service backend-service --force-new-deployment
