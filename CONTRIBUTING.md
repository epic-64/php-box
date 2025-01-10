# Local Testing and Development (Optional)
Clone the repo and navigate to the project directory.

You can try to run all the commands without Docker.
If you don't want to mess with your local PHP installation, I recommend using Docker.

Build the container
```bash
docker compose build
```

Start the container
```bash
docker compose up -d
```

Install the dependencies
```bash
docker compose exec app composer install
```

Run the tests
```bash
docker compose exec app vendor/bin/pest
```

Run PHPStan
```bash
docker compose exec app vendor/bin/phpstan analyse -v
```

Stop the container
```bash
docker compose stop
```
