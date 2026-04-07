# ReplyFlow — AI Support Reply Assistant

> A production-ready SaaS application that uses AI to generate customer support replies, summaries, and category classifications from incoming customer messages.

---

## Screenshots

> Dashboard, message form, and AI result views available in the `/public/screenshots` folder.

---

## Features

- **AI Reply Generation** — GPT-powered polished replies with selectable tone (professional, friendly, formal, empathetic, assertive)
- **AI Summarization** — Condenses customer issues into 1–2 sentences
- **Auto Classification** — Categorizes messages as Billing, Technical, or General
- **Queue-based Processing** — Jobs dispatched via Redis queue for non-blocking async AI calls
- **SaaS Subscription Logic** — Free (20 req/mo) and Pro (500 req/mo) plans with usage enforcement
- **REST API** — Full Sanctum-authenticated API for headless/mobile clients
- **Web Portal** — Blade + Tailwind CSS dashboard with message history and quota display
- **Fake AI Mode** — Instant mock responses for local development without any API costs

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| Language | PHP 8.2+ |
| Database | MySQL |
| Cache / Queue | Redis |
| AI Provider | OpenAI GPT-4o-mini |
| API Auth | Laravel Sanctum |
| Frontend | Blade + Tailwind CSS v4 |
| Build Tool | Vite |

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── API/          # Sanctum-protected REST endpoints
│   │   └── Web/          # Blade portal controllers
│   ├── Requests/         # Form request validation
│   ├── Resources/        # API response transformers
│   └── Middleware/       # Usage limit enforcement
├── Jobs/
│   └── ProcessMessageJob.php   # Async AI processing job
├── Services/
│   └── AIService.php           # OpenAI integration (reply, summary, classify)
├── Models/
│   ├── User.php
│   ├── Message.php
│   └── Subscription.php
```

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/register` | Register a new user |
| `POST` | `/api/login` | Login and get token |
| `POST` | `/api/messages` | Submit a message for AI processing |
| `GET` | `/api/messages` | List all messages for the authenticated user |
| `GET` | `/api/messages/{id}` | Get a single message with AI results |
| `GET` | `/api/dashboard` | Usage stats and quota info |

---

## Database Schema

**messages**
```
id, user_id, message_text, tone, ai_reply, summary, category, status, error_message, timestamps
```

**subscriptions**
```
id, user_id, plan_name, request_limit, used_requests, timestamps
```

---

## Local Setup

### Requirements
- PHP 8.2+
- MySQL
- Redis
- Node.js 18+
- Composer

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/your-username/replyflow.git
cd replyflow

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Set your database and OpenAI credentials in .env
DB_DATABASE=replyflow
DB_USERNAME=root
DB_PASSWORD=

OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini

# 5. Run migrations
php artisan migrate

# 6. Build frontend assets
npm run build

# 7. Start the application
php artisan serve
```

### Local Testing (No OpenAI Key Required)

Set the following in `.env` to use instant mock AI responses:

```env
AI_FAKE=true
QUEUE_CONNECTION=sync
```

This bypasses all OpenAI calls and processes messages instantly.

---

## Queue Worker (Production)

```bash
# Switch to Redis in .env
QUEUE_CONNECTION=redis
AI_FAKE=false

# Run the worker
php artisan queue:work --tries=3 --timeout=120
```

---

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `OPENAI_API_KEY` | Your OpenAI secret key | — |
| `OPENAI_MODEL` | Model to use | `gpt-4o-mini` |
| `OPENAI_TIMEOUT` | API request timeout (seconds) | `30` |
| `AI_FAKE` | Use mock responses (local dev) | `false` |
| `QUEUE_CONNECTION` | Queue driver (`sync` or `redis`) | `redis` |
| `CACHE_DRIVER` | Cache driver | `redis` |

---

## SaaS Plans

| Plan | Monthly Requests |
|------|-----------------|
| Free | 20 |
| Pro  | 500 |

Users are automatically enrolled on the Free plan at registration. Usage is enforced via middleware and cached in Redis.

---

## Running Tests

```bash
php artisan test
```

Feature tests cover message creation with mocked AI responses.

---

## License

MIT
