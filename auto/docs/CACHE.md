Redis cache configuration
=========================

This project is configured to use Redis as the application cache.

Quick setup:

- Install and run Redis on your machine (default: `redis://127.0.0.1:6379`).
- Install PHP `redis` extension (or `predis/predis` as fallback).
- Ensure `REDIS_URL` is set in `.env` (default provided):

```dotenv
REDIS_URL=redis://127.0.0.1:6379
```

- The Symfony cache is configured in `config/packages/cache.yaml` (`app: cache.adapter.redis`).
- If you change the Redis URL, clear the cache: `php bin/console cache:clear`.

Notes:
- Symfony's Redis adapter requires the `redis` PHP extension or the `predis/predis` package.
- In CI, you can either run a Redis service or unset `REDIS_URL` to fall back to filesystem cache.
