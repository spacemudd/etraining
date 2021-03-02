docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker-compose build backend-web
docker tag etraining_backend-web:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/frontend
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/frontend
aws ecs update-service --cluster prod-ecs-cluster --service frontend-service --force-new-deployment
