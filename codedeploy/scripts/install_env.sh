#!/bin/bash

ENV_PARAMETER_NAME=env-prod

if [[ "$DEPLOYMENT_GROUP_NAME" == *-dev-asg ]]
then
    # this only executes when only in development asg
    ENV_PARAMETER_NAME=env-dev
fi

pwd
aws --region=ap-southeast-2 ssm get-parameter --name /$ENV_PARAMETER_NAME --with-decryption --output text --query "Parameter.Value" > /var/www/api/.env