build:
	docker-compose build

up:
	docker-compose up -d

down:
	docker stop php kafka zookeeper

produce:
	docker exec php php /app/produce.php

consume:
	docker exec php php /app/consume.php