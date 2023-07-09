#!/bin/bash

FILENAME=api.conf
SERVER_NAME=travelbuddi.com.au

if [[ "$DEPLOYMENT_GROUP_NAME" == *-dev-asg ]]
then
    # this only executes when only in development asg
    SERVER_NAME=staging.travelbuddi.com.au
fi

rm -r -f /etc/nginx/sites-available/default

cp /var/www/api/codedeploy/nginx/vhost.conf /etc/nginx/sites-available/$FILENAME

ln -sf /etc/nginx/sites-available/$FILENAME /etc/nginx/sites-enabled/$FILENAME

sed -i -e "s/{{SERVER_NAME}}/$SERVER_NAME/g" /etc/nginx/sites-available/$FILENAME

mkdir -p /var/log/nginx/production/
