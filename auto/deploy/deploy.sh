#!/usr/bin/env bash
set -e
IMAGE="ghcr.io/${GITHUB_REPOSITORY_OWNER}/${GITHUB_REPOSITORY}:latest"
# Stop existing container
docker stop my_app || true
docker rm my_app || true
# Pull latest image
docker pull ${IMAGE}
# Run (adjust envs/ports as needed)
docker run -d --restart unless-stopped --name my_app -p 80:9000 ${IMAGE}
