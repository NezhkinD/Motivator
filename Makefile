PROJECT_NAME=motivator
DOCKER_IMG=motivator_img
DOCKER_CONTAINER=motivator_app
PORT=8000

build:
	docker build -t ${DOCKER_IMG} .docker/php

rm:
	docker stop ${DOCKER_CONTAINER}
	docker rm ${DOCKER_CONTAINER}

up:
	docker run -d \
      --name ${DOCKER_CONTAINER} \
      -v ./app:/home/app \
      ${DOCKER_IMG}

unlock:
	sudo chown -R ${USER}:${USER} ./app
	sudo chmod 775 ./app