docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker-compose build backend-schedule
docker tag etraining_backend-schedule:latest 912413319130.dkr.ecr.eu-central-1.amazonaws.com/schedule
docker push 912413319130.dkr.ecr.eu-central-1.amazonaws.com/schedule
aws ecs update-service --cluster prod-ecs-cluster --service scheduler --force-new-deployment
