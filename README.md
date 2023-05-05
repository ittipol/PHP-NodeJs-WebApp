## Start via docker compose

``` bash
docker-compose up -d --build
```

## Grant access mysql

``` bash
docker exec -it web-app-db sh

mysql -u root -p

type "1234"

ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY '1234';

flush privileges;
```

## Start Node.Js Server
``` bash
docker start web-app-nodejs
```

## Test

after start server

Open [http://localhost:8080](http://localhost:8080) with your browser to test service and application.
