# RABOSA_DockerCodebase - Multi-Container Web System with Microservices

Docker setup for the IT302 Final Performance Task. Runs **two systems** in
separate containers on different ports, plus shared infrastructure:

| System                           | Port | Purpose                                  |
| -------------------------------- | ---- | ---------------------------------------- |
| System 1 - Hotel System (Main)   | 80   | Full CRUD web application                |
| System 2 - Employee Microservice | 81   | JSON API that feeds System 1's dropdowns |

## Services (9 total)

| Service    | Image / Build                | Ports                |
| ---------- | ---------------------------- | -------------------- |
| nginx      | nginx:alpine                 | 80:80, 443:443       |
| php        | build: php/Dockerfile        | (fpm :9000 internal) |
| nginx_ms   | nginx:alpine                 | 81:81                |
| php_ms     | build: php/Dockerfile        | (fpm :9000 internal) |
| mysql      | mysql:8.0                    | 3306:3306            |
| phpmyadmin | phpmyadmin/phpmyadmin:latest | 8080:80              |
| workspace  | devilbox/php-fpm:8.2-work    | -                    |
| redis      | redis:alpine                 | 6379:6379            |
| mailhog    | mailhog/mailhog:latest       | 1025, 8025           |

## Quick start

```bash
docker compose up -d --build
```

Then open:

- `http://localhost:80` - Hotel System (Main CRUD app)
- `http://localhost:81` - Employee Microservice API
- `http://localhost:8080` - phpMyAdmin
- `http://localhost:8025` - MailHog

## Databases

| Database    | Used by  | Credentials                |
| ----------- | -------- | -------------------------- |
| hotel_db    | System 1 | user `default` / `default` |
| employee_db | System 2 | user `default` / `default` |

Both databases are created and seeded automatically on the first `up`
(mysql data volume is initialized by `mysql/init/01-schema.sql`).
