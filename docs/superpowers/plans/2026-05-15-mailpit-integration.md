# Mailpit Integration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add Mailpit service to Docker environment for local email testing with persistence and networking.

**Architecture:** Update `docker-compose.yml` to include `mailpit`, define a bridge network `triangle-network`, and add a volume `mailpit_data`.

**Tech Stack:** Docker, Docker Compose, Mailpit.

---

### Task 1: Update Docker Compose Infrastructure

**Files:**
- Modify: `docker-compose.yml`

- [ ] **Step 1: Modify `docker-compose.yml` to add networks, volumes, and mailpit service**

```yaml
services:
  web:
    image: triangle-pos:latest
    build:
      context: .
    env_file:
      - .env
    ports:
      - "8000:8000"
    volumes:
      - .:/app
    depends_on:
      - db
      - mailpit
    networks:
      - triangle-network

  db:
    platform: "linux/amd64"
    image: mysql:latest
    env_file:
      - .env
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    ports:
      - "3306:3306"
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - triangle-network

  mailpit:
    image: 'axllent/mailpit:latest'
    container_name: triangle-mailpit
    ports:
      - "1025:1025"
      - "8025:8025"
    volumes:
      - mailpit_data:/data
    networks:
      - triangle-network

networks:
  triangle-network:
    driver: bridge

volumes:
  dbdata:
  mailpit_data:
```

- [ ] **Step 2: Validate Docker Compose file syntax**

Run: `docker-compose config`
Expected: Valid YAML output representing the full configuration.

- [ ] **Step 3: Commit**

```bash
git add docker-compose.yml
git commit -m "feat(docker): add mailpit service and custom network"
```

---

### Task 2: Verify Connectivity and UI

- [ ] **Step 1: Restart containers**

Run: `docker-compose down && docker-compose up -d`
Expected: Containers `web`, `db`, and `mailpit` start successfully.

- [ ] **Step 2: Verify Mailpit Web UI accessibility**

Check: `curl -I http://localhost:8025`
Expected: `HTTP/1.1 200 OK`

- [ ] **Step 3: Verify SMTP port accessibility**

Run: `nc -zv localhost 1025`
Expected: `Connection to localhost port 1025 [tcp/smtp] succeeded!`
