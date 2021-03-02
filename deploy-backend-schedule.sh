docker login -u AWS -p $(aws ecr get-login-password) https://$(aws sts get-caller-identity --query 'Account' --output text).dkr.ecr.eu-central-1.amazonaws.com
docker build backend-schedule
docker tag backend-schedule:latest 175380537962.dkr.ecr.eu-central-1.amazonaws.com/schedule
docker push 175380537962.dkr.ecr.eu-central-1.amazonaws.com/schedule
aws ecs update-service --cluster prod-ecs-cluster --service scheduler --force-new-deployment
