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

## 9) Default Workflow (XAMPP + phpMyAdmin + `php spark serve`)
Use this as the official default setup for development and demos.

1. Start Apache and MySQL in XAMPP.
2. In phpMyAdmin, create database `coconutcoir_shop` with collation `utf8mb4_general_ci`.
3. In project root, ensure `.env` has local DB values:

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

4. Install dependencies and build schema/data:

```bash
composer install
php spark migrate
php spark db:seed InitialSeeder
```

5. Run app:

```bash
php spark serve
```

6. Open `http://localhost:8080/`.

## 10) Fastest Free Deployment Option (Recommended)
### Recommended: Railway + Railway MySQL + MySQL Workbench Import
Why this is the best fit for this project now:
- standard PHP app deployment from GitHub
- managed MySQL in the same project
- import `.sql` from local phpMyAdmin using MySQL Workbench
- quick setup for school projects

### A) Push project to GitHub
If your repo is already connected:

```bash
git add .
git commit -m "Update setup and deployment"
git push
```

If this is first push:

```bash
git init
git add .
git commit -m "Initial Coconut Coir shop commit"
git branch -M main
git remote add origin https://github.com/<your-username>/<your-repo>.git
git push -u origin main
```

### B) Create Railway services
1. Log in to Railway.
2. Click `New Project`.
3. Click `Deploy from GitHub repo` and select this repository.
4. In the same project, click `New` -> `Database` -> `MySQL`.
5. Wait until both services are marked healthy.

### C) Set app variables on Railway web service
Open the web service -> `Variables` and add:

```ini
APP_ENV=production
CI_ENVIRONMENT=production
APP_BASE_URL=https://<your-railway-domain>
DATABASE_HOST=${{MySQL.MYSQLHOST}}
DATABASE_NAME=${{MySQL.MYSQLDATABASE}}
DATABASE_USER=${{MySQL.MYSQLUSER}}
DATABASE_PASSWORD=${{MySQL.MYSQLPASSWORD}}
DATABASE_PORT=${{MySQL.MYSQLPORT}}
```

Important:
- `APP_ENV` and `CI_ENVIRONMENT` use literal value `production`.
- `DATABASE_*` use references so they stay in sync with the MySQL service.

### D) Export `.sql` from local phpMyAdmin
1. Open local phpMyAdmin (XAMPP).
2. Select database `coconutcoir_shop`.
3. Click `Export`.
4. Choose `Quick` + `SQL`.
5. Download file, for example: `coconutcoir_shop.sql`.

### E) Import `.sql` into Railway MySQL using MySQL Workbench
#### 1) Get Railway public connection values
Open Railway MySQL service -> `Connect` tab -> `Public Network` and copy:
- host (example: `autorack.proxy.rlwy.net`)
- port (example: `50798`)
- username (usually `root`)
- password
- database name (usually `railway`)

You can also copy from MySQL service `Variables`:
- host: `MYSQL_PUBLIC_URL` (parse host/port) or use Connect tab directly
- user: `MYSQLUSER`
- password: `MYSQLPASSWORD`
- database: `MYSQLDATABASE`

#### 2) Create connection in MySQL Workbench
Open MySQL Workbench -> click `+` next to `MySQL Connections`.

Fill fields exactly:
- `Connection Name`: `Railway MySQL`
- `Connection Method`: `Standard (TCP/IP)`
- `Hostname`: `<railway-public-host>`
- `Port`: `<railway-public-port>`
- `Username`: `<railway-mysql-user>`
- `Password`: click `Store in Vault...` and paste password
- `Default Schema`: `<railway-database-name>`

Click `Test Connection`.
If success, click `OK` to save.

#### 3) Run SQL import
1. Open the saved `Railway MySQL` connection.
2. Go to `Server` -> `Data Import`.
3. Choose `Import from Self-Contained File` and select `coconutcoir_shop.sql`.
4. Under `Default Target Schema`, choose Railway DB (usually `railway`).
5. Click `Start Import`.
6. Refresh `Schemas` and verify tables exist.

Connection mapping summary:
- `Hostname` -> Railway `Connect` host (`autorack.proxy...`)
- `Port` -> Railway `Connect` port (`xxxxx`)
- `Username` -> Railway `MYSQLUSER`
- `Password` -> Railway `MYSQLPASSWORD`
- `Default Schema` -> Railway `MYSQLDATABASE`

### F) Deploy and verify app
1. Trigger deploy (or push new commit).
2. Open deployment logs and ensure no DB connection errors.
3. Open app URL from Railway.
4. Login using seeded accounts.
5. Verify product listing, cart, checkout, seller pages.

### G) Troubleshooting
1. App cannot connect to DB:
   - Confirm DB variables are on the web service (not only MySQL service).
   - Confirm `DATABASE_*` variables are set to `${{MySQL....}}` references.
2. Workbench cannot connect:
   - Use `Public Network` host/port, not `mysql.railway.internal`.
   - Re-check password and username.
3. Imported tables not seen by app:
   - Ensure Workbench imported into schema matching `DATABASE_NAME`.
4. App deployed but blank/errors:
   - Confirm `APP_BASE_URL` is exact Railway domain with `https://`.

## 11) Notes
- Footer disclaimer remains:
  - `For educational purposes only, and no copyright infringement is intended.`
