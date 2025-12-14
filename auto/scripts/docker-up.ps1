param(
    [string]$dbUrl = "postgresql://app:!ChangeMe!@database:5432/app?serverVersion=16&charset=utf8",
    [string]$redisUrl = "redis://redis:6379"
)

Write-Host "Checking Docker availability..."
try {
    docker info | Out-Null
} catch {
    Write-Error "Docker does not seem to be running. Start Docker Desktop or Docker Engine and retry."
    exit 1
}

Write-Host "Exporting env vars for compose..."
$env:DATABASE_URL = $dbUrl
$env:REDIS_URL = $redisUrl

Write-Host "Starting docker compose..."
docker compose -f compose.yaml -f compose.override.yaml up -d --build

Write-Host "Waiting for database to be available..."
Start-Sleep -Seconds 5

Write-Host "Running migrations and fixtures..."
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

Write-Host "Running tests inside container..."
docker compose exec php php bin/phpunit --testdox

Write-Host "All done. Containers status:"
docker compose ps
