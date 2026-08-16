### Hexlet tests and linter status:
[![Actions Status](https://github.com/g1ltz0r/ai-for-developers-project-386/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/g1ltz0r/ai-for-developers-project-386/actions)

## Demo

Приложение развёрнуто на Railway: https://<your-service>.up.railway.app/

## Deployment

Локальный запуск:

```bash
docker build -t calendar-booking .
docker run -p 8080:8080 -e PORT=8080 calendar-booking
```

Деплой на Railway:

```bash
railway login
railway create project
railway create service
railway volume create --mount-path /var/www/html/backend/database/storage
railway up
railway domain
```

После деплоя добавьте ссылку в раздел `Demo` выше.