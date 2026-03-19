# Coconut Coir E-Commerce (CodeIgniter 4) Setup Guide

## Default Accounts
- Seller: `seller@coircraft.local` / `Seller123!`
- Buyer: `buyer@coircraft.local` / `Buyer123!`

## 1) Prerequisites
- PHP `8.2+`
- Composer
- MySQL/MariaDB (`5.7+` or `8+`)
- XAMPP (optional, for local Apache/MySQL)

## 2) Project Location
- `c:\Users\dwayn\Downloads\EC+HCI Project\coconutcoir-shop`

## 3) Free Local DB Setup + Port Fix (Windows)
If port `3306` is already occupied, identify and kill the process first:

```powershell
netstat -ano | findstr :3306
taskkill /PID <PID_FROM_NETSTAT> /F
```

Then:
1. Start MySQL from XAMPP or your local MySQL service.
2. Create database `coconutcoir_shop` with collation `utf8mb4_general_ci`.

## 4) Configure `.env`
From project root:
1. Copy `env` to `.env`.
2. Set:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = coconutcoir_shop
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

## 5) Install + Migrate + Seed
Run in project root:

```bash
composer install
php spark migrate
php spark db:seed InitialSeeder
```

If you previously ran old migrations and want a clean schema:

```bash
php spark migrate:refresh
php spark db:seed InitialSeeder
```

## 6) Database Features Included
The schema now includes:
- `products.sold_count`
- `products.additional_details`
- `products.release_date`
- richer `orders` breakdown fields:
  - `subtotal_amount`
  - `shipping_fee`
  - `voucher_code`
  - `voucher_type`
  - `voucher_discount_amount`
  - `shipping_address`
- `vouchers` table (seeded with):
  - `FREESHIP` (free shipping)
  - `LESS5` (5% off)
  - `LESS10` (10% off)
- `product_reviews` table (`user_id + product_id` unique)
- `storefront_settings` supports:
  - `title`
  - `description`
  - `hero_background_image`

## 7) Run Locally

```bash
php spark serve
```

Open: `http://localhost:8080/`

## 8) Feature Checklist
### Buyer
- Home page includes:
  - announcement marquee strip
  - configurable hero title/description/background image
  - featured product tiles with quick cart add
- Product cards show:
  - stock
  - sold count
  - latest review (or `No product reviews`)
- Product detail page: `/products/{id}`
- Product detail page can show seller-managed `Details` content
- Cart shows item images and split layout
- Checkout has:
  - item list + order summary in separate containers
  - address selection (auto-reuse from previous orders)
  - voucher entry + voucher type details
  - shipping fee + full breakdown
  - payment methods: COD, credit/debit card, e-wallet
- Transactions show item images and review form with 5-star picker

### Seller
- Storefront management: `/seller/storefront`
  - configurable `Title`, `Description`, `Hero Background Image URL`, and `Announcement`
- Inventory: `/seller/inventory`
  - create/edit products with `Additional Details` and `Release Date`
- Reports: `/seller/reports`

## 8.1) New Migration Reminder
If you already migrated before these latest visual/data updates, run:

```bash
php spark migrate
```

If your schema is old or inconsistent:

```bash
php spark migrate:refresh
php spark db:seed InitialSeeder
```

## 9) Free Deployment Recommendation (CI4)
### Why not Vercel (for this project)?
Vercel can run PHP with custom serverless setups, but standard CI4 apps are not a direct fit for its default framework pipeline and can be more fragile for full-session/database app behavior.

### Recommended: Railway (best fit among common free student options)
Railway runs this CI4 app as a normal PHP web process and is simpler for this architecture.

> Note: Railway free usage may be credit-based depending on current plan policies.

### Railway Steps (Detailed)

#### A) Push this project to GitHub (first time)
In project root:

```bash
git init
git add .
git commit -m "Initial Coconut Coir shop commit"
git branch -M main
git remote add origin https://github.com/<your-username>/<your-repo>.git
git push -u origin main
```

If the repo already exists locally (most common), use:

```bash
git add .
git commit -m "Update Coconut Coir shop"
git push
```

#### B) Push future code updates to GitHub
Every time you change code:

```bash
git add .
git commit -m "Describe your change"
git push
```

Railway auto-redeploys after each push to the connected branch.

#### C) Create Railway project + services
1. Login to Railway.
2. Click `New Project`.
3. Choose `Deploy from GitHub repo`.
4. Select this repository.
5. In the same Railway project, click `New` -> `Database` -> `MySQL`.

#### D) Connect app service to MySQL values
In Railway:
1. Open your MySQL service.
2. Copy connection values from its `Variables`/`Connect` tab.
3. Open your web app service -> `Variables`.
4. Add:
   - `APP_ENV=production`
   - `CI_ENVIRONMENT=production`
   - `APP_BASE_URL=https://<your-railway-domain>`
   - `DATABASE_HOST=<mysql-host>`
   - `DATABASE_NAME=<mysql-database>`
   - `DATABASE_USER=<mysql-user>`
   - `DATABASE_PASSWORD=<mysql-password>`
   - `DATABASE_PORT=<mysql-port>`

> You can use phpMyAdmin, MySQL Workbench, DBeaver, or any SQL client you prefer. Railway only needs valid MySQL connection values; the GUI tool does not matter.

#### E) Deploy app
- Railway uses `railway.toml` in this repo.
- Start command is:
  - `php -S 0.0.0.0:$PORT -t public`

#### F) Run migrations safely in Railway shell
1. Open the app service in Railway.
2. Open `Shell`.
3. If this is an existing database with manual image URL fixes, run only:

```bash
php spark migrate
```

4. Only for a brand-new/empty database, run:

```bash
php spark migrate
php spark db:seed InitialSeeder
```

> Avoid `php spark migrate:refresh` on production/existing data because it drops and recreates tables, which will remove manual product edits (including image URLs).

#### G) If Railway shows `network > healthcheck failure`
1. Confirm app variables are set on the web service:
   - `APP_ENV=production`
   - `CI_ENVIRONMENT=production`
   - `APP_BASE_URL=https://<your-railway-domain>`
   - `DATABASE_HOST`, `DATABASE_NAME`, `DATABASE_USER`, `DATABASE_PASSWORD`, `DATABASE_PORT`
2. Confirm Railway uses the command in `railway.toml`:
   - `php -S 0.0.0.0:$PORT -t public`
3. Redeploy after saving variables.
4. Check deployment logs for DB connection errors (wrong host/user/password/port are the most common reason for failing `/` healthcheck).

#### H) Verify production
1. Open app URL from Railway (`<your-railway-domain>`).
2. Login with seeded accounts.
3. Confirm:
   - products load
   - cart/checkout works
   - order placement works
   - seller pages load

## 10) Notes
- Footer disclaimer remains:
  - `For educational purposes only, and no copyright infringement is intended.`
