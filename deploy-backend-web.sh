docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
aws ecr get-login-password --region eu-central-1 | docker login --username AWS --password-stdin 912413319130.dkr.ecr.eu-central-1.amazonaws.com
docker-compose build backend-web
docker tag etraining-backend-web:latest 912413319130.dkr.ecr.eu-central-1.amazonaws.com/frontend:latest
docker push 912413319130.dkr.ecr.eu-central-1.amazonaws.com/frontend
aws ecs update-service --cluster prod-ecs-cluster --service frontend-service --force-new-deployment
