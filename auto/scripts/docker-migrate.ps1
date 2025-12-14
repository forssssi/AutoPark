Write-Host "Running migrations and fixtures inside php container..."
try {
    docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
    docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
} catch {
    Write-Error "Migration/fixtures failed. Ensure containers are running and try again."
    exit 1
}

Write-Host "Done."
