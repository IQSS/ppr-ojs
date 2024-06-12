#!/bin/bash

CONTAINER_NAME=iqss-smtp-ojs

if docker ps | grep -q $CONTAINER_NAME; then
  echo "$(date -u) running"
else
  echo "$(date -u) not running"
  #docker start $CONTAINER_NAME
  docker run --rm -itd --name $CONTAINER_NAME -p 1080:1080 -p 1025:1025 --env MAILDEV_SMTP_PORT=1025 --env MAILDEV_WEB_PORT=1080 --env MAILDEV_MAIL_DIRECTORY=/mail --mount type=tmpfs,destination=/mail maildev/maildev:2.0.5
fi
