Запуск проекта в Docker
=======================

Простой способ поднять сервисы локально (Postgres, Redis, PHP + Nginx):

1. Установите Docker и Docker Compose.

2. В корне проекта запустите:

```bash
docker compose up --build
```

3. Приложение будет доступно на http://localhost:8000

Комментарий:
- `docker compose` использует `compose.yaml` (в котором теперь определены `php`, `web`, `database`, `redis`).
- После первого запуска выполните миграции и загрузите фикстуры внутри контейнера `php`:

	```bash
	docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
	```

	Или используйте автоматизированный PowerShell скрипт `scripts\docker-up.ps1` (Windows + PowerShell):

	```powershell
	.\scripts\docker-up.ps1
	```

Если используете Windows и монтируете код в контейнер, убедитесь, что права на `var/` и `vendor/` корректны.
