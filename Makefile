ENV_FILE := app/.env
PROJECT_NAME=motivator
PORT=8000
DOCKER_IMG=${PROJECT_NAME}_img
DOCKER_CONTAINER=${PROJECT_NAME}_app
DIR_MD := $(shell grep ^PATH_TO_MD_FILES $(ENV_FILE) | cut -d '=' -f2)

# Загружаем все переменные из файла
# include $(ENV_FILE)
# export $(shell sed 's/=.*//' $(ENV_FILE))

build:
	docker build -t ${DOCKER_IMG} .docker/php

rm:
	docker stop ${DOCKER_CONTAINER}
	docker rm ${DOCKER_CONTAINER}

up:
	docker run -d \
      --name ${DOCKER_CONTAINER} \
      -v ./app:/home/app \
      -v ${DIR_MD}:/home/app/var/files \
      -v /etc/localtime:/etc/localtime:ro \
      -v /etc/timezone:/etc/timezone:ro \
      ${DOCKER_IMG}

unlock:
	sudo chown -R ${USER}:${USER} ./app
	sudo chmod 775 ./app

run: build up