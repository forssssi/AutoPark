CI/CD and deployment
=====================

This project uses GitHub Actions to run tests and build/push Docker images.

Workflows:
- `.github/workflows/ci.yml` — runs tests, builds and pushes Docker image to GHCR, optionally deploys via SSH.

Required GitHub Secrets (for pushing and deploying):
- `GITHUB_TOKEN` — automatically provided; used for login to GHCR.
- `DEPLOY_HOST` — hostname or IP of the target server (for SSH deploy).
- `DEPLOY_USER` — SSH user for the target server.
- `DEPLOY_PRIVATE_KEY` — private SSH key (without passphrase) for the user.

Deployment process overview:
1. Push to `main`. CI runs tests and builds image.
2. If `DEPLOY_HOST` secret is set, the `deploy` job executes an SSH script to pull the new image and run a container on the server.

Server setup (example):
1. Create a user with permissions to run Docker (or add to `docker` group).
2. Add SSH public key for the CI user.
3. Ensure Docker is installed and running.
4. Create a small systemd service or let the run command above be used for simple cases.

Security notes:
- On production, use a private, scoped token for registry operations if needed.
- Consider adding more robust deploy scripts, zero-downtime deploys, and a reverse proxy.
