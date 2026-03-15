# Coconut Coir E-Commerce (CodeIgniter 4) Setup Guide

# Default Accounts Created

# Seller Account:
# Email: seller@coircraft.local
# Password: Seller123!

# Buyer Account:
# Email: buyer@coircraft.local
# Password: Buyer123!

## 1) Prerequisites
- XAMPP (Apache + MySQL)
- PHP 8.1+ (XAMPP PHP is fine)
- Composer

## 2) Project Location
Project root:
- `c:\Users\dwayn\Downloads\EC+HCI Project\coconutcoir-shop`

Logo used in all pages:
- Source placeholder: `c:\Users\dwayn\Downloads\EC+HCI Project\logo.png`
- In-app logo path: `public/assets/images/logo.png`

## 3) Database Setup (phpMyAdmin via XAMPP)
1. Open XAMPP Control Panel.
2. Start `Apache` and `MySQL`.
3. Open phpMyAdmin: `http://localhost/phpmyadmin`.
4. Create a new database named `coconutcoir_shop` with collation `utf8mb4_general_ci`.

## 4) Configure Environment
From project root:
1. Copy `env` to `.env`.
2. Edit `.env` and set:

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

If your MySQL root user has a password, set it in `database.default.password`.

## 5) Install and Prepare Data
Run in project root:

```bash
composer install
php spark migrate
php spark db:seed InitialSeeder
```

This creates tables and inserts:
- Default seller and buyer accounts
- Initial coconut coir products
- Initial storefront settings

## 6) Run the App
In project root:

```bash
php spark serve
```

Open:
- `http://localhost:8080/`

## 7) Main Pages Implemented
### Buyer Side
- Home: `/`
- Login: `/login`
- Register: `/register`
- Storefront: `/storefront`
- Products: `/products`
- Cart: `/cart`
- Checkout: `/checkout`
- Transaction history: `/transactions`
- Profile: `/profile`

### Seller Side
- Seller login: `/seller/login`
- Storefront management: `/seller/storefront`
- Inventory management: `/seller/inventory`
- Reports (daily/monthly sales + inventory report): `/seller/reports`

## 8) Notes
- All pages include the group name and logo.
- All pages include footer disclaimer:
  - `For educational purposes only, and no copyright infringement is intended.`
- Theme color scheme uses navy-blue styling.
