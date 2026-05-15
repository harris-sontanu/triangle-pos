# Design Spec: Mailpit Integration

## Overview
Add Mailpit to the Docker development environment to capture and view outgoing emails locally.

## Goals
- Provide a local SMTP server for testing.
- Add a web UI to inspect captured emails.
- Persist email data across container restarts.
- Isolate services using a dedicated Docker network.

## Architecture
### Docker Services
- **mailpit**:
  - Image: `axllent/mailpit:latest`
  - Ports: `1025` (SMTP), `8025` (Web UI)
  - Volumes: `mailpit_data:/data`
  - Network: `triangle-network`
- **web**:
  - `depends_on`: added `mailpit`
  - Network: `triangle-network`
- **db**:
  - Network: `triangle-network`

### Networking
- **triangle-network**: Bridge network for service isolation and discovery.

### Volumes
- **mailpit_data**: Named volume for Mailpit data persistence.

## Configuration
- `.env` and `.env.example` already updated to:
  - `MAIL_HOST=mailpit`
  - `MAIL_PORT=1025`

## Testing & Validation
- Run `docker-compose up -d`.
- Verify `mailpit` dashboard is accessible at `http://localhost:8025`.
- Trigger a test email from the application (e.g., password reset).
- Verify email appears in Mailpit UI.
