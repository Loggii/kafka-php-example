# Simple dockerized php kafka example

### Start the application:

```sh
make up
```

### Produce messages (loop)
```sh
make produce
```


### Start a consumer
```sh
make consume
```

### Stop the application
```sh
make down
```

### Mange the cluster
You can use [Conductor](https://www.conduktor.io/)  or any other app/cli to manage your cluster.

Kafka: `localhost:9093`

Zookeeper `localhost:2181`

To persist data, you can set:
```dockerfile
kafka:
  ...
  volumes:
    - /path/to/kafka-persistence:/bitnami/kafka
  ...
```

see: [dockerhub](https://hub.docker.com/r/bitnami/kafka/)