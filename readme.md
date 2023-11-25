Жмяк:
```bash
docker compose up -d --build
```

потом:
```bash
docker compose exec php composer install
```

следом:
```bash
docker compose exec php bin/console d:m:m
```

ура!!!