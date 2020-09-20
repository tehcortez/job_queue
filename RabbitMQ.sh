#!/bin/bash

# Settings
CONTAINER_NAME="rabbitmq-container"
DEFAULT_USER="root"
DEFAULT_PASSWORD="myPassword"
CLUSTER_COOKIE="hello world"
HOSTNAME="rabbitmq.mydomain.com"

# Don't change below this line.
DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
docker kill $CONTAINER_NAME
docker rm $CONTAINER_NAME

docker run -d \
  --restart=always \
  --hostname $HOSTNAME \
  --name $CONTAINER_NAME \
  -p 15672:15672 \
  -p 4369:4369 \
  -p 5671:5671 \
  -p 5672:5672 \
  -p 15671:15671 \
  -p 25672:25672 \
  -e RABBITMQ_ERLANG_COOKIE="$CLUSTER_COOKIE" \
  -e RABBITMQ_DEFAULT_USER="$DEFAULT_USER" \
  -e RABBITMQ_DEFAULT_PASS="$DEFAULT_PASSWORD" \
  -v $DIR/rabbitmq-state:/var/lib/rabbitmq \
  rabbitmq:3-management
