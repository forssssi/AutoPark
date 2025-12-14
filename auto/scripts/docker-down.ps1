Write-Host "Stopping docker compose and removing containers..."
try {
    docker compose -f compose.yaml -f compose.override.yaml down
} catch {
    Write-Error "Docker is not running or compose command failed."
    exit 1
}

Write-Host "Done."
