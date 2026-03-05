# ⚡ RyaanCMS — AI-Powered Laravel CMS

**Free & Open Source** · Laravel 11 · Shared Hosting Ready · Claude + DeepSeek

> Build complex applications and landing pages with a simple prompt. Runs on any shared hosting with cPanel + MySQL + PHP 8.2.

---

## ✨ Features

- 🤖 **AI Full Stack Developer** — writes Laravel code, database schemas, Blade UI, fixes bugs
- 🔑 **Bring Your Own API Key** — Claude (Anthropic) + DeepSeek V3 support
- 🌐 **Shared Hosting Ready** — works on Hostinger, Namecheap, Bluehost, cPanel
- 🚀 **One-Click FTP Deploy** — auto-deploys to your shared hosting
- 🛒 **Central Marketplace** — buy & sell apps, templates, plugins
- 🔒 **High Security** — AES-256 encrypted keys, CSRF, RBAC, 2FA
- 🔍 **SEO Built-in** — meta tags, sitemap, schema markup auto-generated
- 📊 **Analytics** — built-in page view tracking, AI usage monitoring
- 🎨 **Modern UI** — dark theme, responsive, Tailwind CSS + Alpine.js

---

## 🚀 Installation

### Requirements
- PHP 8.2+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- cPanel Shared Hosting OR VPS/local

### Step 1 — Clone & Install

```bash
git clone https://github.com/ryaancms/ryaancms.git
cd ryaancms
composer install --optimize-autoloader --no-dev
```

### Step 2 — Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_URL=https://yourdomain.com
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### Step 3 — Database Setup

```bash
php artisan migrate --seed
```

### Step 4 — Storage & Permissions

```bash
php artisan storage:link
chmod -R 755 storage bootstrap/cache
```

### Step 5 — Launch

```bash
php artisan serve
# Visit: http://localhost:8000
```

---

## 🌐 Shared Hosting (cPanel) Installation

1. Upload all files to `public_html/laravel/` (except `public/` contents)
2. Upload `public/` contents directly to `public_html/`
3. Edit `public/index.php` — update paths:
   ```php
   require __DIR__.'/../laravel/vendor/autoload.php';
   $app = require_once __DIR__.'/../laravel/bootstrap/app.php';
   ```
4. Create MySQL database in cPanel → phpMyAdmin
5. Edit `.env` with your cPanel database credentials
6. Run migrations via cPanel Terminal or a web-based artisan runner

---

## 🔑 Setting Up AI API Keys

1. Register/Login to RyaanCMS
2. Go to **Settings → API Keys**
3. Add your **Claude API Key** from [console.anthropic.com](https://console.anthropic.com)
4. Add your **DeepSeek API Key** from [platform.deepseek.com](https://platform.deepseek.com)
5. Choose routing mode: **Auto** (recommended), Claude Only, or DeepSeek Only

**Your keys are:**
- ✅ Encrypted with AES-256 before storage
- ✅ Never shared with RyaanCMS servers
- ✅ Stored only in YOUR database

---

## 🏗️ Architecture

```
ryaancms/
├── app/
│   ├── Http/Controllers/
│   │   ├── AiController.php          ← AI generation endpoint
│   │   ├── ProjectController.php     ← Project CRUD
│   │   ├── DeployController.php      ← FTP deployment
│   │   ├── MarketplaceController.php ← Marketplace
│   │   └── SettingsController.php    ← API key management
│   ├── Models/
│   │   ├── User.php                  ← Encrypted API key accessors
│   │   ├── Project.php               ← Project + FTP credentials
│   │   ├── ChatMessage.php           ← AI conversation history
│   │   ├── MarketplaceItem.php       ← Marketplace listings
│   │   └── AiUsageLog.php            ← Token usage tracking
│   └── Services/
│       ├── AiService.php             ← Claude + DeepSeek routing
│       ├── EncryptionService.php     ← AES-256 key encryption
│       └── DeployService.php         ← FTP auto-deploy
├── resources/views/
│   ├── layouts/app.blade.php         ← Main layout (Tailwind + Alpine)
│   ├── ai/builder.blade.php          ← AI builder interface
│   ├── dashboard/settings.blade.php  ← API key settings
│   └── marketplace/                  ← Marketplace views
├── routes/web.php                    ← All routes
└── database/migrations/              ← Full schema
```

---

## 🛒 Marketplace Revenue Model

- Developers sell templates/plugins at any price
- **70% to developer · 30% platform fee**
- Free items always available
- One-click install into any project

---

## 🔐 Security Features

| Feature | Implementation |
|---------|---------------|
| API Key Encryption | AES-256-CBC + HMAC verification |
| CSRF Protection | Laravel built-in, all forms |
| SQL Injection | Eloquent ORM + parameterized queries |
| XSS Prevention | Blade auto-escaping |
| Rate Limiting | Laravel throttle middleware |
| Role-Based Access | Spatie Laravel Permission |
| 2FA | TOTP (Google Authenticator) |
| Audit Logs | All sensitive actions logged |

---

## 🤝 Contributing

RyaanCMS is free and open source. Contributions welcome!

1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'Add amazing feature'`
4. Push: `git push origin feature/amazing-feature`
5. Open a Pull Request

---

## 📄 License

MIT License — Free for personal and commercial use.

---

## 💬 Community

- GitHub Issues: Bug reports & feature requests
- Marketplace: [marketplace.ryaancms.com](https://marketplace.ryaancms.com)

---

Built with ❤️ by the RyaanCMS community · Laravel 11 · PHP 8.2
