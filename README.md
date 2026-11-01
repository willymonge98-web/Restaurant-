# LÉNA RESTAURANT — ERP / POS

Laravel restaurant management system (Phase 1): auth & roles, settings, users, products, tables, orders, kitchen, POS payments (PDF receipt stub), recipe→stock deduction, cashier sessions, Sanctum API.

## Stack

- PHP 8.3+, Laravel 13
- MySQL (cPanel-ready) or SQLite for local demo
- Blade + Tailwind CSS 4 + Chart.js
- Laravel Sanctum (mobile API)
- DomPDF (ticket PDF)

## Requirements

- PHP 8.3 with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `gd`, `sqlite3` or `mysql`
- Composer 2
- Node.js 20+ (asset build)
- MySQL 8+ (production / cPanel) or SQLite (quick local)

## Install (local)

```bash
git clone https://github.com/willymonge98-web/Restaurant-.git
cd Restaurant-
composer install
cp .env.example .env
php artisan key:generate

# SQLite quick start (default .env):
touch database/database.sqlite

# OR MySQL — set in .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=lena_restaurant
# DB_USERNAME=root
# DB_PASSWORD=secret

php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Open http://127.0.0.1:8000

## Default logins (seeded)

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@lena.restaurant` | `password` |
| Caissier | `caissier@lena.restaurant` | `password` |
| Serveur | `serveur@lena.restaurant` | `password` |
| Cuisinier | `cuisine@lena.restaurant` | `password` |
| Magasinier | `stock@lena.restaurant` | `password` |

**Change these immediately in production.**

## cPanel / MySQL notes

1. Create a MySQL database + user in cPanel.
2. Upload the project (or deploy via Git), point the domain/subdomain document root to `/public`.
3. Set `.env` `DB_CONNECTION=mysql` and credentials.
4. Run `composer install --no-dev -o`, `php artisan migrate --seed --force`, `php artisan storage:link`, `npm ci && npm run build` (build assets locally or via SSH).
5. Ensure `storage/` and `bootstrap/cache/` are writable.

## Sanctum API (mobile)

Base URL: `/api`

```http
POST /api/login
{ "email": "admin@lena.restaurant", "password": "password", "device_name": "android" }

GET /api/me
Authorization: Bearer {token}

GET /api/products
GET /api/orders
GET /api/orders/{id}
GET /api/kitchen
POST /api/logout
```

## Phase 1 modules

1. Web + API auth, role middleware (Admin, Gérant, Caissier, Serveur, Cuisinier, Magasinier, Comptable)
2. Restaurant settings (name, logo, address, phone, email, USD/CDF + rate, ticket footer)
3. Users CRUD + activity log basics
4. Categories & products (food/drinks)
5. Tables board: Libre / Occupée / Réservée / En nettoyage
6. Orders: dine-in / takeaway / delivery, statuses, add items to open order
7. Kitchen screen (item status, mark ready)
8. POS: cash, card, M-Pesa, Orange Money, Airtel Money, mixed/partial, discount, PDF receipt stub
9. Stock movements + recipe deduction on validated sale (cancel restores stock with reason)
10. Cash session open/close — theoretical vs counted
11. Seeders + this README

## Phase 2 (follow-ups)

Purchases/suppliers, customers/reservations, delivery module polish, expenses/accounting reports, HR, Excel/PDF export suite, full audit trails, DB backup UI, live Mobile Money operator APIs.

## License

Proprietary / project use — LÉNA RESTAURANT.
